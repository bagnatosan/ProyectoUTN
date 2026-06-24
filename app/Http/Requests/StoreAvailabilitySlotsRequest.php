<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilitySlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'seller';
    }

    public function rules(): array
    {
        return [
            'slots'                => 'required|array|min:1',
            'slots.*.day_of_week'  => 'required|integer|between:0,6',
            'slots.*.start_time'   => 'required|date_format:H:i',
            'slots.*.end_time'     => 'required|date_format:H:i|after:slots.*.start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'slots.required'                 => 'Debes agregar al menos un horario de atención.',
            'slots.*.day_of_week.required'   => 'El día de la semana es obligatorio.',
            'slots.*.day_of_week.between'    => 'El día debe estar entre 0 (domingo) y 6 (sábado).',
            'slots.*.start_time.required'    => 'La hora de inicio es obligatoria.',
            'slots.*.start_time.date_format' => 'Formato de hora inválido (HH:MM).',
            'slots.*.end_time.required'      => 'La hora de fin es obligatoria.',
            'slots.*.end_time.date_format'   => 'Formato de hora inválido (HH:MM).',
            'slots.*.end_time.after'         => 'La hora de fin debe ser posterior a la de inicio.',
        ];
    }
}
