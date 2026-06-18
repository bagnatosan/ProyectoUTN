<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_clients'  => User::where('role', 'client')->count(),
            'total_sellers'  => User::where('role', 'seller')->count(),
            'total_products' => Product::count(),
            'total_reservations' => Reservation::count(),
        ];

        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('stats', 'users'));
    }

    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'No podés eliminar un administrador.');
        }

        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}