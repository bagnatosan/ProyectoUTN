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
    
    Route::get('/products/{product}/recipe/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/products/{product}/recipe/update', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}/remove-ingredient/{ingredient}', [App\Http\Controllers\RecipeController::class, 'removeIngredient']);
    Route::post('/recipes/{recipe}/add-ingredient', [App\Http\Controllers\RecipeController::class, 'addIngredient']);
    
    
   
    // --- Programador 4: Disponibilidad y Reservas (Vendedor/Cliente logueado) ---
    Route::get('/availability/edit', [AvailabilitySlotController::class, 'edit'])->name('availability.edit');
    Route::put('/availability/update', [AvailabilitySlotController::class, 'update'])->name('availability.update');
    Route::get('/my-reservations', [ReservationController::class, 'clientHistory'])->name('reservations.client_history');
    Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');

    // --- Programador 5: Métricas y Dashboard Analítico ---
    Route::get('/dashboard/metrics', [DashboardController::class, 'index'])->name('dashboard.metrics');

});
Route::resource('ingredients', \App\Http\Controllers\IngredientController::class);
//Route::get('/recipes/{product}/edit', [\App\Http\Controllers\RecipeController::class, 'edit'])->name('recipes.edit');
//Route::put('/recipes/{product}', [\App\Http\Controllers\RecipeController::class, 'update'])->name('recipes.update');

// BYPASS TEMPORAL PARA EL MÓDULO DE FACU (SÁCALO ANTES DEL MERGE FINAL)
Route::get('/recipes/{product}/edit', function($product) {
    // Forzamos el login del usuario 1 en la sesión local para que no te rebote nunca
    if (!auth()->check()) {
        auth()->loginUsingId(1); 
    }
    return app(\App\Http\Controllers\RecipeController::class)->edit($product);
})->name('recipes.edit');

Route::put('/recipes/{product}', function(\Illuminate\Http\Request $request, $product) {
    if (!auth()->check()) {
        auth()->loginUsingId(1);
    }
    return app(\App\Http\Controllers\RecipeController::class)->update($request, $product);
})->name('recipes.update');

 

// --- Rutas Públicas (Programador 2 y 4) ---
Route::get('/catalog/{id}', [PublicCatalogController::class, 'show'])->name('catalog.show');
Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/availability/slots', [AvailabilitySlotController::class, 'getAvailableSlots'])->name('availability.slots');

