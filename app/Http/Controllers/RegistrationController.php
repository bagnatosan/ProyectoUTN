<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Reservation;
use App\Services\GeocodingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    /**
     * Show the role selection page.
     */
    public function select()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('register.select');
    }

    /**
     * Show the registration hub (client vs entrepreneur).
     */
    public function registerHub()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('register.hub');
    }

    /**
     * Show the client registration form.
     */
    public function createClient()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('register.client');
    }

    /**
     * Store a new client user.
     */
    public function storeClient(Request $request, GeocodingService $geocodingService)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            // Campos de dirección
            'street'        => 'required|string|max:255',
            'street_number' => 'required|string|max:20',
            'floor'         => 'nullable|string|max:20',
            'apartment'     => 'nullable|string|max:20',
            'province'      => 'required|string|max:100',
            'locality'      => 'required|string|max:100',
            'postal_code'   => 'required|string|max:10',
        ]);

        // Componer la dirección completa para geocoding y compatibilidad
        $address = $this->composeAddress($validated);

        $user = DB::transaction(function () use ($validated, $address, $geocodingService) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'],
                'role'     => 'client',
            ]);

            $profile = \App\Models\ClientProfile::create([
                'user_id'       => $user->id,
                'address'       => $address,
                'street'        => $validated['street'],
                'street_number' => $validated['street_number'],
                'floor'         => $validated['floor'] ?? null,
                'apartment'     => $validated['apartment'] ?? null,
                'province'      => $validated['province'],
                'locality'      => $validated['locality'],
                'postal_code'   => $validated['postal_code'],
            ]);

            $geocodingService->syncProfileCoordinates($profile, $address, null, null, true);
            $profile->save();

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', '¡Registro de cliente completado con éxito! Bienvenido, ' . $user->name);
    }

    /**
     * Show the seller (entrepreneur) registration form.
     */
    public function createSeller()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('register.seller');
    }

    /**
     * Store a new seller user and their business profile.
     */
    public function storeSeller(Request $request, GeocodingService $geocodingService)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $request->merge([
            'phone' => $request->has('phone') ? trim($request->phone) : null,
        ]);

        $validated = $request->validate([
            // Datos de usuario
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            // Datos del negocio
            'business_name' => 'required|string|max:255',
            'description'   => 'required|string',
            'phone'         => ['required', 'string', 'max:50', 'regex:/^\+54\d+$/'],
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // Campos de dirección (obligatorios para ubicar el comercio en el mapa)
            'street'        => 'required|string|max:255',
            'street_number' => 'required|string|max:20',
            'floor'         => 'nullable|string|max:20',
            'apartment'     => 'nullable|string|max:20',
            'province'      => 'required|string|max:100',
            'locality'      => 'required|string|max:100',
            'postal_code'   => 'required|string|max:10',
        ], [
            'phone.regex' => 'El teléfono debe comenzar con +54 y no debe contener espacios.',
        ]);

        // Componer la dirección completa para geocoding
        $address = $this->composeAddress($validated);

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('logos', 'r2')
            : null;

        $user = DB::transaction(function () use ($validated, $address, $geocodingService, $logoPath) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'],
                'role'     => 'seller',
            ]);

            $profile = BusinessProfile::create([
                'user_id'       => $user->id,
                'business_name' => $validated['business_name'],
                'description'   => $validated['description'],
                'phone'         => $validated['phone'],
                'logo'          => $logoPath,
                'address'       => $address,
                'street'        => $validated['street'] ?? null,
                'street_number' => $validated['street_number'] ?? null,
                'floor'         => $validated['floor'] ?? null,
                'apartment'     => $validated['apartment'] ?? null,
                'province'      => $validated['province'] ?? null,
                'locality'      => $validated['locality'] ?? null,
                'postal_code'   => $validated['postal_code'] ?? null,
            ]);

            $geocodingService->syncProfileCoordinates($profile, $address, null, null, true);
            $profile->save();

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', '¡Registro de emprendedor y negocio completado con éxito!');
    }

    /**
     * Compone la dirección completa a partir de los campos individuales.
     * Resultado: "Av. Rivadavia 742, Piso 3, Dpto A, San Justo, Buenos Aires (1754)"
     */
    private function composeAddress(array $data): string
    {
        $parts = [trim($data['street']) . ' ' . trim($data['street_number'])];

        if (!empty($data['floor'])) {
            $parts[] = 'Piso ' . trim($data['floor']);
        }
        if (!empty($data['apartment'])) {
            $parts[] = 'Dpto ' . trim($data['apartment']);
        }
        if (!empty($data['locality'])) {
            $parts[] = trim($data['locality']);
        }
        if (!empty($data['province'])) {
            $parts[] = trim($data['province']);
        }

        $address = implode(', ', $parts);

        if (!empty($data['postal_code'])) {
            $address .= ' (' . trim($data['postal_code']) . ')';
        }

        return $address;
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')
                ->with('info', 'Ya tenés una sesión activa. Cerrá sesión si querés ingresar con otra cuenta.');
        }
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     */
    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user     = Auth::user();
            $redirect = $user->role === 'admin'
                ? route('admin.dashboard')
                : route('dashboard');

            return redirect()->intended($redirect)->with('success', '¡Inicio de sesión exitoso!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Show the dashboard page.
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('register.select');
        }

        $user = Auth::user();

        if ($user->role === 'client') {
            $businesses = BusinessProfile::all();
            $upcomingReservations = Reservation::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('reservation_date', '>=', now()->format('Y-m-d'))
                ->with('product.businessProfile')
                ->orderBy('reservation_date')
                ->orderBy('reservation_time')
                ->take(5)
                ->get();
            return view('dashboard', compact('businesses', 'upcomingReservations'));
        }

        $reservationsToday    = collect();
        $reservationsTomorrow = collect();
        $stats                = [];

        if ($user->role === 'seller' && $user->businessProfile) {
            $bpId      = $user->businessProfile->id;
            $today     = Carbon::today()->format('Y-m-d');
            $tomorrow  = Carbon::tomorrow()->format('Y-m-d');

            $reservationsToday = Reservation::with('product')
                ->forBusiness($bpId)
                ->where('reservation_date', $today)
                ->orderBy('reservation_time')
                ->get();

            $reservationsTomorrow = Reservation::with('product')
                ->forBusiness($bpId)
                ->where('reservation_date', $tomorrow)
                ->orderBy('reservation_time')
                ->get();

            $stats = [
                'pending'   => Reservation::forBusiness($bpId)->pending()->count(),
                'confirmed' => Reservation::forBusiness($bpId)->confirmed()->count(),
                'today'     => Reservation::forBusiness($bpId)->where('reservation_date', $today)->count(),
                'overdue'   => Reservation::forBusiness($bpId)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('reservation_date', '<', $today)
                    ->count(),
            ];
        }

        return view('dashboard', compact('reservationsToday', 'reservationsTomorrow', 'stats'));
    }
}
