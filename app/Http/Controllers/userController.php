<?php

namespace App\Http\Controllers;

use App\Models\Peticion;
use App\Models\roles;
use App\Models\User;
use Illuminate\Http\Request;


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

    public function update(Request $request, $id_usuario)
    {
        
        $usuario = User::where('id_usuario', $id_usuario)->first();
        if (!$usuario) {
            return redirect()->route('usuarios.configuracion')->with('error', 'Usuario no encontrado');
        }
    
        
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|integer|unique:users,cedula,' . $id_usuario . ',id_usuario',
            'email' => 'required|email|max:255|unique:users,email,' . $id_usuario . ',id_usuario',
            'contraseña'=>'required',
        ];
    
       
        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.integer' => 'La cédula debe ser un número entero.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.digits_between' => 'El número de teléfono debe tener entre 10 y 15 dígitos.',
            'contraseña.required'=>'la contraseña es obligatoria'
        ];
    
      
        $request->validate($rules, $messages);
    
        try {
            
         
                $usuario->nombre = $request->input('nombre');
          
            
                $usuario->apellido = $request->input('apellido');
          
                $usuario->cedula = $request->input('cedula');
           
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
