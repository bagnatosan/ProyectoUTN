<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;

Route::get('/probando', function () {
    return view('inicio');

Route::resource('ingredients', IngredientController::class);
// Ruta para guardar/actualizar la receta de un producto
Route::post('/products/{product}/recipe', [RecipeController::class, 'updateRecipe'])->name('products.recipe.update');
// Ruta para borrar/limpiar la receta completa de un producto
Route::delete('/products/{product}/recipe', [RecipeController::class, 'clearRecipe'])->name('products.recipe.clear');
});