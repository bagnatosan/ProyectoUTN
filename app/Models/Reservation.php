<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'client_name',
        'client_email',
        'client_phone',
        'reservation_date',
        'reservation_time',
        'notes',
        'status',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'reservation_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeNotCancelled($query)
    {
        return $query->whereNotIn('status', ['cancelled']);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('reservation_date', $date);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForBusiness($query, int $businessProfileId)
    {
        return $query->whereHas('product', fn ($q) => $q->where('business_profile_id', $businessProfileId));
    }

    public function scopeForUserReservations($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
