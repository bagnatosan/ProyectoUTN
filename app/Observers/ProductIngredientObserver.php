<?php

namespace App\Observers;

use App\Models\ProductIngredient;
use App\Services\ProductCostService;

class ProductIngredientObserver
{
    public function saved(ProductIngredient $productIngredient): void
    {
        $this->updateProductCost($productIngredient);
    }

    public function deleted(ProductIngredient $productIngredient): void
    {
        $this->updateProductCost($productIngredient);
    }

    private function updateProductCost(ProductIngredient $productIngredient): void
    {
        if ($productIngredient->product) {
            app(ProductCostService::class)->update($productIngredient->product);
        }
    }
}
