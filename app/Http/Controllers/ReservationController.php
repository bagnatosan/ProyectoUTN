<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Show the public form for creating a new reservation.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('reservations.create', compact('products'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        // Store reservation logic will go here
        return redirect()->back()->with('success', 'Reserva solicitada con éxito (borrador).');
    }

    /**
     * Display a listing of client's own reservations.
     */
    public function clientHistory()
    {
        $reservations = Auth::user()->reservations ?? [];
        return view('reservations.client_history', compact('reservations'));
    }
}
