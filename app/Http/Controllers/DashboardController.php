<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard de gestión de pedidos del vendedor.
     *
     * Renderiza la vista principal; los datos se cargan vía Fetch
     * desde el frontend llamando a /dashboard/reservations.
     */
    public function index(Request $request)
    {
        return view('dashboard.index');
    }
}
