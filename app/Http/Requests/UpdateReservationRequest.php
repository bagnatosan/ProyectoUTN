<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reservation = $this->route('reservation');
        $sellerId = $reservation->product->businessProfile->user_id;

        return [
            'product_id' => [
                'required',
                'exists:products,id',
                function ($attribute, $value, $fail) use ($sellerId) {
                    $product = Product::where('is_active', true)
                        ->whereHas('businessProfile', fn ($q) => $q->where('user_id', $sellerId))
                        ->find($value);
                    if (!$product) {
                        $fail('El producto seleccionado no es válido o no pertenece al mismo vendedor.');
                    }
                },
            ],
            'reservation_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . now()->addDays(2)->format('Y-m-d'),
            ],
            'reservation_time' => [
                'required',
                'date_format:H:i',
            ],
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reservation = $this->route('reservation');

            if ($reservation->status !== 'pending') {
                $validator->errors()->add('reservation', 'Solo se pueden modificar reservas pendientes.');
                return;
            }

            $minDate = now()->addDays(2)->format('Y-m-d');
            if ($reservation->reservation_date->format('Y-m-d') < $minDate) {
                $validator->errors()->add('reservation', 'No podés modificar una reserva con menos de 2 días de anticipación.');
                return;
            }

            $sellerId = $reservation->product->businessProfile->user_id;
            $availabilityService = app(AvailabilityService::class);
            $isAvailable = $availabilityService->isSlotAvailable(
                $sellerId,
                $this->reservation_date,
                $this->reservation_time,
                $reservation->id,
            );

            if (!$isAvailable) {
                $validator->errors()->add('reservation_time', 'El horario seleccionado ya no está disponible.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Debés seleccionar un producto.',
            'product_id.exists' => 'El producto seleccionado no existe.',
            'reservation_date.required' => 'Debés seleccionar una fecha.',
            'reservation_date.date_format' => 'Formato de fecha inválido.',
            'reservation_date.after_or_equal' => 'La fecha debe ser al menos 2 días después de hoy.',
            'reservation_time.required' => 'Debés seleccionar un horario.',
            'reservation_time.date_format' => 'Formato de horario inválido.',
            'notes.max' => 'Las notas no pueden superar los 1000 caracteres.',
        ];
    }
}
