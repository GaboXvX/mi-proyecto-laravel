<?php
namespace App\Http\Controllers;

use App\Models\incidencia;
use App\Models\incidencia_persona;
use App\Models\Persona;
use App\Models\Peticion;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $totalUsuarios = User::all()->count();

        // Contar el número total de incidencias
        $totalIncidencias = incidencia_persona::count();

        // Contar el número de usuarios con id_estado_usuario == 3 (pendientes de verificación)
        $totalPeticiones = User::where('id_estado_usuario', 3)->count();

        // Contar el número total de personas
        $totalPersonas = Persona::count();

        // Pasar los valores a la vista
        return view('home', compact('totalUsuarios', 'totalIncidencias', 'totalPeticiones', 'totalPersonas'));
    }

    public function obtenerTotalPeticiones()
    {
        $totalPeticiones = User::where('id_estado_usuario', 3)->count();
        return response()->json(['totalPeticiones' => $totalPeticiones]);
    }
}
