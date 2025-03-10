<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPreguntasCompletadas
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (Auth::check()) {
            $user = Auth::user();

            // Si las preguntas no han sido completadas, redirigir a la página de preguntas de seguridad
            if (!$user->preguntas_completadas) {
                return redirect()->route('seguridad.create');
            }
        }

        return $next($request);
    }
}

