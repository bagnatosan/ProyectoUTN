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
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\EntrepreneurContactController;

// Existing Registration Select & Form Routes
Route::get('/', [RegistrationController::class, 'select'])->name('register.select');

Route::get('/register', [RegistrationController::class, 'registerHub'])->name('register.hub');

Route::get('/register/client', [RegistrationController::class, 'createClient'])->name('register.client');
Route::post('/register/client', [RegistrationController::class, 'storeClient'])->name('register.client.store');

Route::get('/register/seller', [RegistrationController::class, 'createSeller'])->name('register.seller');
Route::post('/register/seller', [RegistrationController::class, 'storeSeller'])->name('register.seller.store');

Route::post('/contacto/emprendedores', [EntrepreneurContactController::class, 'store'])->name('entrepreneur.contact.store');

// Existing Login Routes
Route::get('/login', [RegistrationController::class, 'showLogin'])->name('login');
Route::post('/login', [RegistrationController::class, 'login'])->name('login.store');



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

Route::get('/dashboard', [RegistrationController::class, 'dashboard'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // --- Programador 1: Gestión de Perfil de Negocio (BusinessProfile) ---
    Route::middleware(['seller'])->group(function () {
        Route::get('/profile/edit', [BusinessProfileController::class, 'edit'])->name('business_profile.edit');
        Route::put('/profile/update', [BusinessProfileController::class, 'update'])->name('business_profile.update');
        Route::put('/profile/password', [BusinessProfileController::class, 'updatePassword'])->name('business_profile.password');
    });

    // --- Programador 2: Catálogo de Productos y Categorías ---
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'create'])->name('categories.create');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/change-statement', [ProductController::class, 'ChangeStatement'])->name('products.change-statement'); //toggle button

    // --- Programador 3: Inventario de Ingredientes y Constructor de Recetas ---
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{product}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::post('/recipes/{recipe}/add-ingredient', [RecipeController::class, 'addIngredient'])->name('recipes.add-ingredient');
    Route::delete('/recipes/{recipe}/remove-ingredient/{ingredient}', [RecipeController::class, 'removeIngredient'])->name('recipes.remove-ingredient');
    Route::get('/ingredients/{ingredient}/valid-units', [RecipeController::class, 'validUnits'])->name('ingredients.valid-units');

    Route::resource('ingredients', IngredientController::class);

    // --- Programador 4: Disponibilidad (Vendedor) ---
    Route::middleware(['seller'])->group(function () {
        Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
        Route::put('/availability', [AvailabilityController::class, 'update'])->name('availability.update');
    });

    // --- Programador 4: Reservas (Cliente logueado) ---
    Route::middleware(['client'])->group(function () {
        Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    });

    // --- Notificaciones ---
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    })->name('notifications.mark-all-read');

    // --- Programador 5: Gestión de Pedidos (Vendedor) ---
    Route::get('/reservations/manage', [ReservationController::class, 'manage'])->name('reservations.manage');
    Route::get('/reservations/manage/data', [ReservationController::class, 'getReservations'])->name('reservations.manage.data');
    Route::get('/reservations/manage/export', [ReservationController::class, 'exportCsv'])->name('reservations.export');
    Route::get('/reservations/pending-count', [ReservationController::class, 'pendingCount'])->name('reservations.pending-count');
    Route::get('/reservations/{reservation}/detail', [ReservationController::class, 'show'])->name('reservations.detail');
    Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');
    Route::patch('/reservations/{reservation}/seller-notes', [ReservationController::class, 'updateSellerNotes'])->name('reservations.seller-notes');

});

// --- Rutas Públicas (Programador 2 y 4) ---
Route::get('/mapa', [MapController::class, 'index'])->name('map.index');
Route::get('/mapa/emprendimientos', [MapController::class, 'markers'])->name('map.markers');
Route::post('/mapa/geocodificar', [MapController::class, 'geocode'])->name('map.geocode');
Route::get('/catalog/{id}', [PublicCatalogController::class, 'show'])->name('catalog.show');
Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/available-slots/{seller}/{date}', [AvailabilityController::class, 'availableSlots'])->name('availability.slots');

// --- Administrador ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/products/{product}', [App\Http\Controllers\AdminController::class, 'deleteProduct'])->name('admin.products.delete');
});