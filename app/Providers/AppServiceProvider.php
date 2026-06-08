<?php

namespace App\Providers;

use App\Models\Ingredient;
use App\Models\ProductIngredient;
use App\Observers\IngredientObserver;
use App\Observers\ProductIngredientObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ProductIngredient::observe(ProductIngredientObserver::class);
        Ingredient::observe(IngredientObserver::class);
    }
}
