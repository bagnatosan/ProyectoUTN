<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
            'password' => Hash::make($validated['password']),
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
    public function storeSeller(Request $request)
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
            'logo' => 'required|url',
            'address' => 'nullable|string|max:255',
        ]);

        // Wrap in transaction to ensure consistency and return user
        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'seller',
            ]);

            BusinessProfile::create([
                'user_id' => $user->id,
                'business_name' => $validated['business_name'],
                'description' => $validated['description'],
                'phone' => $validated['phone'],
                'logo' => $validated['logo'],
                'address' => $validated['address'] ?? null,
            ]);

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
            return redirect()->route('dashboard');
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

            return redirect()->intended(route('dashboard'))->with('success', '¡Inicio de sesión exitoso!');
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
        return view('dashboard');
    }
}
