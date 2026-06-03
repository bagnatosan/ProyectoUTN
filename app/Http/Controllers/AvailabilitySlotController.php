<?php

namespace App\Http\Controllers;

use App\Models\AvailabilitySlot;
use Illuminate\Http\Request;

class AvailabilitySlotController extends Controller
{
    /**
     * Show the form for editing the availability slots.
     */
    public function edit()
    {
        return view('availability.edit');
    }

    /**
     * Update the availability slots.
     */
    public function update(Request $request)
    {
        // Update availability slots logic will go here
        return redirect()->back()->with('success', 'Disponibilidad horaria guardada (borrador).');
    }
}
