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
        return view('register.select');
    }

    /**
     * Show the registration hub (client vs entrepreneur).
     */
    public function registerHub()
    {
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
    public function storeClient(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'client',
        ]);

        // Auto-login the registered user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '¡Registro de cliente completado con éxito! Bienvenido, ' . $user->name);
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

        $validated = $request->validate([
            // User validations
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // Business profile validations
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'address' => 'nullable|string|max:255',
        ]);

        // El logo se sube ANTES de la transacción, así si falla el guardado en DB
        // no nos quedamos con un archivo huérfano en el storage.
        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('logos', 'public')
            : null;

        $user = DB::transaction(function () use ($validated, $geocodingService, $logoPath) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'seller',
            ]);

            $profile = BusinessProfile::create([
                'user_id' => $user->id,
                'business_name' => $validated['business_name'],
                'description' => $validated['description'],
                'phone' => $validated['phone'],
                'logo' => $logoPath,
                'address' => $validated['address'] ?? null,
            ]);

            if (!blank($profile->address)) {
                $geocodingService->syncProfileCoordinates($profile, $profile->address, null, null, true);
                $profile->save();
            }

            return $user;
        });

        // Auto-login the registered user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '¡Registro de emprendedor y negocio completado con éxito!');
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')->with('info', 'Ya tenés una sesión activa. Cerrá sesión si querés ingresar con otra cuenta.');
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
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
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

        $reservationsToday = collect();
        $reservationsTomorrow = collect();
        $stats = [];

        if ($user->role === 'seller' && $user->businessProfile) {
            $bpId = $user->businessProfile->id;
            $today = Carbon::today()->format('Y-m-d');
            $tomorrow = Carbon::tomorrow()->format('Y-m-d');

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
                'pending'    => Reservation::forBusiness($bpId)->pending()->count(),
                'confirmed'  => Reservation::forBusiness($bpId)->confirmed()->count(),
                'today'      => Reservation::forBusiness($bpId)->where('reservation_date', $today)->count(),
                'overdue'    => Reservation::forBusiness($bpId)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('reservation_date', '<', $today)
                    ->count(),
            ];
        }

        return view('dashboard', compact('reservationsToday', 'reservationsTomorrow', 'stats'));
    }
}
