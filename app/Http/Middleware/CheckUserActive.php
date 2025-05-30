<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->id_estado_usuario == 2) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Su usuario ha sido desactivado.');
        }
        return $next($request);
    }
}
