<?php

namespace App\Http\Controllers;
use App\Http\Requests\storePeticionRequest;
use App\Models\EmpleadoAutorizado;
use App\Models\EstadoUsuario;
use App\Models\movimiento;
use App\Models\Notificacion;
use Illuminate\Support\Str;
use App\Models\peticion;
use App\Models\pregunta;
use App\Models\RespuestaDeSeguridad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class peticionController extends Controller
{
    public function index(Request $request)
    {
        // Verificar si la solicitud es AJAX
        if ($request->ajax()) {
            $peticiones = User::where('id_estado_usuario', 3)
                              ->orWhere('id_estado_usuario', 4)
                              ->orWhere('id_estado_usuario', 1)
                              ->with(['estadoUsuario', 'empleadoAutorizado']) // Eliminar relación 'role'
                              ->get()
                              ->map(function ($user) {
                                  return [
                                      'id_usuario' => $user->id_usuario,
                                      'estado_usuario' => $user->estadoUsuario ? $user->estadoUsuario->nombre_estado : 'Desconocido',
                                      'nombre' => $user->empleadoAutorizado ? $user->empleadoAutorizado->nombre : 'N/A',
                                      'apellido' => $user->empleadoAutorizado ? $user->empleadoAutorizado->apellido : 'N/A',
                                      'cedula' => $user->empleadoAutorizado ? $user->empleadoAutorizado->cedula : 'N/A',
                                      'email' => $user->email,
                                      'nombre_usuario' => $user->nombre_usuario,
                                      'id_estado_usuario' => $user->id_estado_usuario,
                                  ];
                              });

            return response()->json($peticiones);
        }

        // Si no es AJAX, cargar la vista como de costumbre
        $peticiones = User::where('id_estado_usuario', 3)
                          ->with(['estadoUsuario', 'empleadoAutorizado']) // Eliminar relación 'role'
                          ->get();
        return view('peticiones.listapeticiones', compact('peticiones'));
    }
   // UserController.php
   
   // filepath: c:\laragon\www\mi-proyecto-laravel-master\app\Http\Controllers\peticionController.php
   public function buscarEmpleado(Request $request)
   {
       $request->validate(['cedula' => 'required|string']);
   
       $empleado = EmpleadoAutorizado::where('cedula', $request->cedula)->first();
   
       if (!$empleado) {
           return response()->json(['error' => 'Empleado no encontrado'], 404);
       }
   
       // Asegúrate que estos campos coincidan con tu BD
       return response()->json([
           'nombre' => $empleado->nombre,
           'apellido' => $empleado->apellido,
           'genero' => $empleado->genero,
           'fecha_nacimiento' => $empleado->fecha_nacimiento,
           'altura' => $empleado->altura
       ]);
   }

 // Controlador Store corregido
 public function store(Request $request)
 {
     DB::beginTransaction();
     
     try {
         // Validación de datos
         $validated = $request->validate([
             'nombre_usuario' => 'required|string|max:255|unique:users,nombre_usuario',
             'email' => 'required|email|unique:users,email',
             'password' => 'required|string|min:8',
             'pregunta_1' => 'required|integer|exists:preguntas_de_seguridad,id_pregunta',
             'pregunta_2' => 'required|integer|exists:preguntas_de_seguridad,id_pregunta',
             'pregunta_3' => 'required|integer|exists:preguntas_de_seguridad,id_pregunta',
             'respuesta_1' => 'required|string|max:255',
             'respuesta_2' => 'required|string|max:255',
             'respuesta_3' => 'required|string|max:255',
             'cedula' => 'required|string|max:10|regex:/^[0-9]{8,10}$/'
         ], [
             'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
             'nombre_usuario.unique' => 'El nombre de usuario ya está en uso.',
             'email.required' => 'El correo electrónico es obligatorio.',
             'email.unique' => 'El correo electrónico ya está registrado.',
             'password.required' => 'La contraseña es obligatoria.',
             'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
             'pregunta_1.required' => 'Debe seleccionar la primera pregunta de seguridad.',
             'pregunta_2.required' => 'Debe seleccionar la segunda pregunta de seguridad.',
             'pregunta_3.required' => 'Debe seleccionar la tercera pregunta de seguridad.',
             'respuesta_1.required' => 'Debe proporcionar una respuesta para la primera pregunta.',
             'respuesta_2.required' => 'Debe proporcionar una respuesta para la segunda pregunta.',
             'respuesta_3.required' => 'Debe proporcionar una respuesta para la tercera pregunta.',
             'cedula.required' => 'La cédula es obligatoria.',
             'cedula.regex' => 'La cédula debe contener entre 8 y 10 dígitos numéricos.',
         ]);
 
         // Buscar el empleado autorizado
         $empleado = EmpleadoAutorizado::where('cedula', $validated['cedula'])->first();
         if (!$empleado) {
             return response()->json([
                 'success' => false,
                 'errors' => ['cedula' => ['El empleado no está autorizado para registrarse']]
             ], 422);
         }
 
         // Verificar si ya está registrado en Users
         if (User::where('id_empleado_autorizado', $empleado->id_empleado_autorizado)->exists()) {
             return response()->json([
                 'success' => false,
                 'errors' => ['cedula' => ['El empleado ya está registrado.']]
             ], 422);
         }
 
         // Crear usuario
         $user = User::create([
             'id_empleado_autorizado' => $empleado->id_empleado_autorizado,
             'slug' => Str::slug(Str::lower($validated['nombre_usuario'])),
             'nombre_usuario' => Str::lower($validated['nombre_usuario']),
             'email' => $validated['email'],
             'password' => bcrypt($validated['password']),
             'id_estado_usuario' => 3, // No verificado
         ]);
 
         if (!$user) {
             throw new \Exception('No se pudo crear el usuario. Inténtelo de nuevo.');
         }
 
         // Asignar rol "registrador" al usuario recién creado
         $registradorRole = Role::where('name', 'registrador')->first();
         if ($registradorRole) {
             $user->assignRole($registradorRole);
         }
 
         // Guardar respuestas de seguridad
         foreach (range(1, 3) as $i) {
             RespuestaDeSeguridad::create([
                 'id_usuario' => $user->id_usuario,
                 'id_pregunta' => $validated["pregunta_$i"],
                 'respuesta' => $validated["respuesta_$i"],
             ]);
         }
 
         // Crear notificación
         Notificacion::create([
             'id_usuario' => $user->id_usuario,
                'titulo' => 'Petición de Registro',
             'tipo_notificacion' => 'peticion_registrada',
             'mensaje' => 'Se ha realizado una petición de registro para el usuario '.$user->nombre_usuario,
         ]);
 
         DB::commit();
 
         return response()->json([
             'success' => true,
             'message' => 'Usuario registrado exitosamente. Redirigiendo al login...',
             'redirect' => route('login')
         ], 200); // Asegurarse de devolver código 200 para éxito
         
     } catch (ValidationException $e) {
         DB::rollBack();
         return response()->json([
             'success' => false,
             'errors' => $e->errors(),
             'message' => 'Por favor corrige los errores en el formulario.'
         ], 422);
 
     } catch (\Exception $e) {
         DB::rollBack();
         return response()->json([
             'success' => false,
             'message' => 'Ocurrió un error: ' . $e->getMessage()
         ], 500);
     }
 }


   

   
   public function validarCampoAsincrono(Request $request)
   {
       $campo = $request->input('campo');
       $valor = $request->input('valor');
       $error = null;
   
       switch ($campo) {
           case 'cedula':
               $usuarioPorCedula = User::where('cedula', $valor)->first();
               if ($usuarioPorCedula && in_array($usuarioPorCedula->id_estado_usuario, [1, 2, 3])) {
                   $error = 'La cédula ya está asociada a un usuario aceptado, desactivado o no verificado.';
               }
               break;
   
           case 'nombre_usuario':
               $usuarioPorNombre = User::where('nombre_usuario', $valor)->first();
               if ($usuarioPorNombre) {
                   switch ($usuarioPorNombre->id_estado_usuario) {
                       case 1: // Aceptado
                       case 2: // Desactivado
                           $error = 'El nombre de usuario ya está en uso.';
                           break;
                       case 3: // No verificado
                           $error = 'El nombre de usuario ya ha sido escogido para una solicitud.';
                           break;
                       case 4: // Rechazado
                           $error = 'El nombre de usuario ya ha sido rechazado.';
                           break;
                   }
               }
               break;
   
           case 'email':
               $usuarioPorEmail = User::where('email', $valor)->first();
               if ($usuarioPorEmail && in_array($usuarioPorEmail->id_estado_usuario, [1, 2, 3])) {
                   $error = 'El correo electrónico ya está asociado a un usuario aceptado, desactivado o no verificado.';
               }
               break;
   
           default:
               $error = 'Campo no válido para validación.';
               break;
       }
   
       return response()->json(['error' => $error]);
   }
   
    public function rechazar($id)
    {
$peticion=user::where('id_usuario',$id)->first();
        if (!$peticion) {
            return redirect()->route('peticiones.index')->with('error', 'Petición no encontrada');;
        }

        $peticion->id_estado_usuario = 4;
        $peticion->save();
        $movimiento = new movimiento();
        $movimiento->id_usuario = auth()->user()->id_usuario;
        $movimiento->id_usuario_afectado = $peticion->id_usuario;
        $movimiento->descripcion = 'se rechazo una petición';
        $movimiento->save();
        Notificacion::create([
            'id_usuario' => auth()->user()->id_usuario,
            'titulo' => 'Petición Rechazada',
            'tipo_notificacion' => 'peticion_rechazada',
            'mensaje' => 'se rechazo la peticion de ingreso de '.$peticion->nombre_usuario,
        ]);
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
                
                $movimiento = new movimiento();
                $movimiento->id_usuario = auth()->user()->id_usuario;
                $movimiento->id_usuario_afectado = $peticion->id_usuario;
                $movimiento->descripcion = 'se acepto una petición';
                $movimiento->save();
                Notificacion::create([
                    'id_usuario' => auth()->user()->id_usuario,
                    'titulo' => 'Petición Aceptada',
                    'tipo_notificacion' => 'peticion_aceptada',
                    'mensaje' => 'Se ha aceptado la peticion de ingreso de '.$peticion->nombre_usuario,
                ]);
                // Redirigir con un mensaje de éxito
                return redirect()->route('peticiones.index')->with('success', 'Usuario aceptado correctamente');
            }
        
            // Si el usuario no está en estado "No Verificado", mostrar un mensaje de error
            return redirect()->route('peticiones.index')->with('error', 'Este usuario no está en estado No Verificado');
            
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción y mostrar el mensaje correspondiente
            DB::rollBack();
            return redirect()->route('peticiones.index')->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function obtenerPeticiones()
    {
        // Asegúrate de cargar las relaciones necesarias
        $peticiones = User::where('id_estado_usuario', 3)
                          ->with(['estadoUsuario'])
                          ->get();

        return response()->json($peticiones);
    }
    
}
