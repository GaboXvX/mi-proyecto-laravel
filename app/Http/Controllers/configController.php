<?php

namespace App\Http\Controllers;

use App\Models\movimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class configController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        $empleadoAutorizado = $usuario->empleadoAutorizado;
        $cargo = $empleadoAutorizado->cargo;
        
        return view('usuarios.configuracion', compact('usuario', 'empleadoAutorizado', 'cargo'));
    }

    public function restaurar($id_usuario)
    {
        try {
            $usuario = User::where('id_usuario', $id_usuario)->first();
            $usuario->password = bcrypt('12345678');
            $usuario->save();
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->id_usuario_afectado = $usuario->id_usuario;
            $movimiento->descripcion = 'se restauro el usuario ';
            $movimiento->save();
            return redirect()->route('usuarios.index')->with('success', 'usuario restaurado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al restaurar el usuario: ' . $e->getMessage());
        }
    }
}
