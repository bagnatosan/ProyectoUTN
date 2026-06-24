<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsClient
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'client') {
            return redirect()->route('login')
                             ->with('error', 'Acceso restringido a clientes.');
        }

        return $next($request);
    }
}
