<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::check()) {
            // Redirige al usuario autenticado fuera del login
            return redirect()->route('ventas.index'); // Cambia por tu ruta protegida principal
        }

        return $next($request);
    }
}
