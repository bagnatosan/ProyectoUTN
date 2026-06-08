<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    private const STATUSES = [
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmada',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada',
    ];

    /**
     * Display the seller dashboard (metrics, orders with search and filter).
     */
    public function index(Request $request)
    {
        $businessProfile = $this->sellerBusinessProfile();
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(array_keys(self::STATUSES))],
            'today_status' => ['nullable', Rule::in(array_keys(self::STATUSES))],
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $baseReservations = $this->sellerReservationsQuery($businessProfile);
        $metrics = $this->buildMetrics($businessProfile);
        $todayReservations = $this->todayReservations($businessProfile, $filters['today_status'] ?? null);
        $reservations = $this->applyFilters($baseReservations->with(['product.category', 'user']), $filters)
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.index', [
            'businessProfile' => $businessProfile,
            'filters' => $filters,
            'metrics' => $metrics,
            'todayReservations' => $todayReservations,
            'reservations' => $reservations,
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateReservationStatus(Request $request, Reservation $reservation)
    {
        $businessProfile = $this->sellerBusinessProfile();

        abort_if(
            $reservation->product?->business_profile_id !== $businessProfile->id,
            403
        );

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
        ]);

        $reservation->update(['status' => $validated['status']]);

        return redirect()
            ->route('dashboard.metrics', $request->query())
            ->with('success', 'Estado de reserva actualizado.');
    }

    private function sellerBusinessProfile(): BusinessProfile
    {
        $user = Auth::user();

        abort_if(! $user || $user->role !== 'seller' || ! $user->businessProfile, 403);

        return $user->businessProfile;
    }

    private function sellerReservationsQuery(BusinessProfile $businessProfile)
    {
        return Reservation::query()
            ->whereHas('product', function ($query) use ($businessProfile) {
                $query->where('business_profile_id', $businessProfile->id);
            });
    }

    private function applyFilters($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('client_name', 'like', "%{$search}%")
                        ->orWhere('client_email', 'like', "%{$search}%")
                        ->orWhere('client_phone', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('reservation_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('reservation_date', '<=', $date));
    }

    private function todayReservations(BusinessProfile $businessProfile, ?string $status)
    {
        return $this->sellerReservationsQuery($businessProfile)
            ->with(['product.category', 'user'])
            ->whereDate('reservation_date', today())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('reservation_time')
            ->get();
    }

    private function buildMetrics(BusinessProfile $businessProfile): array
    {
        $reservations = $this->sellerReservationsQuery($businessProfile)
            ->with('product')
            ->get();
        $products = Product::where('business_profile_id', $businessProfile->id)
            ->withCount('ingredients')
            ->get();

        $completedThisMonth = $reservations->filter(function (Reservation $reservation) {
            return $reservation->status === 'completed'
                && $reservation->reservation_date->betweenIncluded(now()->startOfMonth(), now()->endOfMonth());
        });

        $monthlyRevenue = $completedThisMonth->sum(fn (Reservation $reservation) => (float) $reservation->product?->price);
        $monthlyCost = $completedThisMonth->sum(fn (Reservation $reservation) => (float) ($reservation->product?->estimated_cost ?? 0));
        $monthlyProfit = $monthlyRevenue - $monthlyCost;

        $statusCounts = collect(self::STATUSES)
            ->mapWithKeys(fn ($label, $status) => [
                $status => $reservations->where('status', $status)->count(),
            ])
            ->all();

        $productProfitability = Product::where('business_profile_id', $businessProfile->id)
            ->with(['category', 'reservations' => fn ($query) => $query->where('status', 'completed')])
            ->get()
            ->map(function (Product $product) {
                $completedCount = $product->reservations->count();
                $revenue = $completedCount * (float) $product->price;
                $cost = $completedCount * (float) ($product->estimated_cost ?? 0);
                $profit = $revenue - $cost;

                return [
                    'product' => $product,
                    'completed_count' => $completedCount,
                    'revenue' => $revenue,
                    'cost' => $cost,
                    'profit' => $profit,
                    'margin' => $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('profit')
            ->values();
        $productsWithoutRecipe = $products
            ->filter(fn (Product $product) => $product->ingredients_count === 0)
            ->values();
        $productsWithoutCost = $products
            ->filter(fn (Product $product) => $product->estimated_cost === null || (float) $product->estimated_cost <= 0)
            ->values();
        $lowMarginProducts = $products
            ->filter(function (Product $product) {
                $price = (float) $product->price;
                $cost = (float) ($product->estimated_cost ?? 0);

                return $price > 0 && $cost > 0 && (($price - $cost) / $price) * 100 < 30;
            })
            ->values();
        $dataQualityIssueCount = $productsWithoutRecipe->count()
            + $productsWithoutCost->count()
            + $lowMarginProducts->count();
        $healthScore = max(0, 100 - ($productsWithoutRecipe->count() * 20) - ($productsWithoutCost->count() * 20) - ($lowMarginProducts->count() * 10));

        return [
            'monthly_revenue' => round($monthlyRevenue, 2),
            'monthly_profit' => round($monthlyProfit, 2),
            'monthly_cost' => round($monthlyCost, 2),
            'completed_this_month' => $completedThisMonth->count(),
            'active_reservations' => $reservations->whereIn('status', ['pending', 'confirmed'])->count(),
            'today_reservations' => $reservations->filter(fn (Reservation $reservation) => $reservation->reservation_date->isToday())->count(),
            'average_ticket' => $completedThisMonth->count() > 0 ? round($monthlyRevenue / $completedThisMonth->count(), 2) : 0,
            'status_counts' => $statusCounts,
            'product_profitability' => $productProfitability,
            'data_quality' => [
                'health_score' => $healthScore,
                'issue_count' => $dataQualityIssueCount,
                'total_products' => $products->count(),
                'products_without_recipe' => $productsWithoutRecipe,
                'products_without_cost' => $productsWithoutCost,
                'low_margin_products' => $lowMarginProducts,
            ],
        ];
    }
}
