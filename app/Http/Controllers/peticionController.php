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
use Illuminate\Database\QueryException;

class peticionController extends Controller
{
    public function index(Request $request)
    {
        // Verificar si la solicitud es AJAX
        if ($request->ajax()) {
            $peticiones = User::where('id_estado_usuario', 3)->orwhere('id_estado_usuario', 4)->orwhere('id_estado_usuario', 1)
                              ->with(['role', 'estadoUsuario'])
                              ->get();

            return response()->json($peticiones);
        }

        // Si no es AJAX, cargar la vista como de costumbre
        $peticiones = User::where('id_estado_usuario', 3)->get();
        return view('peticiones.listapeticiones', compact('peticiones'));
    }
   // UserController.php
   public function store(Request $request)
{
    // Validación de los datos
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'nombre_usuario' => 'required|string|max:255',
        'cedula' => [
            'required',
            'string',
            'max:255',
            'unique:users,cedula',
            'regex:/^[0-9]{8,10}$/'  // Ejemplo de validación de cédula numérica entre 8 y 10 dígitos
        ],
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
    ], [
        'required' => 'El campo :attribute es obligatorio.',
        'max' => 'El campo :attribute no puede tener más de :max caracteres.',
        'email' => 'El :attribute debe ser una dirección de correo electrónico válida.',
        'min' => 'El campo :attribute debe tener al menos :min caracteres.',
        'exists' => 'La :attribute seleccionada no existe en nuestros registros.',
        'unique' => 'El :attribute ya está registrado.',
        'regex' => [
            'cedula' => 'La cédula ingresada no es válida. Debe contener entre 8 y 10 dígitos numéricos.',
        ],
        'cedula.required' => 'La cédula es un campo obligatorio.',
        'cedula.unique' => 'La cédula ingresada ya está registrada en nuestros registros.',
        'cedula.regex' => 'La cédula debe contener entre 8 y 10 dígitos numéricos.',
        'email.unique' => 'El correo electrónico ingresado ya está registrado.',
        'email.required' => 'El correo electrónico es obligatorio.',
    ]);

    try {
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
        $user->save();

        // Crear respuestas de seguridad
        for ($i = 1; $i <= 3; $i++) {
            $preguntaId = $validated['pregunta_' . $i];
            $respuesta = $validated['respuesta_' . $i];

            $respuestaSeguridad = new RespuestaDeSeguridad;
            $respuestaSeguridad->id_usuario = $user->id_usuario;
            $respuestaSeguridad->id_pregunta = $preguntaId;
            $respuestaSeguridad->respuesta = $respuesta;
            $respuestaSeguridad->save();
        }

        // Redirigir al login con mensaje de éxito
        return redirect()->route('login')->with('success', 'Usuario registrado exitosamente!');
    } catch (QueryException $e) {
        if ($e->getCode() === '23000') { // Código de error para violación de restricción única
            return redirect()->back()->withErrors('El nombre de usuario, correo electrónico o cédula ya está en uso.');
        }

        // Si ocurre otro error, lanzar la excepción
        throw $e;
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
                return redirect()->route('peticiones.index')->with('success', 'Usuario aceptado correctamente');
            }
        
            // Si el usuario no está en estado "No Verificado", mostrar un mensaje de error
            return redirect()->route('peticiones.index')->with('error', 'Este usuario no está en estado No Verificado');
            
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción y mostrar el mensaje correspondiente
            DB::rollBack();
            return redirect()->route('usuarios.index')->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function obtenerPeticiones()
    {
        // Asegúrate de cargar las relaciones necesarias
        $peticiones = User::where('id_estado_usuario', 3)
                          ->with(['role', 'estadoUsuario'])
                          ->get();

        return response()->json($peticiones);
    }
    
}
