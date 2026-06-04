<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_profile_id',
        'category_id',
        'name',
        'description',
        'image',
        'price',
        'estimated_cost',
        'suggested_price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'suggested_price' => 'decimal:2',
    ];

    

    // Relaciones
    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}