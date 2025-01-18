<?php

namespace App\Http\Controllers;

use App\Http\Requests\updateUserRequest;
use App\Models\Peticion;
use App\Models\roles;
use App\Models\User;



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


            $usuario->nombre = $request->input('nombre');
            $usuario->apellido = $request->input('apellido');
            $usuario->email = $request->input('email');
            $usuario->nombre_usuario = $request->input('nombre_usuario');
            $usuario->password = bcrypt($request->input('contraseña'));


            $usuario->save();

            return redirect()->route('usuarios.configuracion')->with('success', 'Datos actualizados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.configuracion')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
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
        try {
            $usuario = User::where('id_usuario', $id)->first();
            $usuario->estado = 'desactivado';
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
            $usuario->estado = 'activo';
            $usuario->save();
            return redirect()->route('usuarios.index')->with('success', 'Usuario activado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al activar el usuario: ' . $e->getMessage());
        }
    }
}
