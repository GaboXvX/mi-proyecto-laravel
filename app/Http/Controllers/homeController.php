<?php
namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TipoIncidencia;
use App\Models\EstadoIncidencia;
use App\Models\NivelIncidencia;
use App\Models\Institucion;
use App\Models\personalReparacion;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function incidenciasTemporales(Request $request)
    {
        $query = \App\Models\Incidencia::query();
        if ($request->filled('tipo_incidencia_id')) {
            $query->where('id_tipo_incidencia', $request->tipo_incidencia_id);
        }
        if ($request->filled('nivel_incidencia_id')) {
            $query->where('id_nivel_incidencia', $request->nivel_incidencia_id);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        // Si no hay mes seleccionado, devolver el día con más incidencias de cada mes
        if (!$request->filled('mes') && $request->input('agrupado') !== 'mes') {
            $datos = $query->selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, DAY(created_at) as dia, COUNT(*) as total')
                ->groupBy('anio', 'mes', 'dia')
                ->orderBy('anio')
                ->orderBy('mes')
                ->orderBy('dia')
                ->get();
            $maximosPorMes = [];
            foreach ($datos as $dato) {
                $key = $dato->anio . '-' . str_pad($dato->mes, 2, '0', STR_PAD_LEFT);
                if (!isset($maximosPorMes[$key]) || $dato->total > $maximosPorMes[$key]['total']) {
                    $maximosPorMes[$key] = [
                        'mes' => $dato->mes,
                        'anio' => $dato->anio,
                        'total' => $dato->total
                    ];
                }
            }
            $labels = [];
            $data = [];
            foreach ($maximosPorMes as $key => $info) {
                $labels[] = __( [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ][$info['mes']-1] ) . ' ' . $info['anio'];
                $data[] = $info['total'];
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        // Agrupar por mes si se solicita
        if ($request->input('agrupado') === 'mes' && $request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $anio = date('Y', strtotime($request->fecha_inicio));
            $mesFin = date('n', strtotime($request->fecha_fin));
            $datos = $query->selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, COUNT(*) as total')
                ->groupBy('anio', 'mes')
                ->orderBy('anio')
                ->orderBy('mes')
                ->get();
            $data = array_fill(0, 12, 0);
            foreach ($datos as $dato) {
                $data[$dato->mes - 1] = $dato->total;
            }
            $data = array_slice($data, 0, $mesFin);
            return response()->json([
                'labels' => array_slice([
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ], 0, $mesFin),
                'data' => $data
            ]);
        } else {
            // Agrupación por día (original)
            $datos = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as fecha, COUNT(*) as total')
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get();
            $labels = [];
            $data = [];
            foreach ($datos as $dato) {
                $labels[] = $dato->fecha;
                $data[] = $dato->total;
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
    }

   public function incidenciasRecientes(Request $request)
{
    // Usar Eloquent para obtener relaciones y evitar problemas de nombres
    $query = \App\Models\incidencia::with([
        'tipoIncidencia', 
        'estadoIncidencia', 
        'nivelIncidencia', 
        'persona', 
        'direccionIncidencia.comunidad', // Cargar relación anidada
    ])->orderByDesc('created_at')->limit(10);
    
    if ($request->filled('tipo_incidencia_id')) {
        $query->where('id_tipo_incidencia', $request->tipo_incidencia_id);
    }
    if ($request->filled('fecha_inicio')) {
        $query->whereDate('created_at', '>=', $request->fecha_inicio);
    }
    if ($request->filled('fecha_fin')) {
        $query->whereDate('created_at', '<=', $request->fecha_fin);
    }
    
    $incidencias = $query->get();
    return response()->json($incidencias);
}

public function descargarGraficoPDF(Request $request)
{
    $imagenGrafico = $request->input('imagenGrafico');

    $tipo = $request->input('tipo_incidencia_id') ?: 'Todos';
    $nivel = $request->input('nivel_incidencia_id') ?: 'Todos';
    $mes = $request->input('mes') ?: 'Todos';
    $anio = $request->input('anio') ?: 'Todos';

    $institucion = Institucion::where('es_propietario', 1)->first();

    $logoBase64 = null;
    if ($institucion && $institucion->logo_path) {
        $logoPath = public_path('storage/' . $institucion->logo_path);
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }
    }

    $membrete = $institucion->encabezado_html ?? '';
    $pie_html = $institucion->pie_html ?? 'Generado el ' . now()->format('d/m/Y H:i:s');

    $pdf = Pdf::loadView('graficos.graficohome_pdf', [
        'imagenGrafico' => $imagenGrafico,
        'tipo' => $tipo,
        'nivel' => $nivel,
        'mes' => $mes,
        'anio' => $anio,
        'logoBase64' => $logoBase64,
        'membrete' => $membrete,
        'pie_html' => $pie_html,
    ])->setPaper('a4', 'landscape');

    return $pdf->download('grafico_incidencias.pdf');
}
}
