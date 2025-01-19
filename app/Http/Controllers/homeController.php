<?php

namespace App\Http\Controllers;

use App\Models\incidencia;
use App\Models\Persona;
use App\Models\Peticion;
use App\Models\User;
use Illuminate\Http\Request;


class homeController extends Controller
{
    public function index()
    {
        $totalUsuarios = User::count();
        $totalIncidencias = incidencia::count();
        $totalPeticiones = Peticion::count();
        $totalPersonas = Persona::count();

        return view('home', compact('totalUsuarios', 'totalIncidencias', 'totalPeticiones', 'totalPersonas'));
    }
}
