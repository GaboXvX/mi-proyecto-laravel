<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // Verificar el rol del usuario
            $user = Auth::user();
            // Si es administrador, redirigir al homeAdmin
            if ($user->role == 'admin') {
                return redirect()->route('homeAdmin');
            }

            // Si es registrador, redirigir al home
            
            $user = Auth::user();

           
            return redirect()->route('home');
        }

        return $next($request);
    }
}
