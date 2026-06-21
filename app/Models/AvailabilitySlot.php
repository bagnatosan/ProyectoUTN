<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailabilitySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'weekday',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function scopeForDay($query, string $day)
    {
        return $query->where('weekday', $day);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('businessProfile', fn ($q) => $q->where('user_id', $userId));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
