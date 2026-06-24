<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'seller', 'client']);
    }

    public function viewAnyClient(User $user): bool
    {
        return $user->role === 'client';
    }

    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'client') {
            return $reservation->user_id === $user->id;
        }

        if ($user->role === 'seller') {
            $businessProfile = $user->businessProfile;
            return $businessProfile && $reservation->product
                && $reservation->product->business_profile_id === $businessProfile->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Reservation $reservation): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'seller') {
            $businessProfile = $user->businessProfile;
            return $businessProfile && $reservation->product
                && $reservation->product->business_profile_id === $businessProfile->id;
        }

        return false;
    }

    public function modify(User $user, Reservation $reservation): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'client') {
            return $reservation->user_id === $user->id
                && $reservation->status === 'pending';
        }

        return false;
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'client') {
            return $reservation->user_id === $user->id
                && $reservation->status === 'pending';
        }

        if ($user->role === 'seller') {
            $businessProfile = $user->businessProfile;
            return $businessProfile && $reservation->product
                && $reservation->product->business_profile_id === $businessProfile->id;
        }

        return false;
    }
}
