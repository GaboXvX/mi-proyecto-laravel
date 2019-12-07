<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Incidencia;
use Symfony\Component\HttpFoundation\Response;

class CheckIncidenciaAtendida
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener el slug de la incidencia de la ruta
        $slug = $request->route('slug');
        
        // Buscar la incidencia
        $incidencia = Incidencia::where('slug', $slug)->first();
        
        if (!$incidencia) {
            abort(404, 'Incidencia no encontrada');
        }
        
        // Verificar si la incidencia ya está atendida (asumiendo que hay un estado "Resuelta")
        if ($incidencia->estadoIncidencia && $incidencia->estadoIncidencia->nombre == 'atendido') {
            return redirect()->back();
               
        }
        
        return $next($request);
    }
}