<?php

namespace App\Http\Controllers;
use App\Http\Requests\storePeticionRequest;
use App\Models\EstadoUsuario;
use Illuminate\Support\Str;
use App\Models\peticion;
use App\Models\pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class peticionController extends Controller
{
    public function index()
    {
        $peticiones = user::where('id_estado_usuario',3)->get();
        return view('peticiones.listapeticiones', compact('peticiones'));
    }
   // UserController.php
   public function store(Request $request)
   {
       // Validación
       $validated = $request->validate([
           'nombre' => 'required|string|max:255',
           'apellido' => 'required|string|max:255',
           'nombre_usuario' => 'required|string|max:255|unique:users,nombre_usuario',
           'cedula' => 'required|string|max:255|unique:users,cedula',
           'email' => 'required|email|unique:users,email',
           'password' => 'required|string|min:8',
           'genero' => 'required|string',
           'fecha_nacimiento' => 'required|date',
           'altura' => 'required|numeric|min:0',
           'pregunta_1' => 'required|integer|exists:preguntas_de_seguridad,id_pregunta',
           'pregunta_2' => 'required|integer|exists:preguntas_de_seguridad,id_pregunta',
           'pregunta_3' => 'required|integer|exists:preguntas_de_seguridad,id_pregunta',
           'respuesta_1' => 'required|string|max:255',
           'respuesta_2' => 'required|string|max:255',
           'respuesta_3' => 'required|string|max:255',
           'rol' => 'required|exists:roles,id_rol',
       ]);
   
       // Inserción manual del usuario
       $user = new User;
       $user->id_rol = $validated['rol'];
       $user->slug = Str::slug($validated['nombre'] . ' ' . $validated['apellido']);
       $user->nombre = $validated['nombre'];
       $user->apellido = $validated['apellido'];
       $user->nombre_usuario = $validated['nombre_usuario'];
       $user->cedula = $validated['cedula'];
       $user->email = $validated['email'];
       $user->password = bcrypt($validated['password']);
       $user->genero = $validated['genero'];
       $user->fecha_nacimiento = $validated['fecha_nacimiento'];
       $user->altura = $validated['altura'];
       $user->id_estado_usuario = 3; // Asignación manual de estado no verificado (3)
       $user->save(); // Guardar el usuario
   
       // Crear respuestas de seguridad
       for ($i = 1; $i <= 3; $i++) {
           $preguntaId = $validated['pregunta_' . $i];
           $respuesta = $validated['respuesta_' . $i];
   
           // Verificar si la pregunta existe
           $pregunta = Pregunta::find($preguntaId);
           
           if ($pregunta) {
               // Crear la respuesta de seguridad
               $respuestaSeguridad = new RespuestaDeSeguridad;
               $respuestaSeguridad->id_usuario = $user->id_usuario;
               $respuestaSeguridad->id_pregunta = $preguntaId;
               $respuestaSeguridad->respuesta = $respuesta;
               $respuestaSeguridad->save(); // Guardar la respuesta
           } else {
               // Si la pregunta no existe, devolver un error
               return redirect()->back()->withErrors("La pregunta de seguridad $i no existe.");
           }
       }
   
       // Redirigir al login con mensaje de éxito
       return redirect()->route('login')->with('success', 'Usuario registrado exitosamente!');
   }
   
   
   

    

    
    public function rechazar($id)
    {
$peticion=user::where('id_usuario',$id)->first();
        if (!$peticion) {
            return redirect()->route('peticiones.index')->with('error', 'Petición no encontrada');;
        }

        $peticion->id_estado_usuario = 4;
        $peticion->save();
        return redirect()->route('peticiones.index')->with('success', 'Petición rechazada con éxito');
    }
 
    
    public function aceptar($id)
    {
        try {
            // Buscar la petición por su ID
            $peticion = User::where('id_usuario', $id)->first();
        
            // Verificar si la petición existe
            if (!$peticion) {
                return redirect()->route('usuarios.index')->with('error', 'Usuario no encontrado');
            }
    
            // Verificar si el usuario está en estado "No Verificado" (id 3)
            if ($peticion->id_estado_usuario == 3) { // "No Verificado" tiene el id 3
                // Cambiar el estado del usuario a "Aceptado" (id 1)
                
        
                // Obtener el ID del estado "Aceptado" (id 1)
               
        
                // Asignar el ID de "Aceptado" al campo id_estado_usuario
                $peticion->id_estado_usuario =1;
                $peticion->save(); // Guardar el cambio de estado
        
                // Confirmar la transacción
                
        
                // Redirigir con un mensaje de éxito
                return redirect()->route('usuarios.index')->with('success', 'Usuario aceptado correctamente');
            }
        
            // Si el usuario no está en estado "No Verificado", mostrar un mensaje de error
            return redirect()->route('usuarios.index')->with('error', 'Este usuario no está en estado No Verificado');
            
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción y mostrar el mensaje correspondiente
            DB::rollBack();
            return redirect()->route('usuarios.index')->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }
    
    

    



    
}
