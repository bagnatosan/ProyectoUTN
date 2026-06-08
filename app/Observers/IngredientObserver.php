<?php

namespace App\Observers;

use App\Models\Ingredient;
use App\Services\ProductCostService;

class IngredientObserver
{
    public function updated(Ingredient $ingredient): void
    {
        if (! $ingredient->wasChanged('unit_cost')) {
            return;
        }

        $ingredient->products()->each(function ($product) {
            app(ProductCostService::class)->update($product);
        });
    }
}
