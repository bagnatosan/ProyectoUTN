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

    public function rules(): array
    {
        $rules = [
            'product_id'       => 'required|integer|exists:products,id',
            'client_name'      => 'required|string|max:255',
            'client_email'     => 'required|email|max:255',
            'client_phone'     => 'nullable|string|max:50',
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
            'product_id.required'            => 'Debes seleccionar un producto.',
            'product_id.exists'              => 'El producto seleccionado no existe.',
            'client_name.required'           => 'El nombre es obligatorio.',
            'client_email.required'          => 'El correo electrónico es obligatorio.',
            'client_email.email'             => 'El correo electrónico no es válido.',
            'reservation_date.required'      => 'La fecha es obligatoria.',
            'reservation_date.date_format'   => 'Formato de fecha inválido.',
            'reservation_date.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'reservation_time.required'      => 'La hora es obligatoria.',
            'reservation_time.date_format'   => 'Formato de hora inválido (HH:MM).',
            'reservation_time.after'         => 'La hora debe ser posterior a la actual.',
            'notes.max'                      => 'Las notas no pueden exceder 1000 caracteres.',
        ];
    }
}
