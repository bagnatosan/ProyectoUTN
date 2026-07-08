<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

   protected $fillable = [
    'business_profile_id',
    'name',
    'unit_measure',
    'unit_cost',
    'supplier_notes',
    'stock',
    'stock_minimo',
];

    
    protected $casts = [
        'unit_cost'    => 'decimal:2',
        'stock'        => 'decimal:2',
        'stock_minimo' => 'decimal:2',
    ];


    // Relaciones
    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}