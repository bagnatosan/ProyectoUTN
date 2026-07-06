<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $productName  = $this->reservation->product?->name ?? 'Producto';
        $businessName = $this->reservation->product?->businessProfile?->business_name ?? 'El emprendedor';
        $date         = $this->reservation->reservation_date->format('d/m/Y');
        $time         = substr($this->reservation->reservation_time, 0, 5);

        return (new MailMessage)
            ->subject('Reserva Confirmada - ' . $productName)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line($businessName . ' confirmó tu reserva de **' . $productName . '**.')
            ->line('Fecha: **' . $date . '** a las **' . $time . '**')
            ->action('Ver mis reservas', url('/my-reservations'))
            ->line('¡Nos vemos pronto!');
    }

    public function toArray(object $notifiable): array
    {
        $productName  = $this->reservation->product?->name ?? 'Producto';
        $businessName = $this->reservation->product?->businessProfile?->business_name ?? 'El emprendedor';

        return [
            'reservation_id' => $this->reservation->id,
            'product_name'   => $productName,
            'date'           => $this->reservation->reservation_date->format('Y-m-d'),
            'time'           => substr($this->reservation->reservation_time, 0, 5),
            'message'        => $businessName . ' confirmó tu reserva de "' . $productName . '".',
        ];
    }
}
