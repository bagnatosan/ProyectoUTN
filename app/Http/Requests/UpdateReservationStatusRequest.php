<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required'             => 'El estado es obligatorio.',
            'status.in'                   => 'Estado inválido (pending, confirmed, completed, cancelled).',
            'cancellation_reason.required_if' => 'Debes indicar el motivo de la cancelación.',
            'cancellation_reason.max'     => 'El motivo no puede exceder 1000 caracteres.',
        ];
    }
}
