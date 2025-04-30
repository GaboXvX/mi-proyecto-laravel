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
        // Obtener incidencias de personas
        $incidenciasPersonas = incidencia_persona::with(['usuario.empleadoAutorizado', 'categoriaExclusiva.persona'])
            ->when($request->codigo, function ($query, $codigo) {
                return $query->where('cod_incidencia', 'like', "%$codigo%");
            })
            ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
            })
            ->when($request->estado && $request->estado !== 'Todos', function ($query, $estado) {
                return $query->where('estado', $estado);
            })
            ->select('slug', 'id_incidencia_p as id', 'cod_incidencia', 'tipo_incidencia', 'descripcion', 'nivel_prioridad', 'estado', 'created_at', 'id_usuario', DB::raw("'persona' as tipo"))
            ->orderBy('created_at', 'desc');

        // Obtener incidencias generales
        $incidenciasGenerales = IncidenciaGeneral::with(['usuario'])
            ->when($request->codigo, function ($query, $codigo) {
                return $query->where('cod_incidencia', 'like', "%$codigo%");
            })
            ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
            })
            ->when($request->estado && $request->estado !== 'Todos', function ($query, $estado) {
                return $query->where('estado', $estado);
            })
            ->select('slug', 'id_incidencia_g as id', 'cod_incidencia', 'tipo_incidencia', 'descripcion', 'nivel_prioridad', 'estado', 'created_at', 'id_usuario', DB::raw("'general' as tipo"))
            ->orderBy('created_at', 'desc');

        // Combinar ambas consultas
        $incidencias = $incidenciasPersonas->union($incidenciasGenerales)->orderBy('created_at', 'desc')->get();

        // Retornar la vista con las incidencias
        return view('incidencias.listaincidencias', compact('incidencias'));
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
            // Verificar si la incidencia está vinculada directamente a una persona
            if ($request->filled('id_persona')) {
                return $this->storeIncidenciaVinculada($request);
            }

            // Verificar si la incidencia tiene una persona de contacto
            if ($request->filled('cedula_persona')) {
                return $this->storeIncidenciaConContacto($request);
            }

            // Si no cumple ninguna de las condiciones anteriores, es una incidencia general
            return $this->storeIncidenciaGeneral($request);
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
            // Buscar la incidencia en ambas tablas
            $incidencia = incidencia_persona::with(['persona', 'direccion.estado', 'direccion.municipio', 'direccion.parroquia', 'direccion.urbanizacion', 'direccion.sector', 'institucion', 'institucionEstacion'])
                ->where('slug', $slug)->first()
                ?? IncidenciaGeneral::with(['direccion.estado', 'direccion.municipio', 'direccion.parroquia', 'direccion.urbanizacion', 'direccion.sector', 'institucion', 'institucionEstacion'])
                ->where('slug', $slug)->first();

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
            // Buscar la incidencia en ambas tablas
            $incidencia = incidencia_persona::with(['direccion', 'institucion', 'institucionEstacion'])
                ->where('slug', $slug)
                ->first()
                ?? IncidenciaGeneral::with(['direccion', 'institucion', 'institucionEstacion'])
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


   public function update(StoreIncidenciaRequest $request, $slug)
{
    try {
        // Determinar si la incidencia es vinculada o general
        $incidencia = incidencia_persona::where('slug', $slug)->first()
            ?? IncidenciaGeneral::where('slug', $slug)->first();

        if (!$incidencia) {
            return response()->json([
                'success' => false,
                'message' => 'Incidencia no encontrada.',
            ], 404);
        }

        // Llamar al método correspondiente según el tipo de incidencia
        if ($incidencia instanceof incidencia_persona) {
            $result = $this->updateIncidenciaVinculada($request, $incidencia);
        } elseif ($incidencia instanceof IncidenciaGeneral) {
            $result = $this->updateIncidenciaGeneral($request, $incidencia);
        }

        // Verificar si hubo éxito en la operación
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Error al actualizar la incidencia',
            ], 500);
        }

        // Redirección según el tipo de incidencia
        return response()->json([
            'success' => true,
            'message' => '✅ Incidencia actualizada correctamente.',
            'redirect_url' => $result['redirect_url'] ?? route('incidencias.index'),
        ]);

    } catch (\Exception $e) {
        Log::error('Error al actualizar la incidencia:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => '⚠️ Error al actualizar la incidencia: ' . $e->getMessage(),
        ], 500);
    }
}





    public function atender($slug)
    {
       
    }

    public function atenderGuardar(Request $request, $slug)
    {
        $request->validate([
            'descripcion' => 'required|string|max:1000',
            'prueba_fotografica' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        try {
            // Buscar la incidencia
            $incidencia = incidencia_persona::where('slug', $slug)->first()
                ?? IncidenciaGeneral::where('slug', $slug)->first();
    
            if (!$incidencia) {
                return response()->json([
                    'message' => 'Incidencia no encontrada.'
                ], 404);
            }
    
            // Subir la prueba fotográfica
            $rutaPrueba = $request->file('prueba_fotografica')->store('pruebas', 'public');
    
            // Crear registro de reparación
            ReparacionIncidencia::create([
                'id_incidencia_p' => $incidencia->id_incidencia_p,
                'id_incidencia_g' => $incidencia->id_incidencia_g,
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
                'id_incidencia' => $incidencia->id ?? $incidencia->id_incidencia_p,
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
        $validated = $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'estado' => 'nullable|string|in:Atendido,Por atender,Todos',
        ]);
    
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $estado = $request->input('estado', 'Todos');
    
        // Obtener incidencias de personas
        $incidenciasPersonas = incidencia_persona::with(['usuario.empleadoAutorizado', 'categoriaExclusiva.persona', 'categoriaExclusiva.categoria'])
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                return $query->whereBetween('created_at', [
                    Carbon::parse($fechaInicio)->startOfDay(),
                    Carbon::parse($fechaFin)->endOfDay()
                ]);
            })
            ->when($estado !== 'Todos', function ($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->select('slug', 'id_incidencia_p as id', 'cod_incidencia', 'tipo_incidencia', 'descripcion', 'nivel_prioridad', 'estado', 'created_at', 'id_usuario', DB::raw("'persona' as tipo"));
    
        // Obtener incidencias generales
        $incidenciasGenerales = IncidenciaGeneral::with(['usuario.empleadoAutorizado'])
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                return $query->whereBetween('created_at', [
                    Carbon::parse($fechaInicio)->startOfDay(),
                    Carbon::parse($fechaFin)->endOfDay()
                ]);
            })
            ->when($estado !== 'Todos', function ($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->select('slug', 'id_incidencia_g as id', 'cod_incidencia', 'tipo_incidencia', 'descripcion', 'nivel_prioridad', 'estado', 'created_at', 'id_usuario', DB::raw("'general' as tipo"));
    
        // Combinar ambas consultas
        $incidencias = $incidenciasPersonas->union($incidenciasGenerales)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json(['incidencias' => $incidencias]);
    }

    public function generarPDF(Request $request)
    {
        // Obtener las fechas del request con valores por defecto (todas las incidencias si no se especifican fechas)
        $fechaInicio = $request->input('fecha_inicio') ?: incidencia_persona::min('created_at');
        $fechaFin = $request->input('fecha_fin') ?: incidencia_persona::max('created_at');
        $estado = $request->input('estado', 'Todos');

        // Validar las fechas
        if (!$fechaInicio || !$fechaFin) {
            return back()->withErrors(['error' => 'Debe seleccionar un rango de fechas válido.']);
        }

        // Obtener incidencias de personas
        $incidenciasPersonas = incidencia_persona::with(['persona', 'categoriaExclusiva.persona', 'categoriaExclusiva.categoria'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->when($estado !== 'Todos', function ($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->select('slug', 'cod_incidencia', 'tipo_incidencia', 'descripcion', 'nivel_prioridad', 'estado', 'created_at', 'id_usuario', DB::raw("'persona' as tipo"));

        // Obtener incidencias generales
        $incidenciasGenerales = IncidenciaGeneral::with(['usuario.empleadoAutorizado'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->when($estado !== 'Todos', function ($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->select('slug', 'cod_incidencia', 'tipo_incidencia', 'descripcion', 'nivel_prioridad', 'estado', 'created_at', 'id_usuario', DB::raw("'general' as tipo"));

        // Combinar ambas consultas
        $incidencias = $incidenciasPersonas->union($incidenciasGenerales)
            ->orderBy('created_at', 'desc')
            ->get();

        // Verificar si hay incidencias para generar el PDF
        if ($incidencias->isEmpty()) {
            return back()->withErrors(['error' => 'No se encontraron incidencias para el rango de fechas seleccionado.']);
        }

        // Generar el PDF con la tabla completa
        $pdf = FacadePdf::loadView('incidencias.pdf_table', compact('incidencias', 'fechaInicio', 'fechaFin', 'estado'));

        return $pdf->download('reporte_incidencias_' . $fechaInicio . '_a_' . $fechaFin . '.pdf');
    }


    public function show($slug)
    {
        try {
            // Buscar la incidencia en ambas tablas
            $incidencia = incidencia_persona::with('persona', 'usuario.empleadoAutorizado')
                ->where('slug', $slug)
                ->first()
                ?? IncidenciaGeneral::with('usuario.empleadoAutorizado')
                ->where('slug', $slug)
                ->first();
    
            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada');
            }
    
            // Si la incidencia está vinculada a una persona, cargar la información de la persona
            $persona = $incidencia instanceof incidencia_persona ? $incidencia->persona : null;
    
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
            // Buscar la incidencia
            $incidencia = incidencia_persona::where('slug', $slug)->first()
                ?? IncidenciaGeneral::where('slug', $slug)->first();
    
            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada.');
            }
    
            // Buscar la reparación asociada (según el tipo de incidencia)
            $reparacion = null;
    
            if ($incidencia instanceof incidencia_persona) {
                $reparacion = ReparacionIncidencia::where('id_incidencia_p', $incidencia->id_incidencia_p)->first();
            } elseif ($incidencia instanceof IncidenciaGeneral) {
                $reparacion = ReparacionIncidencia::where('id_incidencia_g', $incidencia->id_incidencia_g)->first();
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


        $queryAtendidas = incidencia_persona::where('estado', 'Atendido')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($tipoIncidencia) {
            $queryAtendidas->where('tipo_incidencia', $tipoIncidencia);
        }


        $incidenciasAtendidas = $queryAtendidas->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        $labels = [];
        $dataAtendidas = [];


        foreach ($incidenciasAtendidas as $incidencia) {

            $monthName = Carbon::createFromFormat('m', $incidencia->month)->locale('es')->isoFormat('MMMM');
            $labels[] = $monthName . ' ' . $incidencia->year;
            $dataAtendidas[] = $incidencia->total;
        }


        return view('incidencias.grafica_incidencia_resueltas', compact('labels', 'dataAtendidas', 'startDate', 'endDate', 'tipoIncidencia'));
    }
    public function buscar(Request $request)
    {
        $codigo = $request->input('buscar');
        $incidencia = incidencia_persona::where('cod_incidencia', $codigo)->first();
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


        $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();


        $incidencias = incidencia_persona::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'por atender')
            ->get();


        return response()->json([
            'incidencias' => $incidencias
        ]);
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

    public function storeIncidenciaVinculada(Request $request)
{
    try {
        Log::info('Datos recibidos para registrar incidencia vinculada:', $request->all());

        // Validar los datos del request
        $request->validate([
            'id_persona' => 'required|exists:personas,id_persona',
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
                'id_comunidad'=> $request->input('comunidad'),
                'punto_de_referencia' => $request->input('punto_de_referencia'),
            ]
        );

        // Generar un slug único entre ambas tablas
        $slug = Str::slug(Str::lower($request->input('descripcion')));
        $originalSlug = $slug;
        $counter = 1;

        while (
            incidencia_persona::where('slug', $slug)->exists() ||
            IncidenciaGeneral::where('slug', $slug)->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Generar un código único para la incidencia
        $codigo = Str::random(8);
        while (incidencia_persona::where('cod_incidencia', $codigo)->exists()) {
            $codigo = Str::random(8);
        }

        // Crear la incidencia vinculada
        $incidencia = new incidencia_persona();
        $incidencia->id_persona = $request->input('id_persona');
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
            'id_incidencia_p' => $incidencia->id_incidencia_p,
            'id_usuario' => auth()->id(),
            'descripcion' => 'Se registró una incidencia vinculada a una persona',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incidencia registrada correctamente.',
            'redirect_url' => route('incidencias.show', $incidencia->slug),
        ]);
    } catch (\Exception $e) {
        Log::error('Error al registrar incidencia vinculada:', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la incidencia: ' . $e->getMessage(),
        ], 500);
    }
}

    public function storeIncidenciaConContacto(Request $request)
    {
        try {
            Log::info('Datos recibidos para registrar incidencia con contacto:', $request->all());


            $direccion = Direccion::where('calle', $request->input('calle'))
                ->where('numero_de_vivienda', $request->input('numero_de_vivienda'))
                ->where('id_estado', $request->input('estado'))
                ->where('id_municipio', $request->input('municipio'))
                ->where('id_parroquia', $request->input('parroquia'))
                ->where('id_urbanizacion', $request->input('urbanizacion'))
                ->where('id_sector', $request->input('sector'))
                ->where('id_comunidad', $request->input('comunidad'))
                ->first();

            // Si no existe, crear una nueva dirección
            if (!$direccion) {
                $direccion = new Direccion();
                $direccion->calle = $request->input('calle');
                $direccion->manzana = $request->input('manzana');
                $direccion->bloque = $request->input('bloque');
                $direccion->numero_de_vivienda = $request->input('numero_de_vivienda');
                $direccion->id_estado = $request->input('estado');
                $direccion->id_municipio = $request->input('municipio');
                $direccion->id_parroquia = $request->input('parroquia');
                $direccion->id_urbanizacion = $request->input('urbanizacion');
                $direccion->id_sector = $request->input('sector');
                $direccion->id_comunidad = $request->input('comunidad');
                $direccion->punto_de_referencia = $request->input('punto_de_referencia');
                $direccion->save();
            }

            // Crear la incidencia
            $incidencia = new incidencia_persona();
            $incidencia->id_direccion = $direccion->id_direccion;
            $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
            $incidencia->descripcion = $request->input('descripcion');
            $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
            $incidencia->estado = 'Por atender';
            $incidencia->id_institucion = $request->input('institucion');
            $incidencia->id_institucion_estacion = $request->input('estacion');
            $incidencia->id_usuario = auth()->id();
            $incidencia->save();

            // Registrar movimiento
            movimiento::create([
                'id_incidencia_p' => $incidencia->id_incidencia_p,
                'id_usuario' => auth()->id(),
                'descripcion' => 'Incidencia registrada con persona de contacto',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Incidencia registrada correctamente con persona de contacto.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al registrar incidencia con contacto:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la incidencia: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeIncidenciaGeneral(Request $request)
{
    try {
        Log::info('Datos recibidos para registrar incidencia general:', $request->all());

        // Validar que se proporcione la institución
        if (!$request->filled('institucion')) {
            return response()->json([
                'success' => false,
                'message' => 'Debe seleccionar una institución.',
            ], 400);
        }

        // Validar que se proporcione la dirección
        if (!$request->filled('calle')) {
            return response()->json([
                'success' => false,
                'message' => 'Debe proporcionar una dirección válida.',
            ], 400);
        }

        // Verificar si la dirección ya existe
        $direccion = Direccion::where('calle', $request->input('calle'))
            ->where('id_estado', $request->input('estado'))
            ->where('id_municipio', $request->input('municipio'))
            ->where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->where('id_comunidad', $request->input('comunidad'))
            ->where('punto_de_referencia', $request->input('punto_de_referencia'))
            ->first();

        // Si no existe, crear una nueva dirección
        if (!$direccion) {
            $direccion = new Direccion();
            $direccion->calle = $request->input('calle');
            $direccion->punto_de_referencia = $request->input('punto_de_referencia');
            $direccion->id_estado = $request->input('estado');
            $direccion->id_municipio = $request->input('municipio');
            $direccion->id_parroquia = $request->input('parroquia');
            $direccion->id_urbanizacion = $request->input('urbanizacion');
            $direccion->id_sector = $request->input('sector');
            $direccion->save();
        }

        // Crear la incidencia general
        $incidencia = new IncidenciaGeneral();

        // Generar un slug único entre ambas tablas
        $slug = Str::slug(Str::lower($request->input('descripcion')));
        $originalSlug = $slug;
        $counter = 1;

        while (
            incidencia_persona::where('slug', $slug)->exists() ||
            IncidenciaGeneral::where('slug', $slug)->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $incidencia->slug = $slug;

        // Generar un código único para la incidencia
        $codigo = Str::random(8);
        while (IncidenciaGeneral::where('cod_incidencia', $codigo)->exists()) {
            $codigo = Str::random(8);
        }

        $incidencia->cod_incidencia = $codigo;
        $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
        $incidencia->descripcion = $request->input('descripcion');
        $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
        $incidencia->estado = 'Por atender';
        $incidencia->id_usuario = auth()->id();
        $incidencia->id_institucion = $request->input('institucion');
        $incidencia->id_institucion_estacion = $request->input('estacion'); // Asignar la institución
        $incidencia->id_direccion = $direccion->id_direccion; // Asignar la dirección
        $incidencia->save();

        // Registrar movimiento
        movimiento::create([
            'id_incidencia' => $incidencia->id_incidencia,
            'id_usuario' => auth()->id(),
            'descripcion' => 'Incidencia general registrada',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incidencia general registrada correctamente.',
            'redirect_url' => route('incidencias.show', $incidencia->slug),
        ]);
    } catch (\Exception $e) {
        Log::error('Error al registrar incidencia general:', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la incidencia: ' . $e->getMessage(),
        ], 500);
    }
}

    public function atenderVista($slug)
    {
        try {
            // Buscar la incidencia en ambas tablas
            $incidencia = incidencia_persona::where('slug', $slug)->first()
                ?? IncidenciaGeneral::where('slug', $slug)->first();

            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada.');
            }

            return view('incidencias.atenderIncidencia', compact('incidencia'));
        } catch (\Exception $e) {
            Log::error('Error al cargar la vista de atención:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al cargar la incidencia para atender.']);
        }
    }

    private function updateIncidenciaVinculada(StoreIncidenciaRequest $request, $incidencia)
{
    // Buscar la dirección original
    $direccionOriginal = Direccion::find($incidencia->id_direccion);

    // Buscar si ya existe una dirección idéntica
    $direccionExistente = Direccion::where('calle', $request->input('calle'))
        ->where('punto_de_referencia', $request->input('punto_de_referencia'))
        ->where('id_estado', $request->input('estado'))
        ->where('id_municipio', $request->input('municipio'))
        ->where('id_parroquia', $request->input('parroquia'))
        ->where('id_urbanizacion', $request->input('urbanizacion'))
        ->where('id_sector', $request->input('sector'))
        ->first();

    // Si existe una dirección idéntica, usarla
    if ($direccionExistente && $direccionExistente->id_direccion != $direccionOriginal->id_direccion) {
        $direccion = $direccionExistente;
    } else {
        // Verificar si otras incidencias usan la misma dirección original
        $usoDireccion = incidencia_persona::where('id_direccion', $direccionOriginal->id_direccion)
            ->where('id_incidencia_p', '!=', $incidencia->id_incidencia_p)
            ->exists();

        // Si otras incidencias usan la dirección original, crear una nueva
        if ($usoDireccion) {
            $direccion = new Direccion();
        } else {
            $direccion = $direccionOriginal;
        }

        // Actualizar los datos de la dirección
        $direccion->calle = $request->input('calle');
        $direccion->punto_de_referencia = $request->input('punto_de_referencia');
        $direccion->id_estado = $request->input('estado');
        $direccion->id_municipio = $request->input('municipio');
        $direccion->id_parroquia = $request->input('parroquia');
        $direccion->id_urbanizacion = $request->input('urbanizacion');
        $direccion->id_sector = $request->input('sector');
        $direccion->id_comunidad = $request->input('comunidad');
        $direccion->save();
    }

    // Actualizar la incidencia vinculada
    $incidencia->id_direccion = $direccion->id_direccion;
    $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
    $incidencia->descripcion = Str::lower($request->input('descripcion'));
    $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
    $incidencia->estado = 'Por atender';
    $incidencia->id_institucion = $request->input('institucion');
    $incidencia->id_institucion_estacion = $request->input('estacion');
    $incidencia->save();

    // Registrar el movimiento
    movimiento::create([
        'id_incidencia_p' => $incidencia->id_incidencia_p,
        'id_usuario' => auth()->id(),
        'descripcion' => 'Se actualizó la incidencia vinculada y su dirección',
    ]);
}

private function updateIncidenciaGeneral(StoreIncidenciaRequest $request, $incidencia)
{
    DB::beginTransaction();
    try {
        // Buscar la dirección original
        $direccionOriginal = Direccion::find($incidencia->id_direccion);

        // Buscar si ya existe una dirección idéntica
        $direccionExistente = Direccion::where('calle', $request->input('calle'))
            ->where('punto_de_referencia', $request->input('punto_de_referencia'))
            ->where('id_estado', $request->input('estado'))
            ->where('id_municipio', $request->input('municipio'))
            ->where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->first();

        // Si existe una dirección idéntica, usarla
        if ($direccionExistente && $direccionExistente->id_direccion != $direccionOriginal->id_direccion) {
            $direccion = $direccionExistente;
        } else {
            // Verificar si otras incidencias usan la misma dirección original
            $usoDireccion = IncidenciaGeneral::where('id_direccion', $direccionOriginal->id_direccion)
                ->where('id_incidencia_g', '!=', $incidencia->id_incidencia_g)
                ->exists();

            // Si otras incidencias usan la dirección original, crear una nueva
            if ($usoDireccion) {
                $direccion = new Direccion();
            } else {
                $direccion = $direccionOriginal;
            }

            // Actualizar los datos de la dirección
            $direccion->calle = $request->input('calle');
            $direccion->punto_de_referencia = $request->input('punto_de_referencia');
            $direccion->id_estado = $request->input('estado');
            $direccion->id_municipio = $request->input('municipio');
            $direccion->id_parroquia = $request->input('parroquia');
            $direccion->id_urbanizacion = $request->input('urbanizacion');
            $direccion->id_sector = $request->input('sector');
            $direccion->id_comunidad = $request->input('comunidad');
            $direccion->save();
        }

        // Actualizar la incidencia general
        $incidencia->id_direccion = $direccion->id_direccion;
        $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
        $incidencia->descripcion = Str::lower($request->input('descripcion'));
        $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
        $incidencia->estado = 'Por atender';
        $incidencia->id_institucion = $request->input('institucion');
        $incidencia->id_institucion_estacion = $request->input('estacion');
        $incidencia->save();

        // Registrar el movimiento
        movimiento::create([
            'id_incidencia' => $incidencia->id_incidencia_g,
            'id_usuario' => auth()->id(),
            'descripcion' => 'Se actualizó la incidencia general y su dirección',
        ]);

        DB::commit();

        return [
            'success' => true,
            'redirect_url' => route('incidencias.index')
        ];

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al actualizar incidencia general: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al actualizar la incidencia general.'
        ];
    }
}
}
