<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Http\Requests\updateUserRequest;
use App\Models\pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\roles;
use App\Models\User;
use Illuminate\Http\Request;



class UserController extends Controller
{
   public function index()
{
    $usuarios = User::where('id_estado_usuario', 1)
                    ->orWhere('id_estado_usuario', 2)
                    ->orderBy('id_usuario', 'desc')
                    ->get();
    return view('usuarios.listaUsuarios', compact('usuarios'));
}
    public function create()
    {
        $preguntas=pregunta::all();

        $roles = roles::all();
        return view('usuarios.registrarUsuarios', compact('roles','preguntas'));
    }

    public function edit($slug)
    {
        $usuario = User::where('slug', $slug)->firstOrFail();
        return view('usuarios.modificarUsuarios', compact('usuario'));
    }

    public function update(updateUserRequest $request, $id_usuario)
{
    $usuario = User::where('id_usuario', $id_usuario)->first();
    if (!$usuario) {
        return redirect()->route('usuarios.configuracion')->with('error', 'Usuario no encontrado');
    }

    try {
        // Verificar si el nombre o apellido ha cambiado para actualizar el slug
        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        if ($usuario->nombre !== $nombre || $usuario->apellido !== $apellido) {
            $slug = Str::slug($nombre . ' ' . $apellido);
            $originalSlug = $slug;
            $counter = 1;

            while (User::where('slug', $slug)->where('id_usuario', '!=', $id_usuario)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $usuario->slug = $slug;
        }

        // Actualizar los campos del usuario
        $usuario->nombre = $nombre;
        $usuario->apellido = $apellido;
        $usuario->email = $request->input('email');
        $usuario->nombre_usuario = $request->input('nombre_usuario');
        $usuario->password = bcrypt($request->input('contraseña'));
        $usuario->genero = $request->input('genero'); // Procesar género
        $usuario->fecha_nacimiento = $request->input('fecha_nacimiento'); // Procesar fecha de nacimiento
        $usuario->altura = $request->input('altura'); // Procesar altura

        $usuario->save();

        return redirect()->route('usuarios.configuracion')->with('success', 'Datos actualizados correctamente');
    } catch (\Exception $e) {
        return redirect()->route('usuarios.configuracion')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
    }
}

   
    public function desactivar($id)
    {
        try {
            $usuario = User::where('id_usuario', $id)->first();
            $usuario->id_estado_usuario = 2;
            $usuario->save();
            return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al desactivar el usuario: ' . $e->getMessage());
        }
    }
    public function activar($id)
    {
        try {
            $usuario = User::where('id_usuario', $id)->first();
            $usuario->id_estado_usuario = 1;
            $usuario->save();
            return redirect()->route('usuarios.index')->with('success', 'Usuario activado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al activar el usuario: ' . $e->getMessage());
        }
    }
   



}
