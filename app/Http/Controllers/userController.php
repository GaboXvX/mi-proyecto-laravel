<?php

namespace App\Http\Controllers;

use App\Models\Peticion;
use App\Models\roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('id_usuario', 'desc')->get();
        return view('usuarios.listaUsuarios', compact('usuarios'));
    }

    public function create()
    {
        $roles = roles::all();
        return view('usuarios.registrarUsuarios', compact('roles'));
    }

    public function store(Request $request, $id)
    {


        try {
            $peticion = Peticion::where('id_peticion', $id)->first();

            if (!$peticion) {
                return redirect()->route('usuarios.create')->with('error', 'Petición no encontrada');
            }

            $peticion->estado_peticion = 'aceptado';
            $peticion->save();

            $usuario = new User();
            $usuario->id_rol = $peticion->id_rol;
            $usuario->nombre = $peticion->nombre;
            $usuario->apellido = $peticion->apellido;
            $usuario->cedula = $peticion->cedula;
            $usuario->email = $peticion->email;
            $usuario->nombre_usuario = $peticion->nombre_usuario;
            $usuario->password = bcrypt($peticion->password);
            $usuario->estado = 'activo';
            $usuario->save();
            return redirect()->route('peticiones.index')->with('success', 'Datos enviados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('peticiones.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
        }
    }



    public function edit($slug)
    {
        $usuario = User::where('slug', $slug)->firstOrFail();
        return view('usuarios.modificarUsuarios', compact('usuario'));
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|max:255',
            'correo' => 'required|email|max:255|unique:users,correo,' . $slug . ',slug',
            'password' => 'nullable|string|min:8',
        ]);

        try {
            $usuario = User::where('slug', $slug)->firstOrFail();
            $usuario->nombre = $request->input('nombre');
            $usuario->apellido = $request->input('apellido');
            $usuario->cedula = $request->input('cedula');
            $usuario->correo = $request->input('correo');


            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->input('password'));
            }

            $usuario->save();

            return redirect()->route('usuarios.index')->with('success', 'Datos actualizados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
    }

    public function destroy($slug)
    {
        try {
            $usuario = User::where('slug', $slug)->firstOrFail();
            $usuario->delete();
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
    public function desactivar($id)
    {
        try{
        $usuario = User::where('id_usuario', $id)->first();
        $usuario->estado = 'desactivado';
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado correctamente');
}catch(\Exception $e){
    return redirect()->route('usuarios.index')->with('error', 'Error al desactivar el usuario: ' . $e->getMessage());
}

    }
    public function activar($id)
    {
        try{
        $usuario = User::where('id_usuario', $id)->first();
        $usuario->estado = 'activo';
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'Usuario activado correctamente');
}catch(\Exception $e){
    return redirect()->route('usuarios.index')->with('error', 'Error al activar el usuario: ' . $e->getMessage());
}
    }
    
}
