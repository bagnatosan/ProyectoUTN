<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'business_name', 'description', 'phone', 'logo', 'address'])]
class BusinessProfile extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the business profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
