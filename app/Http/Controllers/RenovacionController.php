<?php
namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RenovacionController extends Controller
{
    public function mostrarFormulario()
    {
        return view('recuperacion.FormularioRecuperacionPt');
    }

    public function procesarFormulario(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            'correo' => 'required|email|exists:users,email'
        ], [
            'correo.exists' => 'No encontramos una solicitud con este correo.'
        ]);

        if ($validacion->fails()) {
            return redirect()->back()
                ->withErrors($validacion)
                ->withInput();
        }

        $usuario = User::where('email', $request->correo)->first();

        if (!$usuario->puedeRenovar()) {
            $mensaje = $usuario->id_estado_usuario != 4 
                ? 'Tu solicitud no ha sido rechazada.'
                : 'Has excedido el límite de renovaciones (3 intentos).';
            
            return redirect()->back()
                ->with('error', $mensaje)
                ->withInput();
        }

        // Cambiar estado a "No verificado" (3)
        $usuario->id_estado_usuario = 3;
        $usuario->incrementarIntentosRenovacion();
        $usuario->save();

        // Crear notificación
        Notificacion::create([
            'id_usuario' => $usuario->id_usuario,
            'titulo' => 'Solicitud Renovada',
            'tipo_notificacion' => 'solicitud_renovada',
            'mensaje' => 'Has renovado tu solicitud de registro. Será revisada nuevamente.'
        ]);

        return redirect()->route('login')
            ->with('success', 'Solicitud renovada con éxito. Será revisada nuevamente.');
    }
}
