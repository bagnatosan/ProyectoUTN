<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCompleted extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('Reserva Completada - ' . $productName)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line($businessName . ' marcó como completada tu reserva de **' . $productName . '** del **' . $date . '**.')
            ->action('Ver mis reservas', url('/my-reservations'))
            ->line('¡Gracias por tu compra!');
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
            'message'        => $businessName . ' marcó como completada tu reserva de "' . $productName . '". ¡Gracias!',
        ];
    }
}
