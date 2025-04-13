<?php
namespace App\Http\Controllers;

use App\Models\movimiento;
use App\Models\User;
use App\Models\Pregunta;  // Asumimos que la clase Pregunta ya está creada.
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isEmpty;

class LoginController extends Controller
{
    public function index(){
        return view('login');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            // Si el usuario ya está autenticado, redirigir al home
            return redirect()->route('home')->with('success', 'Inicio de sesión exitoso');
        }
        return view('login');
    }

       public function authenticate(Request $request)
{
    // Validar la entrada del usuario
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    // Intentar autenticar al usuario
    $user = User::where('email', $request->input('email'))->first();

    if ($user) {
        // Verificar si el usuario está desactivado
        if ($user->id_estado_usuario == 2) {
            return redirect()->route('login')->with('error', 'Este usuario se encuentra desactivado');
        }

        // Verificar si el usuario está pendiente de verificación (estado 3)
        if ($user->id_estado_usuario == 3) {
            return redirect()->route('login')->with('error', 'Este usuario tiene una petición pendiente de verificación.');
        }
    }

    // Verificar las credenciales de la contraseña
    if ($user && Hash::check($request->input('password'), $user->password)) {
        // Autenticación exitosa
        Auth::login($user);
        $request->session()->regenerate();
        $movimiento = new movimiento();
        $movimiento->id_usuario = auth()->user()->id_usuario;
        $movimiento->descripcion = 'ha iniciado sesión';
        $movimiento->save();
        // Redirigir al home o página de inicio
        return redirect()->route('home')->with('success', 'Inicio de sesión exitoso');
    }

    // Si no es posible autenticar, devolver un error
    return redirect()->back()->withErrors([
        'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    ]);
}


    



    public function logout(Request $request)
    {
         $movimiento = new movimiento();
        $movimiento->id_usuario = auth()->user()->id_usuario;
        $movimiento->descripcion = 'ha cerrado sesión';
        $movimiento->save();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
       
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente');
    }

    public function preguntasSeguridad()
    {
        // Mostrar la vista para completar las preguntas de seguridad
        return view('seguridad.preguntas-seguridad'); // Esta es la vista para registrar las preguntas
    }

    public function storePreguntasSeguridad(Request $request)
    {
        $user = new User; // Obtén el usuario autenticado

        if ($user) {  // Verificamos que $user no sea nulo
            $user->respuesta_1 = $request->input('pregunta_1');
            $user->respuesta_2 = $request->input('pregunta_2');
            $user->respuesta_3 = $request->input('pregunta_3');
            $user->preguntas_completadas = true;
            
            // Guardar las respuestas y marcar las preguntas como completadas
            $user->save(); 
        }
        
}}
