<?php
namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;

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

        $datosTemporales = $this->getDatosTemporales();

        // Pasar los valores a la vista
        return view('home', compact('totalUsuarios', 'totalIncidencias', 'totalPeticiones', 'totalPersonas', 'datosTemporales'));
    }

    public function obtenerTotalPeticiones()
    {
        // Contar el número de usuarios con id_estado_usuario == 3
        $totalPeticiones = User::where('id_estado_usuario', 3)->count();
        return response()->json(['totalPeticiones' => $totalPeticiones]);
    }

    protected function getDatosTemporales()
    {
        $datos = Incidencia::selectRaw('
                DATE_FORMAT(incidencias.created_at, "%Y-%m-%d") as fecha,
                SUM(CASE WHEN estado_incidencia.nombre = "Atendido" THEN 1 ELSE 0 END) as atendidas,
                SUM(CASE WHEN estado_incidencia.nombre = "Pendiente" THEN 1 ELSE 0 END) as pendientes
            ')
            ->join('estados_incidencias as estado_incidencia', 'incidencias.id_estado_incidencia', '=', 'estado_incidencia.id_estado_incidencia')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $labels = [];
        $atendidas = [];
        $pendientes = [];

        foreach ($datos as $dato) {
            $labels[] = $dato->fecha;
            $atendidas[] = $dato->atendidas;
            $pendientes[] = $dato->pendientes;
        }

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Atendidas',
                    'data' => $atendidas,
                    'color' => '#28a745',
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4
                ],
                [
                    'name' => 'Pendientes',
                    'data' => $pendientes,
                    'color' => '#ffc107',
                    'borderColor' => '#ffc107',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4
                ]
            ]
        ];
    }
}
