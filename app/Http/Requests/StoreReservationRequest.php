<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('client_phone')) {
            $this->merge([
                'client_phone' => trim($this->client_phone),
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'product_id'       => 'required|integer|exists:products,id',
            'quantity'         => 'required|integer|min:1|max:50',
            'delivery_type'    => 'required|in:delivery,pickup',
            'shipping_address' => 'required_if:delivery_type,delivery|nullable|string|max:255',
            'client_name'      => 'required|string|max:255',
            'client_email'     => 'required|email|max:255',
            'client_phone'     => [
                'nullable',
                'string',
                'max:50',
                'regex:/^\+54\d+$/'
            ],
            'reservation_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'notes'            => 'nullable|string|max:1000',
        ];

        if ($this->reservation_date === Carbon::today()->format('Y-m-d')) {
            $rules['reservation_time'] = 'required|date_format:H:i|after:' . Carbon::now()->format('H:i');
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'product_id.required'              => 'Debes seleccionar un producto.',
            'product_id.exists'                => 'El producto seleccionado no existe.',
            'quantity.required'                => 'La cantidad es obligatoria.',
            'quantity.integer'                 => 'La cantidad debe ser un número entero.',
            'quantity.min'                     => 'La cantidad mínima es 1.',
            'quantity.max'                     => 'La cantidad máxima es 50.',
            'delivery_type.required'           => 'Seleccioná si querés envío o retiro en local.',
            'delivery_type.in'                 => 'La modalidad de entrega no es válida.',
            'shipping_address.required_if'     => 'Ingresá la dirección de envío.',
            'client_name.required'             => 'El nombre es obligatorio.',
            'client_email.required'            => 'El correo electrónico es obligatorio.',
            'client_email.email'               => 'El correo electrónico no es válido.',
            'client_phone.regex'               => 'El teléfono debe comenzar con +54 y no debe contener espacios.',
            'reservation_date.required'        => 'La fecha es obligatoria.',
            'reservation_date.date_format'     => 'Formato de fecha inválido.',
            'reservation_date.after_or_equal'  => 'La fecha no puede ser anterior a hoy.',
            'reservation_time.required'        => 'La hora es obligatoria.',
            'reservation_time.date_format'     => 'Formato de hora inválido (HH:MM).',
            'reservation_time.after'           => 'La hora debe ser posterior a la actual.',
            'notes.max'                        => 'Las notas no pueden exceder 1000 caracteres.',
        ];
    }
}
