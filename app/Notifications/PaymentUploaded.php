<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentUploaded extends Notification
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
        $productName = $this->reservation->product?->name ?? 'Producto';
        $clientName  = $this->reservation->client_name;

        return [
            'reservation_id' => $this->reservation->id,
            'product_name'   => $productName,
            'client_name'    => $clientName,
            'message'        => "{$clientName} subió el comprobante de transferencia para la reserva de \"{$productName}\".",
        ];
    }
}
