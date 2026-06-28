<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $productName   = $this->reservation->product?->name ?? 'Producto';
        $businessName  = $this->reservation->product?->businessProfile?->business_name ?? 'El emprendedor';

        return [
            'reservation_id' => $this->reservation->id,
            'product_name'   => $productName,
            'message'        => "{$businessName} confirmó el pago de tu reserva de \"{$productName}\". ¡Tu turno está asegurado!",
        ];
    }
}
