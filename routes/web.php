<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\IngredientController;
use App\Models\Ingredient;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\AvailabilitySlotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DashboardController;

// Existing Registration Select & Form Routes
Route::get('/', [RegistrationController::class, 'select'])->name('register.select');

Route::get('/register/client', [RegistrationController::class, 'createClient'])->name('register.client');
Route::post('/register/client', [RegistrationController::class, 'storeClient'])->name('register.client.store');

Route::get('/register/seller', [RegistrationController::class, 'createSeller'])->name('register.seller');
Route::post('/register/seller', [RegistrationController::class, 'storeSeller'])->name('register.seller.store');

// Existing Login Routes
Route::get('/login', [RegistrationController::class, 'showLogin'])->name('login');
Route::post('/login', [RegistrationController::class, 'login'])->name('login.store');

// Existing Authenticated Dashboard Route
Route::get('/dashboard', [RegistrationController::class, 'dashboard'])->name('dashboard');

// Existing Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('register.select');
})->name('logout');

// ==========================================
// NUEVAS RUTAS SEGÚN EL PLAN DE DESARROLLO
// ==========================================

Route::middleware(['auth'])->group(function () {

    // --- Programador 1: Gestión de Perfil de Negocio (BusinessProfile) ---
    Route::get('/profile/edit', [BusinessProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [BusinessProfileController::class, 'update'])->name('profile.update');

    // --- Santiago Bagnato: Catálogo de Productos y Categorías ---
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'create'])->name('categories.create');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::put('/categories/{category}' , [CategoryController::class , 'update'])->name('categories.update');

    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/change-statement', [ProductController::class, 'ChangeStatement'])->name('products.change-statement'); //toggle button

    // --- Programador 3: Inventario de Ingredientes y Constructor de Recetas ---
    Route::resource('ingredients', IngredientController::class);
    Route::get('/products/{product}/recipe/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/products/{product}/recipe/update', [RecipeController::class, 'update'])->name('recipes.update');
    
   
    // --- Programador 4: Disponibilidad y Reservas (Vendedor/Cliente logueado) ---
    Route::get('/availability/edit', [AvailabilitySlotController::class, 'edit'])->name('availability.edit');
    Route::put('/availability/update', [AvailabilitySlotController::class, 'update'])->name('availability.update');
    Route::get('/my-reservations', [ReservationController::class, 'clientHistory'])->name('reservations.client_history');

    // --- Programador 5: Métricas y Dashboard Analítico ---
    Route::get('/dashboard/metrics', [DashboardController::class, 'index'])->name('dashboard.metrics');

});
Route::get('/costos', function () {
    // 1. Buscamos los ingredientes de la base de datos
    try {
        $ingredients = \App\Models\Ingredient::all();
    } catch (\Exception $e) {
        $ingredients = collect();
    }

    // 2. Buscamos el CSS de tu amigo para mantener el modo oscuro global
    $cssPath = resource_path('css/app.css');
    $estilosAmigo = '';
    if (Illuminate\Support\Facades\File::exists($cssPath)) {
        $estilosAmigo = Illuminate\Support\Facades\File::get($cssPath);
    }

    // 3. Renderizamos la vista 'costos.blade.php' pasándole las variables
    return view('costos', compact('ingredients', 'estilosAmigo'));
});

 

// --- Rutas Públicas (Programador 2 y 4) ---
Route::get('/catalog/{id}', [PublicCatalogController::class, 'show'])->name('catalog.show');
Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations/store', [ReservationController::class, 'store'])->name('reservations.store');

