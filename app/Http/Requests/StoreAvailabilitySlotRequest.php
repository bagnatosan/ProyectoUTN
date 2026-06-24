<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilitySlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_of_week' => 'required|integer|between:0,6',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'day_of_week.required'  => 'El día de la semana es obligatorio.',
            'day_of_week.between'   => 'El día debe estar entre 0 (domingo) y 6 (sábado).',
            'start_time.required'   => 'La hora de inicio es obligatoria.',
            'start_time.date_format' => 'Formato de hora inválido (HH:MM).',
            'end_time.required'     => 'La hora de fin es obligatoria.',
            'end_time.date_format'  => 'Formato de hora inválido (HH:MM).',
            'end_time.after'        => 'La hora de fin debe ser posterior a la de inicio.',
        ];
    }
}
