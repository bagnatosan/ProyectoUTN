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
            // Usuarios
            'total_users'          => User::count(),
            'total_clients'        => User::where('role', 'client')->count(),
            'total_sellers'        => User::where('role', 'seller')->count(),
            'sellers_no_products'  => User::where('role', 'seller')
                ->whereHas('businessProfile', function ($q) {
                    $q->whereDoesntHave('products');
                })->count(),
            // Productos
            'total_products'       => Product::count(),
            'active_products'      => Product::where('is_active', true)->count(),
            'inactive_products'    => Product::where('is_active', false)->count(),
            // Reservas
            'total_reservations'   => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];

        // Actividad reciente (últimos 5 de cada uno)
        $recentUsers    = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentProducts = Product::with('businessProfile')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Listas completas para las tablas expandibles
        $users    = User::orderBy('created_at', 'desc')->get();
        $products = Product::with('businessProfile')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentUsers', 'recentProducts', 'users', 'products'
        ));
    }

    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'No podés eliminar un administrador.');
        }
        $user->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Producto eliminado correctamente.');
    }
}
