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
    ];

    protected $casts = [
        'reservation_date' => 'date',
    ];

    // Relaciones
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}