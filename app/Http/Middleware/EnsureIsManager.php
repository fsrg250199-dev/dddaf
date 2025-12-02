<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureIsManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Versión explícita con Auth facade (equivalente a tu RoleMiddleware)
        if (!Auth::check()) {
            return redirect()->route('manager.login'); // Redirige al login de gerente
        }

        if (Auth::user()->role !== 'manager') {
            abort(403, 'Acceso reservado para gerentes');
        }

        return $next($request);
    }
}
