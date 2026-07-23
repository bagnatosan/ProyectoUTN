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

    /**
     * Suspende o reactiva un usuario. A diferencia de eliminar, esto no
     * borra ningún dato: solo le bloquea el login mientras esté suspendido.
     */
    public function toggleSuspendUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'No podés suspender a un administrador.');
        }

        if ($user->isSuspended()) {
            $user->suspended_at = null;
            $message = 'Usuario reactivado correctamente.';
        } else {
            $user->suspended_at = now();
            $message = 'Usuario suspendido correctamente.';
        }

        $user->save();

        return back()->with('success', $message);
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Activa o desactiva un producto directamente desde el panel de admin,
     * sin necesidad de borrarlo.
     */
    public function toggleProductStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        $message = $product->is_active
            ? 'Producto activado correctamente.'
            : 'Producto desactivado correctamente.';

        return back()->with('success', $message);
    }

    /**
     * Muestra el detalle completo de un usuario: su perfil (negocio o
     * cliente), y un resumen de su actividad en la plataforma.
     */
    public function showUser(User $user)
    {
        $user->load('businessProfile', 'clientProfile');

        $productsCount     = 0;
        $reservationsCount = 0;
        $recentProducts    = collect();
        $recentReservations = collect();

        if ($user->role === 'seller' && $user->businessProfile) {
            $bpId = $user->businessProfile->id;

            $productsCount = Product::where('business_profile_id', $bpId)->count();
            $reservationsCount = Reservation::forBusiness($bpId)->count();

            $recentProducts = Product::where('business_profile_id', $bpId)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $recentReservations = Reservation::with('product')
                ->forBusiness($bpId)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } elseif ($user->role === 'client') {
            $reservationsCount = Reservation::where('user_id', $user->id)->count();

            $recentReservations = Reservation::with('product.businessProfile')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('admin.user_detail', compact(
            'user', 'productsCount', 'reservationsCount', 'recentProducts', 'recentReservations'
        ));
    }
}
