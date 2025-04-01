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
    public function index(Request $request)
    {
        // Verificar si la solicitud es AJAX
        if ($request->ajax()) {
            $peticiones = User::where('id_estado_usuario', 3)
                              ->orWhere('id_estado_usuario', 4)
                              ->orWhere('id_estado_usuario', 1)
                              ->with(['role', 'estadoUsuario', 'empleadoAutorizado']) // Cargar relaciones necesarias
                              ->get();

            return response()->json($peticiones);
        }

        // Si no es AJAX, cargar la vista como de costumbre
        $usuarios = User::with(['empleadoAutorizado', 'role'])->get(); // Cargar relaciones necesarias
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
       
        // Actualizar los campos del usuario
      
        $usuario->email = $request->input('email');
        $usuario->nombre_usuario = $request->input('nombre_usuario');
        $usuario->password = bcrypt($request->input('contraseña'));

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
        
        // Verificar si el usuario autenticado es admin o si el usuario a desactivar es admin
        if (auth()->user()->role->rol == 'admin' && $usuario->role->rol == 'admin') {
            return redirect()->route('usuarios.index')->with('error', 'No se puede desactivar a otro administrador.');
        }

        $usuario->id_estado_usuario = 2; // Estado desactivado
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

        // Verificar si el usuario autenticado es admin o si el usuario a activar es admin
        if (auth()->user()->role->rol == 'admin' && $usuario->role->rol == 'admin') {
            return redirect()->route('usuarios.index')->with('error', 'No se puede activar a un administrador.');
        }

        $usuario->id_estado_usuario = 1; // Estado activo
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario activado correctamente');
    } catch (\Exception $e) {
        return redirect()->route('usuarios.index')->with('error', 'Error al activar el usuario: ' . $e->getMessage());
    }
}

   



}
