<?php
namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Auth::user()->notificaciones()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('notificaciones.notificaciones', compact('notificaciones'));
    }


public function marcarComoLeida($id)
{
    // Actualizar el pivot en lugar del modelo Notificacion
    Auth::user()->notificaciones()->updateExistingPivot($id, [
        'leido' => true,
        'fecha_leido' => now()
    ]);
    
    $notificacion = Notificacion::findOrFail($id);
    return $this->redirigirSegunTipo($notificacion);
}

public function marcarTodasComoLeidas()
{
    // Actualizar todas las relaciones pivote
    Auth::user()->notificaciones()->wherePivot('leido', false)->update([
        'leido' => true,
        'fecha_leido' => now()
    ]);
    
    return back()->with('success', 'Todas las notificaciones marcadas como leídas');
}

public function getContadorNoLeidas()
{
    $count = Auth::user()->notificaciones()->wherePivot('leido', false)->count();
    return response()->json(['count' => $count]);
}

protected function redirigirSegunTipo($notificacion)
{
    if (request()->wantsJson()) {
        switch($notificacion->tipo_notificacion) {
            case 'nueva_incidencia':
                return response()->json(['redirect' => route('incidencias.show', $notificacion->id_incidencia)]);
                
            case 'nueva_persona':
            case 'nuevo_lider':
                return response()->json(['redirect' => route('personas.show', $notificacion->id_persona)]);
                
            default:
                return response()->json(['success' => true]);
        }
    }
    
    // Para solicitudes normales (no AJAX)
    switch($notificacion->tipo_notificacion) {
        case 'nueva_incidencia':
            return redirect()->route('incidencias.show', $notificacion->id_incidencia);
            
        case 'nueva_persona':
        case 'nuevo_lider':
            return redirect()->route('personas.show', $notificacion->id_persona);
            
        default:
            return back();
    }
}
}