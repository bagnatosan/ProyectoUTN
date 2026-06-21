<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public string $cancelledBy,
        public ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $productName = $this->reservation->product?->name ?? 'Producto';
        $date = $this->reservation->reservation_date->format('d/m/Y');
        $time = substr($this->reservation->reservation_time, 0, 5);

        return (new MailMessage)
            ->subject('Reserva Cancelada - ' . $productName)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Tu reserva de **' . $productName . '** para el **' . $date . '** a las **' . $time . '** ha sido cancelada.')
            ->line('Cancelada por: ' . ($this->cancelledBy === 'client' ? 'Cliente' : 'Vendedor'))
            ->when($this->reason, fn ($msg) => $msg->line('Motivo: ' . $this->reason))
            ->action('Ver reservas', url('/my-reservations'))
            ->line('Si tenés dudas, contactanos.');
    }

    public function toArray(object $notifiable): array
    {
        $productName = $this->reservation->product?->name ?? 'Producto';

        return [
            'reservation_id' => $this->reservation->id,
            'product_name'   => $productName,
            'date'           => $this->reservation->reservation_date->format('Y-m-d'),
            'time'           => substr($this->reservation->reservation_time, 0, 5),
            'cancelled_by'   => $this->cancelledBy,
            'reason'         => $this->reason,
            'message'        => 'Reserva de "' . $productName . '" cancelada.',
        ];
    }
}
