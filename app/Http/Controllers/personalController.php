<?php

namespace App\Http\Controllers;

use App\Models\personalReparacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class personalController extends Controller
{
 public function buscar($cedula)
{
    $empleado = PersonalReparacion::where('cedula', $cedula)->first();

    if ($empleado) {
        return response()->json([
            'encontrado' => true,
            'nombre' => $empleado->nombre,
            'apellido' => $empleado->apellido,
            'telefono' => $empleado->telefono,
            'nacionalidad' => $empleado->nacionalidad,
        ]);
    }

    return response()->json(['encontrado' => false]);
}


}
