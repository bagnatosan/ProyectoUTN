<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'description',
        'phone',
        'logo',
        'address',
        'latitude',
        'longitude',
        'profit_margin',
        'bank_cbu',
        'bank_alias',
        'bank_name',
        'bank_account_holder',
        'shipping_cost',
        'street',
        'street_number',
        'floor',
        'apartment',
        'province',
        'locality',
        'postal_code',
        'mp_public_key',
        'mp_access_token',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}