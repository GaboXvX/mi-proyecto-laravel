<?php
namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
<<<<<<< HEAD
    public function index()
    {
        $notificaciones = Auth::user()->notificaciones()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('notificaciones.notificaciones', compact('notificaciones'));
    }
=======
    // En tu controlador de notificaciones
public function index()
{
    // Obtener todas las notificaciones importantes para mostrar a todos
    $notificaciones = Notificacion::where('mostrar_a_todos', true)
        ->orWhere('id_usuario', auth()->id())
        ->latest()
        ->paginate(10);

    return view('notificaciones.notificaciones', compact('notificaciones'));
}
>>>>>>> e822bfd70272d7eb9ea0ea59d3021ff6f6771c31

    public function marcarComoLeida($id)
    {
        $notificacion = Auth::user()->notificaciones()
            ->findOrFail($id);
            
        $notificacion->update(['leido' => true]);
        
        return $this->redirigirSegunTipo($notificacion);
    }

    public function marcarTodasComoLeidas()
    {
        Auth::user()->notificaciones()
            ->where('leido', false)
            ->update(['leido' => true]);
            
        return back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    public function getContadorNoLeidas()
    {
        $count = Auth::user()->notificacionesNoLeidas()->count();
        return response()->json(['count' => $count]);
    }

    protected function redirigirSegunTipo($notificacion)
    {
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