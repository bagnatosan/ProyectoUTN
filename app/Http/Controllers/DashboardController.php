<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the seller dashboard (metrics, orders with search and filter).
     */
    public function index(Request $request)
    {
        // Calculate metrics, fetch and filter reservations
        return view('dashboard.index');
    }
}
