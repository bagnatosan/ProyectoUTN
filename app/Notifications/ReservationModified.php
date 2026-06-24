<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationModified extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public array $changes,
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

        $lines = [];
        foreach ($this->changes as $field => $change) {
            $labels = [
                'product_id' => 'Producto',
                'reservation_date' => 'Fecha',
                'reservation_time' => 'Horario',
                'notes' => 'Notas',
            ];
            $label = $labels[$field] ?? $field;
            $lines[] = "• {$label}: de \"{$change['old']}\" a \"{$change['new']}\"";
        }

        return (new MailMessage)
            ->subject('Reserva Modificada - ' . $productName)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El cliente ha modificado su reserva de **' . $productName . '**.')
            ->line('Nuevos datos: **' . $date . '** a las **' . $time . '**')
            ->line('Cambios realizados:')
            ->line(implode("\n", $lines))
            ->action('Ver reserva', url('/reservations/' . $this->reservation->id . '/detail'))
            ->line('Revisá los cambios desde el panel de gestión.');
    }

    public function toArray(object $notifiable): array
    {
        $productName = $this->reservation->product?->name ?? 'Producto';

        return [
            'reservation_id' => $this->reservation->id,
            'product_name'   => $productName,
            'date'           => $this->reservation->reservation_date->format('Y-m-d'),
            'time'           => substr($this->reservation->reservation_time, 0, 5),
            'changes'        => $this->changes,
            'message'        => 'Reserva de "' . $productName . '" modificada por el cliente.',
        ];
    }
}
