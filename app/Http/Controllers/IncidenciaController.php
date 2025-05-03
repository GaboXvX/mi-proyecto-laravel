<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\categoriaExclusivaPersona;
use App\Models\Direccion;
use App\Models\Estado;
use App\Models\incidencia;
use App\Models\incidencia_persona;
use App\Models\IncidenciaGeneral;
use App\Models\Institucion;
use App\Models\InstitucionEstacion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\Notificacion;
use App\Models\Persona;
use App\Models\ReparacionIncidencia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncidenciaController extends Controller
{


    public function index(Request $request)
{
    try {
        $incidencias = Incidencia::with(['persona', 'direccion', 'usuario.empleadoAutorizado'])
            ->when($request->codigo, function ($query, $codigo) {
                return $query->where('cod_incidencia', 'like', "%$codigo%");
            })
            ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                return $query->whereBetween('created_at', [
                    Carbon::parse($request->fecha_inicio)->startOfDay(),
                    Carbon::parse($request->fecha_fin)->endOfDay()
                ]);
            })
            ->when($request->estado && $request->estado !== 'Todos', function ($query) use ($request) {
                return $query->where('estado', $request->estado);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('incidencias.listaincidencias', compact('incidencias'));
    } catch (\Exception $e) {
        Log::error('Error al cargar la lista de incidencias:', ['error' => $e->getMessage()]);
        return back()->withErrors(['error' => 'Error al cargar la lista de incidencias.']);
    }
}

    public function crear($slug)
    {
        $persona = Persona::where('slug', $slug)->first();
        $instituciones = Institucion::all(); // Obtener todas las instituciones

        return view('incidencias.registrarIncidencia', compact('persona', 'instituciones'));
    }


    public function create()
    {
        // Cargar datos necesarios para la vista, como instituciones o direcciones
        $instituciones = Institucion::all();
        $direcciones = Direccion::all();
        $estados = Estado::all();
        // Retornar la vista para registrar una incidencia general
        return view('incidencias.registrarIncidenciaGeneral', compact('instituciones', 'direcciones', 'estados'));
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos del request
            $request->validate([
                'id_persona' => 'nullable|exists:personas,id_persona',
                'calle' => 'required|string|max:255',
                'punto_de_referencia' => 'nullable|string|max:255',
                'estado' => 'required|exists:estados,id_estado',
                'municipio' => 'required|exists:municipios,id_municipio',
                'parroquia' => 'required|exists:parroquias,id_parroquia',
                'urbanizacion' => 'nullable|exists:urbanizaciones,id_urbanizacion',
                'sector' => 'nullable|exists:sectores,id_sector',
                'tipo_incidencia' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'nivel_prioridad' => 'required|integer|min:1|max:5',
                'institucion' => 'required|exists:instituciones,id_institucion',
                'estacion' => 'nullable|exists:instituciones_estaciones,id_institucion_estacion',
            ]);

            // Buscar o crear la dirección
            $direccion = Direccion::firstOrCreate(
                [
                    'calle' => $request->input('calle'),
                    'id_estado' => $request->input('estado'),
                    'id_municipio' => $request->input('municipio'),
                    'id_parroquia' => $request->input('parroquia'),
                    'id_urbanizacion' => $request->input('urbanizacion'),
                    'id_sector' => $request->input('sector'),
                    'punto_de_referencia' => $request->input('punto_de_referencia'),
                ]
            );

            // Generar un slug único
            $slug = Str::slug(Str::lower($request->input('descripcion')));
            $originalSlug = $slug;
            $counter = 1;

            while (Incidencia::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Generar un código único
            $codigo = Str::random(8);
            while (Incidencia::where('cod_incidencia', $codigo)->exists()) {
                $codigo = Str::random(8);
            }

            // Crear la incidencia
            $incidencia = new Incidencia();
            $incidencia->id_persona = $request->input('id_persona'); // Puede ser null
            $incidencia->slug = $slug;
            $incidencia->cod_incidencia = $codigo;
            $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
            $incidencia->descripcion = Str::lower($request->input('descripcion'));
            $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
            $incidencia->estado = 'Por atender';
            $incidencia->id_direccion = $direccion->id_direccion;
            $incidencia->id_usuario = auth()->id();
            $incidencia->id_institucion = $request->input('institucion');
            $incidencia->id_institucion_estacion = $request->input('estacion');
            $incidencia->save();

            // Registrar movimiento
            movimiento::create([
                'id_incidencia' => $incidencia->id_incidencia,
                'id_usuario' => auth()->id(),
                'descripcion' => 'Se registró una incidencia',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Incidencia registrada correctamente.',
                'redirect_url' => route('incidencias.show', $incidencia->slug),
            ]);
        } catch (\Exception $e) {
            Log::error('Error al registrar incidencia:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la incidencia: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function descargar($slug)
    {
        try {
            // Buscar la incidencia
            $incidencia = Incidencia::with(['persona', 'direccion.estado', 'direccion.municipio', 'direccion.parroquia', 'direccion.urbanizacion', 'direccion.sector', 'institucion', 'institucionEstacion'])
                ->where('slug', $slug)
                ->first();

            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada.');
            }

            // Generar el PDF
            $pdf = FacadePdf::loadView('incidencias.incidencia_pdf', compact('incidencia'))
                ->setPaper('a4', 'portrait');

            return $pdf->download('incidencia-' . $incidencia->cod_incidencia . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar el PDF de la incidencia:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el PDF de la incidencia: ' . $e->getMessage(),
            ], 500);
        }
    }





    public function edit($slug)
    {
        try {
            // Buscar la incidencia
            $incidencia = Incidencia::with(['direccion', 'institucion', 'institucionEstacion'])
                ->where('slug', $slug)
                ->first();

            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada.');
            }

            // Obtener todas las instituciones y estaciones
            $instituciones = Institucion::all();
            $estaciones = InstitucionEstacion::all();

            // Retornar la vista con los datos necesarios
            return view('incidencias.editarIncidencia', compact('incidencia', 'instituciones', 'estaciones'));
        } catch (\Exception $e) {
            Log::error('Error al cargar la vista de edición:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al cargar la incidencia para editar.']);
        }
    }


    public function update(Request $request, $slug)
    {
        try {
            // Buscar la incidencia
            $incidencia = Incidencia::where('slug', $slug)->first();

            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incidencia no encontrada.',
                ], 404);
            }

            // Validar los datos del request
            $validatedData = $request->validate([
                'id_persona' => 'nullable|exists:personas,id_persona',
                'calle' => 'required|string|max:255',
                'punto_de_referencia' => 'nullable|string|max:255',
                'estado' => 'required|exists:estados,id_estado',
                'municipio' => 'required|exists:municipios,id_municipio',
                'parroquia' => 'required|exists:parroquias,id_parroquia',
                'urbanizacion' => 'nullable|exists:urbanizaciones,id_urbanizacion',
                'sector' => 'nullable|exists:sectores,id_sector',
                'tipo_incidencia' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'nivel_prioridad' => 'required|integer|min:1|max:5',
                'institucion' => 'required|exists:instituciones,id_institucion',
                'estacion' => 'nullable|exists:instituciones_estaciones,id_institucion_estacion',
            ]);

            // Buscar o crear la dirección
            $direccion = Direccion::firstOrCreate(
                [
                    'calle' => $request->input('calle'),
                    'id_estado' => $request->input('estado'),
                    'id_municipio' => $request->input('municipio'),
                    'id_parroquia' => $request->input('parroquia'),
                    'id_urbanizacion' => $request->input('urbanizacion'),
                    'id_sector' => $request->input('sector'),
                     'id_comunidad' => $request->input('comunidad'),
                    'punto_de_referencia' => $request->input('punto_de_referencia'),
                ]
            );
            // Generar un nuevo slug único
            $slug = Str::slug(Str::lower($request->input('descripcion')));
            $originalSlug = $slug;
            $counter = 1;
            while (Incidencia::where('slug', $slug)->where('id_incidencia', '!=', $incidencia->id_incidencia)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            // Actualizar los datos de la incidencia
            $incidencia->update([
                'slug' => $slug,
                'tipo_incidencia' => $request->input('tipo_incidencia'),
                'descripcion' => Str::lower($request->input('descripcion')),
                'nivel_prioridad' => $request->input('nivel_prioridad'),
                'id_direccion' => $direccion->id_direccion,
                'id_usuario' => auth()->id(),
                'id_institucion' => $request->input('institucion'),
                'id_institucion_estacion' => $request->input('estacion'),
            ]);

            // Registrar movimiento
            movimiento::create([
                'id_incidencia' => $incidencia->id_incidencia,
                'id_usuario' => auth()->id(),
                'descripcion' => 'Se actualizó una incidencia',
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Incidencia actualizada correctamente.',
                'redirect_url' => route('incidencias.show', $incidencia->slug),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar la incidencia:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => '⚠️ Error al actualizar la incidencia: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function atenderGuardar(Request $request, $slug)
    {
        $request->validate([
            'descripcion' => 'required|string|max:1000',
            'prueba_fotografica' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Buscar la incidencia
            $incidencia = Incidencia::where('slug', $slug)->first();

            if (!$incidencia) {
                return response()->json([
                    'message' => 'Incidencia no encontrada.'
                ], 404);
            }

            // Subir la prueba fotográfica
            $rutaPrueba = $request->file('prueba_fotografica')->store('pruebas', 'public');

            // Crear registro de reparación
            ReparacionIncidencia::create([
                'id_incidencia' => $incidencia->id_incidencia,
                'descripcion' => $request->input('descripcion'),
                'prueba_fotografica' => $rutaPrueba,
                'slug' => $slug,
                'id_usuario' => auth()->id(),
            ]);

            // Actualizar estado
            $incidencia->estado = 'Atendido';
            $incidencia->save();

            // Registrar movimiento
            movimiento::create([
                'id_incidencia' => $incidencia->id_incidencia,
                'id_usuario' => auth()->id(),
                'descripcion' => 'Incidencia atendida con prueba fotográfica',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Incidencia atendida correctamente.',
                'redirect' => route('incidencias.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al atender la incidencia:', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => $e->getMessage() ?? 'Error al atender la incidencia.'
            ], 500);
        }
    }

    public function filtrar(Request $request)
    {
        try {
            $validated = $request->validate([
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date',
                'estado' => 'nullable|string|in:Atendido,Por atender,Todos',
                'codigo' => 'nullable|string|max:20'
            ]);
    
            $incidencias = Incidencia::with(['persona', 'direccion', 'usuario.empleadoAutorizado'])
                ->when($request->codigo, function ($query, $codigo) {
                    return $query->where('cod_incidencia', 'like', "%$codigo%");
                })
                ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                    return $query->whereBetween('created_at', [
                        Carbon::parse($request->fecha_inicio)->startOfDay(),
                        Carbon::parse($request->fecha_fin)->endOfDay()
                    ]);
                })
                ->when($request->estado && $request->estado !== 'Todos', function ($query) use ($request) {
                    return $query->where('estado', $request->estado);
                })
                ->orderBy('created_at', 'desc')
                ->get();
    
            return response()->json([
                'success' => true,
                'incidencias' => $incidencias,
                'fecha_actualizacion' => now()->format('d-m-Y H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Error al filtrar las incidencias:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al filtrar las incidencias: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function generarPDF(Request $request)
    {
        try {
            // Obtener las fechas del request con valores por defecto (todas las incidencias si no se especifican fechas)
            $fechaInicio = $request->input('fecha_inicio') ?: Incidencia::min('created_at');
            $fechaFin = $request->input('fecha_fin') ?: Incidencia::max('created_at');
            $estado = $request->input('estado', 'Todos');

            // Validar las fechas
            if (!$fechaInicio || !$fechaFin) {
                return back()->withErrors(['error' => 'Debe seleccionar un rango de fechas válido.']);
            }

            // Obtener las incidencias filtradas
            $incidencias = Incidencia::with(['persona', 'direccion', 'usuario.empleadoAutorizado'])
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->when($estado !== 'Todos', function ($query) use ($estado) {
                    return $query->where('estado', $estado);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Verificar si hay incidencias para generar el PDF
            if ($incidencias->isEmpty()) {
                return back()->withErrors(['error' => 'No se encontraron incidencias para el rango de fechas seleccionado.']);
            }

            // Generar el PDF con la tabla completa
            $pdf = FacadePdf::loadView('incidencias.pdf_table', compact('incidencias', 'fechaInicio', 'fechaFin', 'estado'));

            return $pdf->download('reporte_incidencias_' . $fechaInicio . '_a_' . $fechaFin . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar el PDF de incidencias:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al generar el PDF de incidencias.']);
        }
    }


    public function show($slug)
    {
        try {
            // Buscar la incidencia
            $incidencia = Incidencia::with(['persona', 'usuario.empleadoAutorizado'])
                ->where('slug', $slug)
                ->first();
    
            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada');
            }
    
            // Si la incidencia está vinculada a una persona, cargar la información de la persona
            $persona = $incidencia->id_persona ? $incidencia->persona : null;
    
            // Retornar la vista con la incidencia y, si aplica, la persona
            return view('incidencias.incidencia', compact('incidencia', 'persona'));
        } catch (\Exception $e) {
            Log::error('Error en IncidenciaController@show: ' . $e->getMessage());
            abort(500, 'Ocurrió un error al mostrar la incidencia');
        }
    }

    public function ver($slug)
    {
        try {
            // Buscar la incidencia con las relaciones necesarias
            $incidencia = Incidencia::with(['persona', 'direccion', 'institucion', 'institucionEstacion', 'usuario'])
                ->where('slug', $slug)
                ->first();

            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada.');
            }

            // Obtener la reparación asociada si la incidencia está atendida
            $reparacion = null;
            if ($incidencia->estado === 'Atendido') {
                $reparacion = ReparacionIncidencia::where('id_incidencia', $incidencia->id_incidencia)
                    ->with('usuario') // Cargar el usuario que realizó la reparación
                    ->first();
            }

            return view('incidencias.verIncidencia', compact('incidencia', 'reparacion'));
        } catch (\Exception $e) {
            Log::error('Error al cargar los detalles de la incidencia:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al cargar los detalles de la incidencia.']);
        }
    }

    public function showChart(Request $request)
    {
        Carbon::setLocale('es');
    
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfYear()));
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()));
        $tipoIncidencia = $request->input('tipo_incidencia', '');
        $denunciante = $request->input('denunciante', '');
    
        // Consulta para incidencias atendidas
        $queryAtendidas = Incidencia::where('estado', 'Atendido')
            ->whereBetween('created_at', [$startDate, $endDate]);
    
        // Consulta para total de incidencias (para calcular porcentaje)
        $queryTotal = Incidencia::whereBetween('created_at', [$startDate, $endDate]);
    
        if ($tipoIncidencia) {
            $queryAtendidas->where('tipo_incidencia', $tipoIncidencia);
            $queryTotal->where('tipo_incidencia', $tipoIncidencia);
        }
    
        if ($denunciante === 'con') {
            $queryAtendidas->whereNotNull('id_persona');
            $queryTotal->whereNotNull('id_persona');
        } elseif ($denunciante === 'sin') {
            $queryAtendidas->whereNull('id_persona');
            $queryTotal->whereNull('id_persona');
        }
    
        // Obtener datos mensuales de incidencias atendidas
        $incidenciasAtendidas = $queryAtendidas->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    
        // Obtener totales mensuales para calcular porcentajes
        $totalesMensuales = $queryTotal->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . $item->month;
            });
    
        $labels = [];
        $dataAtendidas = [];
        $porcentajes = [];
    
        foreach ($incidenciasAtendidas as $incidencia) {
            $monthName = Carbon::createFromFormat('m', $incidencia->month)->locale('es')->isoFormat('MMMM');
            $labels[] = $monthName . ' ' . $incidencia->year;
            $dataAtendidas[] = $incidencia->total;
            
            // Calcular porcentaje
            $key = $incidencia->year . '-' . $incidencia->month;
            $totalMes = $totalesMensuales[$key]->total ?? 0;
            $porcentaje = $totalMes > 0 ? round(($incidencia->total / $totalMes) * 100, 2) : 0;
            $porcentajes[] = $porcentaje;
        }
    
        return view('incidencias.grafica_incidencia_resueltas', compact(
            'labels', 
            'dataAtendidas', 
            'porcentajes',
            'startDate', 
            'endDate', 
            'tipoIncidencia',
            'denunciante'
        ));
    }

    public function buscar(Request $request)
    {
        $codigo = $request->input('buscar');
        $incidencia = Incidencia::where('cod_incidencia', $codigo)->first();

        if (!$incidencia) {
            return back()->withErrors(['error' => 'No se encontró ninguna incidencia con el código proporcionado.']);
        }

        if (url()->previous() == route('incidencias.gestionar')) {
            return view('incidencias.gestionincidencias')->with('incidencias', [$incidencia]);
        }

        return view('incidencias.listaincidencias')->with('incidencias', [$incidencia]);
    }
    public function filtrarPorFechas(Request $request)
{
    $request->validate([
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
    ]);

    try {
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();

        // Obtener las incidencias filtradas
        $incidencias = Incidencia::with(['persona', 'direccion', 'usuario.empleadoAutorizado'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'incidencias' => $incidencias,
        ]);
    } catch (\Exception $e) {
        Log::error('Error al filtrar incidencias por fechas:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error al filtrar incidencias: ' . $e->getMessage(),
        ], 500);
    }
}


    public function getEstacionesPorEstadoEInstitucion($estadoId, $institucionId)
    {
        try {
            // Obtener las estaciones relacionadas con el estado y la institución
            $estaciones = InstitucionEstacion::where('id_institucion', $institucionId)
                ->whereHas('municipio', function ($query) use ($estadoId) {
                    $query->where('id_estado', $estadoId);
                })
                ->with('municipio') // Cargar el municipio relacionado
                ->get();

            return response()->json([
                'success' => true,
                'estaciones' => $estaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estaciones: ' . $e->getMessage(),
            ], 500);
        }
    }

    
    public function atenderVista($slug)
    {
        try {
            // Buscar la incidencia
            $incidencia = Incidencia::with(['direccion', 'institucion', 'institucionEstacion'])
                ->where('slug', $slug)
                ->first();

            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada.');
            }

            return view('incidencias.atenderIncidencia', compact('incidencia'));
        } catch (\Exception $e) {
            Log::error('Error al cargar la vista de atención:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al cargar la incidencia para atender.']);
        }
    }

   
}
