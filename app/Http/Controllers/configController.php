<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class configController extends Controller
{
    public function index(){
        $id_usuario=Auth::user()->id_usuario;
        $usuario=User::where('id_usuario',$id_usuario)->first();
        return view('usuarios.configuracion',compact('usuario'));
    }

    public function update(Request $request, $id_usuario)
    {
        // Obtener los datos del usuario
        $usuario = User::where('id_usuario', $id_usuario)->first();
        if (!$usuario) {
            return redirect()->route('usuarios.configuracion')->with('error', 'Usuario no encontrado');
        }
    
        // Reglas de validación
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|integer|unique:users,cedula,' . $id_usuario . ',id_usuario',
            'email' => 'required|email|max:255|unique:users,email,' . $id_usuario . ',id_usuario',
            'contraseña'=>'required',
        ];
    
        // Mensajes de validación
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
    
        // Validación de los datos de la solicitud
        $request->validate($rules, $messages);
    
        try {
            // Actualizar los datos del usuario
         
                $usuario->nombre = $request->input('nombre');
          
            
                $usuario->apellido = $request->input('apellido');
          
                $usuario->cedula = $request->input('cedula');
           
                $usuario->email = $request->input('email');
            
              $usuario->nombre_usuario = $request->input('nombre_usuario');
                $usuario->password = bcrypt($request->input('contraseña')); // Encriptar la contraseña
            
            // Guardar los cambios
            $usuario->save();
    
            return redirect()->route('usuarios.configuracion')->with('success', 'Datos actualizados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.configuracion')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
    }
    
}
