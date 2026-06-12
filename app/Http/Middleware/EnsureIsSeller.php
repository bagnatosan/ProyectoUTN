<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'seller') {
            return redirect()->route('login')
                             ->with('error', 'Acceso restringido a vendedores.');
        }

        return $next($request);
    }
}