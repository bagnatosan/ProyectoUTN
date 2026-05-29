<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RegistrationController;

// Registration Select & Form Routes
Route::get('/', [RegistrationController::class, 'select'])->name('register.select');

Route::get('/register/client', [RegistrationController::class, 'createClient'])->name('register.client');
Route::post('/register/client', [RegistrationController::class, 'storeClient'])->name('register.client.store');

Route::get('/register/seller', [RegistrationController::class, 'createSeller'])->name('register.seller');
Route::post('/register/seller', [RegistrationController::class, 'storeSeller'])->name('register.seller.store');

// Login Routes
Route::get('/login', [RegistrationController::class, 'showLogin'])->name('login');
Route::post('/login', [RegistrationController::class, 'login'])->name('login.store');

// Authenticated Dashboard Route
Route::get('/dashboard', [RegistrationController::class, 'dashboard'])->name('dashboard');

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('register.select');
})->name('logout');
