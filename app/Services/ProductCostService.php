<?php

namespace App\Services;

use App\Models\Product;

class ProductCostService
{
    public function calculate(Product $product): array
    {
        $product->unsetRelation('ingredients');
        $product->load('ingredients');

        $estimatedCost = $product->ingredients->sum(function ($ingredient) {
            return (float) $ingredient->pivot->quantity * (float) $ingredient->unit_cost;
        });

        $estimatedCost = round($estimatedCost, 2);

        return [
            'estimated_cost' => $estimatedCost,
            'suggested_price' => round($estimatedCost * 3, 2),
        ];
    }

    public function update(Product $product): void
    {
        $product->updateQuietly($this->calculate($product));
    }
}
