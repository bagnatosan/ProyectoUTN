<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'client_name',
        'client_email',
        'client_phone',
        'quantity',
        'reservation_date',
        'reservation_time',
        'notes',
        'status',
        'cancellation_reason',
        'cancelled_by',
        'seller_notes',
        'payment_status',
        'transfer_amount',
        'transfer_date',
        'transfer_reference',
        'receipt_path',
        'payment_confirmed_at',
    ];

    protected $casts = [
        'reservation_date' => 'date:Y-m-d',
        'completed_at' => 'datetime',
        'transfer_date' => 'date:Y-m-d',
        'payment_confirmed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by')->withDefault();
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
        return $query->whereNot('status', 'cancelled');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('reservation_date', $date);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForClient($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeForBusiness($query, int $businessProfileId)
    {
        return $query->whereHas('product', fn ($q) => $q->where('business_profile_id', $businessProfileId));
    }

    public function scopeToday($query)
    {
        return $query->where('reservation_date', now()->format('Y-m-d'));
    }

    public function scopeForWeek($query, ?string $startDate = null)
    {
        $start = $startDate ?: now()->startOfWeek()->format('Y-m-d');
        $end = now()->parse($start)->endOfWeek()->format('Y-m-d');

        return $query->whereBetween('reservation_date', [$start, $end]);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isAwaitingPayment(): bool
    {
        return $this->payment_status === 'pending_upload';
    }

    public function hasReceiptUploaded(): bool
    {
        return $this->payment_status === 'uploaded';
    }

    public function isPaymentConfirmed(): bool
    {
        return $this->payment_status === 'confirmed';
    }
}
