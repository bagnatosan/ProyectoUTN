<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_profile_id',
    ];

    // Relaciones
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}