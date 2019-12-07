<?php

namespace App\Http\Controllers;

use App\Models\EstadoIncidencia;
use App\Models\Incidencia;
use App\Models\NivelIncidencia;
use App\Models\TipoIncidencia;
use App\Models\Institucion;
use App\Models\InstitucionEstacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GraficoIncidenciasController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('es');
        
        // Validación de fechas con valores por defecto
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->subMonth()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()))->endOfDay();
        
        // Validar fechas
        if ($startDate > $endDate) {
            return back()->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin');
        }
        
        // Obtener parámetros de filtro
        $filters = [
            'tipo_incidencia_id' => $request->input('tipo_incidencia_id'),
            'institucion_id' => $request->input('institucion_id'),
            'estacion_id' => $request->input('estacion_id'),
            'nivel_incidencia_id' => $request->input('nivel_incidencia_id')
        ];

        // Obtener datos para el gráfico
        $chartData = $this->getChartData($startDate, $endDate, $filters);

        return view('graficos.incidencias', array_merge($chartData, [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'tiposIncidencia' => TipoIncidencia::orderBy('nombre')->get(),
            'nivelesIncidencia' => NivelIncidencia::where('activo', true)->orderBy('id_nivel_incidencia')->get(),
            'instituciones' => Institucion::orderBy('nombre')->get(),
            'estaciones' => $filters['institucion_id'] 
                ? InstitucionEstacion::where('id_institucion', $filters['institucion_id'])->orderBy('nombre')->get()
                : collect(),
            'filters' => $filters
        ]));
    }

    protected function getChartData($startDate, $endDate, $filters)
{
    // Consulta base para todas las incidencias en el rango de fechas
    $query = Incidencia::with([
            'estadoIncidencia', 
            'institucion', 
            'estacion', 
            'tipoIncidencia',
            'nivelIncidencia'
        ])
        ->whereBetween('incidencias.created_at', [$startDate, $endDate]);

    // Aplicar filtros
    $this->applyFilters($query, $filters);

    // Obtener datos agrupados por estado
    $incidenciasPorEstado = $this->getIncidenciasPorEstado($query);
    
    // Obtener datos agrupados por nivel
    $incidenciasPorNivel = $this->getIncidenciasPorNivel($query);
    
    // Obtener datos para gráfico temporal
    $datosTemporales = $this->getDatosTemporales($query);

    // Obtener lista de incidencias pendientes
    $listaPendientes = (clone $query)
        ->whereHas('estadoIncidencia', fn($q) => $q->where('nombre', 'Pendiente'))
        ->orderBy('fecha_vencimiento')
        ->get();

    return [
        'incidenciasPorEstado' => $incidenciasPorEstado,
        'incidenciasPorNivel' => $incidenciasPorNivel,
        'datosTemporales' => $datosTemporales,
        'totalIncidencias' => $query->count(),
        'incidenciasAtendidas' => $query->whereHas('estadoIncidencia', fn($q) => $q->where('nombre', 'Atendido'))->count(),
        'incidenciasPendientes' => $listaPendientes->count(),
        'incidenciasPorVencer' => $this->getIncidenciasPorVencer($startDate, $endDate, $filters),
        'listaPendientes' => $listaPendientes // Aquí agregamos las incidencias pendientes
    ];
}


    protected function applyFilters(&$query, $filters)
    {
        // Filtro por tipo de incidencia
        if (!empty($filters['tipo_incidencia_id'])) {
            $query->where('id_tipo_incidencia', $filters['tipo_incidencia_id']);
        }

        // Filtro por institución
        if (!empty($filters['institucion_id'])) {
            $query->where('id_institucion', $filters['institucion_id']);
        }

        // Filtro por estación
        if (!empty($filters['estacion_id'])) {
            $query->where('id_institucion_estacion', $filters['estacion_id']);
        }

        // Filtro por nivel de incidencia
        if (!empty($filters['nivel_incidencia_id'])) {
            $query->where('id_nivel_incidencia', $filters['nivel_incidencia_id']);
        }
    }

   protected function getIncidenciasPorEstado($query)
{
    $estados = EstadoIncidencia::orderBy('id_estado_incidencia')->get();
    $niveles = NivelIncidencia::where('activo', true)->orderBy('id_nivel_incidencia')->get();

    $data = [
        'labels' => [], // Asegurarse de inicializar el array de labels
        'values' => [], // Asegurarse de inicializar el array de values
        'colors' => [], // Asegurarse de inicializar el array de colors
        'detalles' => []
    ];

    foreach ($estados as $estado) {
        $count = (clone $query)->where('id_estado_incidencia', $estado->id_estado_incidencia)->count();
        
        if ($count > 0) {
            $data['labels'][] = $estado->nombre;
            $data['values'][] = $count;
            $data['colors'][] = $this->getEstadoColor($estado->nombre);
            
            // Detalle por niveles
            foreach ($niveles as $nivel) {
                $data['detalles'][$estado->nombre][$nivel->nombre] = 
                    (clone $query)
                        ->where('id_estado_incidencia', $estado->id_estado_incidencia)
                        ->where('id_nivel_incidencia', $nivel->id_nivel_incidencia)
                        ->count();
            }
        }
    }

    return $data;
}

protected function getIncidenciasPorNivel($query)
{
    $niveles = NivelIncidencia::where('activo', true)->orderBy('id_nivel_incidencia')->get();

    $data = [
        'labels' => [], // Inicializa el array de labels
        'values' => [], // Inicializa el array de values
        'colors' => []  // Inicializa el array de colors
    ];

    foreach ($niveles as $nivel) {
        $count = (clone $query)->where('id_nivel_incidencia', $nivel->id_nivel_incidencia)->count();
        
        if ($count > 0) {
            $data['labels'][] = $nivel->nombre;
            $data['values'][] = $count;
            $data['colors'][] = $nivel->color ?? $this->getNivelColor($nivel->nombre);
        }
    }

    return $data;
}


   protected function getDatosTemporales($query)
{
    $start = $query->getQuery()->wheres[0]['values'][0] ?? now()->subMonth();
    $end = $query->getQuery()->wheres[0]['values'][1] ?? now();

    $datos = (clone $query)
        ->selectRaw('
            DATE_FORMAT(incidencias.created_at, "%Y-%m-%d") as fecha,
            SUM(CASE WHEN estado_incidencia.nombre = "Atendido" THEN 1 ELSE 0 END) as atendidas,
            SUM(CASE WHEN estado_incidencia.nombre = "Pendiente" THEN 1 ELSE 0 END) as pendientes
        ')
        ->join('estados_incidencias as estado_incidencia', 'incidencias.id_estado_incidencia', '=', 'estado_incidencia.id_estado_incidencia')
        ->whereBetween('incidencias.created_at', [$start, $end])
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

    protected function getIncidenciasPorVencer($startDate, $endDate, $filters)
    {
        $query = Incidencia::where('fecha_vencimiento', '<=', Carbon::now()->addMinutes(15))
            ->whereBetween('incidencias.created_at', [$startDate, $endDate])
            ->whereHas('estadoIncidencia', fn($q) => $q->where('nombre', 'Pendiente'));

        $this->applyFilters($query, $filters);

        return $query->count();
    }

    protected function getEstadoColor($estado)
    {
        $colores = [
            'Atendido' => '#28a745',
            'Pendiente' => '#ffc107',
            'En proceso' => '#17a2b8',
            'Cancelado' => '#6c757d',
            'Rechazado' => '#dc3545'
        ];

        return $colores[$estado] ?? '#007bff';
    }

    protected function getNivelColor($nivel)
    {
        $colores = [
            'Alto' => '#dc3545',
            'Medio' => '#fd7e14',
            'Bajo' => '#28a745',
            'Informativo' => '#17a2b8'
        ];

        return $colores[$nivel] ?? '#6c757d';
    }
}
