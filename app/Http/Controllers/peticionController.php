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
        $peticiones = user::all();
        return view('peticiones.listapeticiones', compact('peticiones'));
    }
   // UserController.php
   public function store(Request $request)
   {
       // Obtener la cédula del usuario
       $cedula = $request->input('cedula');
       
       // Limpiar el nombre de usuario eliminando caracteres especiales
       $request->merge([
           'nombre_usuario' => preg_replace('/[^a-zA-Z0-9_]/', '', $request->input('nombre_usuario'))
       ]);

       // Validación de los datos
       $validated = $request->validate([
           'nombre' => 'required|string|max:255',
           'apellido' => 'required|string|max:255',
           'nombre_usuario' => 'required|string|max:255',
           'cedula' => 'required|string|max:255',
           'email' => 'required|email',
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
       ], [
           'required' => 'El campo :attribute es obligatorio.',
           'max' => 'El campo :attribute no puede tener más de :max caracteres.',
           'email' => 'El :attribute debe ser una dirección de correo electrónico válida.',
           'min' => 'El campo :attribute debe tener al menos :min caracteres.',
           'exists' => 'La :attribute seleccionada no existe en nuestros registros.',
       ]);
   
       // Verificar si el correo electrónico ya existe
       $existingEmail = User::where('email', $validated['email'])->first();
       if ($existingEmail && $existingEmail->id_estado_usuario != 4) {
           return redirect()->back()->withErrors('El correo electrónico ya está en uso.');
       }

       // Verificar si el nombre de usuario ya existe
       $existingNombreUsuario = User::where('nombre_usuario', $validated['nombre_usuario'])->first();
       if ($existingNombreUsuario) {
           return redirect()->back()->withErrors('El nombre de usuario ya está en uso.');
       }

       // Verificar si el usuario existe por cédula
       $existingUser = User::where('cedula', $validated['cedula'])->first();
   
       if ($existingUser) {
           // Si el usuario ya existe, verificamos su estado
           switch ($existingUser->id_estado_usuario) {
               case 4: // Rechazado
                   // Si el usuario está rechazado, renovamos la solicitud y cambiamos el estado a Aceptado (1)
                   $existingUser->id_estado_usuario = 3;
                   $existingUser->save();
                   return redirect()->route('login')->with('success', 'Petición renovada.');
               
               case 2: // Desactivado
                   // Si el usuario está desactivado, puedes mostrar un mensaje o decidir si permites la renovación
                   return redirect()->back()->withErrors('Este usuario está desactivado y no puede hacer una nueva solicitud.');
               
               case 3: // No verificado
                   // Si el usuario no está verificado, le indicamos que ya tiene una solicitud pendiente
                   return redirect()->back()->withErrors('Este usuario tiene una petición pendiente.');
               
               case 1: // Aceptado
                   // Si el usuario ya está aceptado, no realizamos ninguna acción
                   return redirect()->back()->withErrors('Este usuario ya tiene una petición aceptada.');
           }
       }
   
       // Generar un slug único para el usuario
       $slug = Str::slug($validated['nombre'] . ' ' . $validated['apellido']);
       $originalSlug = $slug;
       $counter = 1;
   
       while (User::where('slug', $slug)->exists()) {
           $slug = $originalSlug . '-' . $counter;
           $counter++;
       }
   
       // Crear el nuevo usuario
       $user = new User;
       $user->id_rol = $validated['rol'];
       $user->slug = $slug;
       $user->nombre = $validated['nombre'];
       $user->apellido = $validated['apellido'];
       $user->nombre_usuario = $validated['nombre_usuario'];
       $user->cedula = $validated['cedula'];
       $user->email = $validated['email'];
       $user->password = bcrypt($validated['password']);
       $user->genero = $validated['genero'];
       $user->fecha_nacimiento = $validated['fecha_nacimiento'];
       $user->altura = $validated['altura'];
       $user->id_estado_usuario = 3; // Asignar estado "No verificado"
       $user->save(); // Guardar el nuevo usuario
   
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
   
   public function validarCedulaYUsuario(Request $request)
   {
       $cedula = $request->input('cedula');
       $nombreUsuario = $request->input('nombre_usuario');
       $email = $request->input('email');

       $errorCedula = null;
       $errorNombreUsuario = null;
       $errorEmail = null;
   
       // Validar cédula
       if ($cedula) {
           $usuarioPorCedula = User::where('cedula', $cedula)->first();
           if ($usuarioPorCedula) {
               if ($usuarioPorCedula->id_estado_usuario == 3) {
                   $errorCedula = 'Esta cédula ya ha sido escogida para una solicitud.';
               } elseif ($usuarioPorCedula->id_estado_usuario == 1) {
                   $errorCedula = 'La cédula ya está asociada a un usuario aceptado.';
               }
           }
       }
   
       // Validar nombre de usuario
       if ($nombreUsuario) {
           $regexCorreo = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
           if (preg_match($regexCorreo, $nombreUsuario)) {
               $errorNombreUsuario = 'El nombre de usuario no puede ser un correo electrónico.';
           } else {
               $usuarioPorNombre = User::where('nombre_usuario', $nombreUsuario)->first();
               if ($usuarioPorNombre) {
                   if ($usuarioPorNombre->id_estado_usuario == 3) {
                       $errorNombreUsuario = 'El nombre de usuario ya ha sido escogido para una solicitud.';
                   } elseif (in_array($usuarioPorNombre->id_estado_usuario, [1, 2])) {
                       $errorNombreUsuario = 'El nombre de usuario ya está en uso.';
                   }
               }
           }
       }

       // Validar correo electrónico
       if ($email) {
           $usuarioPorEmail = User::where('email', $email)->first();
           if ($usuarioPorEmail) {
               if ($usuarioPorEmail->id_estado_usuario == 3) {
                   $errorEmail = 'El correo electrónico ya ha sido escogido para una solicitud.';
               } elseif ($usuarioPorEmail->id_estado_usuario != 4) {
                   $errorEmail = 'El correo electrónico ya está en uso.';
               }
           }
       }
   
       return response()->json([
           'error_cedula' => $errorCedula,
           'error_nombre_usuario' => $errorNombreUsuario,
           'error_email' => $errorEmail
       ]);
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
