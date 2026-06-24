<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reservation;

class SellerReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');

        if (!$reservation || !$reservation instanceof Reservation) {
            return false;
        }

        $user = $this->user();
        $businessProfile = $user->businessProfile;

        if (!$businessProfile) {
            return false;
        }

        return $reservation->product
            && $reservation->product->business_profile_id === $businessProfile->id;
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
            'status.required'                  => 'El estado es obligatorio.',
            'status.in'                        => 'Estado inválido.',
            'cancellation_reason.required_if'  => 'Debes indicar el motivo de la cancelación.',
            'cancellation_reason.max'          => 'El motivo no puede exceder 1000 caracteres.',
        ];
    }
}
