<?php

namespace App\Http\Controllers;

use App\Models\pregunta;
use App\Models\User;
use Illuminate\Http\Request;

class seguridadController extends Controller
{
  public function comprobar(Request $request)
  {
    try {
      $cedula = $request->input('cedula');
      $mascota = $request->input('mascota');
      $ciudad = $request->input('ciudad');
      $amigo = $request->input('amigo');
      $usuario = User::where('cedula', $cedula)->first();
      $preguntas = $usuario->preguntas_de_seguridad()->first();
      if ($preguntas->primera_mascota == $mascota && $preguntas->ciudad_de_nacimiento == $ciudad && $preguntas->nombre_de_mejor_amigo == $amigo) {
        $usuario->password = bcrypt('12345678');
        $usuario->save();
      } else {
        return redirect()->route('recuperar.clave')->with('error', 'algunas de las preguntas respondidas son incorrectas');
      }
      return redirect()->route('login')->with('success', 'su contraseña se ha restablecido');
    } catch (\Exception $e) {
      return redirect()->route('login')->with('error', 'Error al procesar la petición: ' . $e->getMessage());
    }
  }
}
