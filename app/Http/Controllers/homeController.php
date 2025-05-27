<?php
namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\personalReparacion;
use App\Models\Institucion;

class HomeController extends Controller
{
    public function index()
    {
        // Contar el número total de usuarios
        $totalUsuarios = User::count();

        // Contar el número total de incidencias
        $totalIncidencias = Incidencia::count();

        // Contar el número de usuarios con id_estado_usuario == 3 (pendientes de verificación)
        $totalPeticiones = User::where('id_estado_usuario', 3)->count();

        // Contar el número total de personas
        $totalPersonas = Persona::count();

        $totalPersonal = personalReparacion::count();

        $totalInstituciones = Institucion::count();

        // Pasar los valores a la vista
        return view('home', compact('totalUsuarios', 'totalIncidencias', 'totalPeticiones', 'totalPersonas', 'totalPersonal', 'totalInstituciones'));
    }

    public function obtenerTotalPeticiones()
    {
        // Contar el número de usuarios con id_estado_usuario == 3
        $totalPeticiones = User::where('id_estado_usuario', 3)->count();
        return response()->json(['totalPeticiones' => $totalPeticiones]);
    }
}
