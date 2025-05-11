<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\categoriaExclusivaPersona;
use App\Models\Direccion;
use App\Models\Estado;
use App\Models\estadoIncidencia;
use App\Models\incidencia;
use App\Models\incidencia_persona;
use App\Models\IncidenciaGeneral;
use App\Models\Institucion;
use App\Models\InstitucionEstacion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\nivelIncidencia;
use App\Models\Notificacion;
use App\Models\Persona;
use App\Models\personalReparacion;
use App\Models\ReparacionIncidencia;
use App\Models\tipoIncidencia;
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
        // Obtener todos los estados para el filtro
        $estados = estadoIncidencia::all();
        $niveles = nivelIncidencia::all();

        // Consultar las incidencias con los filtros aplicados
        $incidencias = Incidencia::with([
            'persona',
            'direccion',
            'usuario.empleadoAutorizado',
            'nivelIncidencia',
            'estadoIncidencia',
            'tipoIncidencia'
        ])
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
            return $query->whereHas('estadoIncidencia', function ($q) use ($request) {
                $q->where('nombre', $request->estado);
            });
        })
        ->when($request->prioridad && $request->prioridad !== 'Todos', function ($query) use ($request) {
            return $query->whereHas('nivelIncidencia', function ($q) use ($request) {
                $q->where('nombre', $request->prioridad);
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10); // Paginación para mejorar la carga

        return view('incidencias.listaincidencias', compact('incidencias', 'estados', 'niveles'));
    } catch (\Exception $e) {
        Log::error('Error al cargar la lista de incidencias:', ['error' => $e->getMessage()]);
        return back()->withErrors(['error' => 'Error al cargar la lista de incidencias.']);
    }
}
    public function crear($slug)
    {
        $direcciones = Direccion::all();
        $prioridades = nivelIncidencia::all();
        $tipos= tipoIncidencia::all();
        $persona = Persona::where('slug', $slug)->first();
        $instituciones = Institucion::all(); // Obtener todas las instituciones

        return view('incidencias.registrarIncidencia', compact('persona', 'instituciones', 'direcciones', 'prioridades', 'tipos'));
    }


    public function create()
    {
        // Cargar datos necesarios para la vista, como instituciones o direcciones
        $instituciones = Institucion::all();
        $direcciones = Direccion::all();
        $estados = Estado::all();
        $prioridades = nivelIncidencia::all();
        $tipos = tipoIncidencia::all();
        // Retornar la vista para registrar una incidencia general
        return view('incidencias.registrarIncidenciaGeneral', compact('instituciones', 'direcciones', 'estados', 'prioridades', 'tipos'));
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
            'comunidad' => 'nullable|exists:comunidades,id_comunidad',
            'descripcion' => 'required|string',
            'nivel_prioridad' => 'required|exists:niveles_incidencias,id_nivel_incidencia',
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

        // Obtener el nivel de incidencia
        $nivel = NivelIncidencia::findOrFail($request->input('nivel_prioridad'));

        // Obtener el estado inicial (por ejemplo, estado "Pendiente" con ID 1)
        $estadoInicial = estadoIncidencia::where('nombre', 'Pendiente')->firstOrFail();

        // Calcular la fecha de vencimiento
        $fechaVencimiento = now()->addHours($nivel->horas_vencimiento);

        // Crear la incidencia
        $incidencia = new Incidencia();
        $incidencia->id_persona = $request->input('id_persona');
        $incidencia->slug = $slug;
        $incidencia->cod_incidencia = $codigo;
        $incidencia->id_tipo_incidencia = $request->input('tipo_incidencia');
        $incidencia->descripcion = Str::lower($request->input('descripcion'));
        $incidencia->id_nivel_incidencia = $nivel->id_nivel_incidencia;
        $incidencia->id_estado_incidencia = $estadoInicial->id_estado_incidencia;
        $incidencia->id_direccion = $direccion->id_direccion;
        $incidencia->id_usuario = auth()->id();
        $incidencia->id_institucion = $request->input('institucion');
        $incidencia->id_institucion_estacion = $request->input('estacion');
        $incidencia->fecha_vencimiento = $fechaVencimiento;
        $incidencia->save();

        // Registrar movimiento inicial
        Movimiento::create([
            'id_incidencia' => $incidencia->id_incidencia,
            'id_usuario' => auth()->id(),
            'descripcion' => 'Incidencia creada con estado: ' . $estadoInicial->nombre,
            'fecha_movimiento' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incidencia registrada correctamente.',
            'redirect_url' => route('incidencias.show', $incidencia->slug),
            'data' => [
                'incidencia_id' => $incidencia->id_incidencia,
                'fecha_vencimiento' => $fechaVencimiento->format('d/m/Y H:i'),
                'estado_actual' => $estadoInicial->nombre,
            ]
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
            // Buscar la incidencia con todas las relaciones necesarias
            $incidencia = Incidencia::with([
                'persona',
                'direccion.estado',
                'direccion.municipio',
                'direccion.parroquia',
                'direccion.urbanizacion',
                'direccion.sector',
                'direccion.comunidad',
                'institucion',
                'institucionEstacion.municipio',
                'nivelIncidencia',
                'estadoIncidencia',
                'usuario',
                'tipoIncidencia'
            ])->where('slug', $slug)->first();
    
            if (!$incidencia) {
                return redirect()->route('incidencias.index')
                    ->withErrors(['error' => 'Incidencia no encontrada o no disponible para edición.']);
            }
    
            // Verificar permisos de edición
            if (!auth()->user()->can('editar incidencias') && 
                $incidencia->id_usuario !== auth()->id()) {
                return redirect()->route('incidencias.ver', $incidencia->slug)
                    ->withErrors(['error' => 'No tiene permisos para editar esta incidencia.']);
            }
    
            // Obtener datos necesarios para los selects
            $instituciones = Institucion::orderBy('nombre')->get();
            $estaciones = InstitucionEstacion::with('municipio')
                ->orderBy('nombre')
                ->get();
                
            $prioridades = NivelIncidencia::orderBy('horas_vencimiento', 'desc')->get();
            $estados = EstadoIncidencia::orderBy('nombre')->get();
            $tipos = tipoIncidencia::orderBy('nombre')->get();
            // Obtener estaciones relacionadas con la institución actual (para precargar)
            $estacionesRelacionadas = $incidencia->id_institucion 
                ? InstitucionEstacion::where('id_institucion', $incidencia->id_institucion)
                    ->with('municipio')
                    ->get()
                : collect();
            
            return view('incidencias.editarIncidencia', [
                'incidencia' => $incidencia,
                'tipos'=>$tipos,
                'instituciones' => $instituciones,
                'estaciones' => $estaciones,
                'prioridades' => $prioridades,
                'estados' => $estados,
                'estacionesRelacionadas' => $estacionesRelacionadas,
                'estadoActual' => $incidencia->direccion->estado ?? null,
                'municipioActual' => $incidencia->direccion->municipio ?? null,
                'parroquiaActual' => $incidencia->direccion->parroquia ?? null,
                'urbanizacionActual' => $incidencia->direccion->urbanizacion ?? null,
                'sectorActual' => $incidencia->direccion->sector ?? null,
                'comunidadActual' => $incidencia->direccion->comunidad ?? null,
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error al cargar vista de edición de incidencia:', [
                'error' => $e->getMessage(),
                'slug' => $slug,
                'user' => auth()->id()
            ]);
            
            return redirect()->route('incidencias.index')
                ->withErrors(['error' => 'Ocurrió un error al cargar la incidencia para edición. Por favor intente nuevamente.']);
        }
    }


    public function update(Request $request, $slug)
    {
        try {
            // Buscar la incidencia con relaciones necesarias
            $incidencia = Incidencia::with(['nivelIncidencia', 'tipoIncidencia'])
                ->where('slug', $slug)
                ->first();
    
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
                'comunidad' => 'nullable|exists:comunidades,id_comunidad',
                'descripcion' => 'required|string',
                'nivel_prioridad' => 'required|exists:niveles_incidencias,id_nivel_incidencia',
                'institucion' => 'required|exists:instituciones,id_institucion',
                'estacion' => 'nullable|exists:instituciones_estaciones,id_institucion_estacion',
            ]);
    
            // Solo generar nuevo slug si cambió la descripción
            $nuevoSlug = $incidencia->slug;
            if ($incidencia->descripcion !== $request->input('descripcion')) {
                $nuevoSlug = Str::slug(Str::lower($request->input('descripcion')));
                $originalSlug = $nuevoSlug;
                $counter = 1;
    
                while (Incidencia::where('slug', $nuevoSlug)->where('id_incidencia', '!=', $incidencia->id_incidencia)->exists()) {
                    $nuevoSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
    
            // Obtener modelos relacionados
            $nivel = NivelIncidencia::findOrFail($request->input('nivel_prioridad'));
            $tipoIncidencia = tipoIncidencia::findOrFail($request->input('tipo_incidencia'));
    
            // Buscar o crear la dirección
            $direccion = Direccion::firstOrCreate([
                'calle' => $request->input('calle'),
                'id_estado' => $request->input('estado'),
                'id_municipio' => $request->input('municipio'),
                'id_parroquia' => $request->input('parroquia'),
                'id_urbanizacion' => $request->input('urbanizacion'),
                'id_sector' => $request->input('sector'),
                'id_comunidad' => $request->input('comunidad'),
                'punto_de_referencia' => $request->input('punto_de_referencia'),
            ]);
    
            // Actualizar los datos de la incidencia
            $datosActualizacion = [
                'slug' => $nuevoSlug,
                'id_persona' => $request->input('id_persona'),
                'id_tipo_incidencia' => $tipoIncidencia->id_tipo_incidencia,
                'descripcion' => Str::lower($request->input('descripcion')),
                'id_nivel_incidencia' => $nivel->id_nivel_incidencia,
                'id_direccion' => $direccion->id_direccion,
                'id_institucion' => $request->input('institucion'),
                'id_institucion_estacion' => $request->input('estacion'),
            ];
    
            // Actualizar fecha de vencimiento solo si cambió la prioridad
            if ($incidencia->id_nivel_incidencia != $nivel->id_nivel_incidencia) {
                $datosActualizacion['fecha_vencimiento'] = now()->addHours($nivel->horas_vencimiento);
            }
    
            $incidencia->update($datosActualizacion);
    
            // Registrar movimiento
            Movimiento::create([
                'id_incidencia' => $incidencia->id_incidencia,
                'id_usuario' => auth()->id(),
                'descripcion' => 'Se actualizó una incidencia',
                'fecha_movimiento' => now(),
            ]);
    
            return response()->json([
                'success' => true,
                'message' => '✅ Incidencia actualizada correctamente.',
                'redirect_url' => route('incidencias.show', $nuevoSlug),
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
        $institucion = Institucion::find($incidencia->id_institucion);
        $institucionEstacion = InstitucionEstacion::find($incidencia->id_institucion_estacion);
        // Generar un slug único
        $slug = $this->generarSlugUnico($slug);

        // Subir la prueba fotográfica
        $rutaPrueba = $this->subirPruebaFotografica($request);

        // Crear registro de reparación
        ReparacionIncidencia::create([
            'id_incidencia' => $incidencia->id_incidencia,
            'id_usuario' => auth()->id(),
            'descripcion' => $request->input('descripcion'),
            'prueba_fotografica' => $rutaPrueba,
            'slug' => $slug,
            'id_usuario' => auth()->id(),
        ]);
        $this->registrarPersonalReparacion( $request,$institucion, $institucionEstacion,$incidencia);

        // Registrar al personal que realizó la reparación

        // Actualizar estado
        $incidencia->id_estado_incidencia = estadoIncidencia::where('nombre', 'Atendido')->first()->id_estado_incidencia;
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

// Método para generar un slug único
private function generarSlugUnico($slug)
{
    $originalSlug = $slug;
    $counter = 1;

    while (ReparacionIncidencia::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

// Método para subir la prueba fotográfica
private function subirPruebaFotografica(Request $request)
{
    return $request->file('prueba_fotografica')->store('pruebas', 'public');
}

// Método para registrar al personal que hizo la reparación
private function registrarPersonalReparacion(Request $request, $institucion, $institucionEstacion,$incidencia)
{
    // Validar que los campos no estén vacíos
    $request->validate([
     
        'cedula' => 'required|string|max:255',
        'nacionalidad' => 'required|string|max:2',
    ]);

    // Buscar la persona por cédula
    $personal = personalReparacion::where('cedula', $request->input('cedula'))->first();
    $slug= Str::slug(Str::lower($request->input('nombre') . ' ' . $request->input('apellido')));
    $originalSlug = $slug;
    $counter = 1;
    while (personalReparacion::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    // Si la persona no existe, crearla
    if (!$personal) {
        $personal = personalReparacion::create([
            'id_usuario' => auth()->id(),
            'id_institucion' => $institucion->id_institucion,
            'id_institucion_estacion' => $institucionEstacion->id_institucion_estacion,
            'cedula' => $request->input('cedula'),
            'slug' => $slug,
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'nacionalidad' => $request->input('nacionalidad'),
            'telefono' => $request->input('telefono'),
        ]);
    }

    // Ahora que tenemos a la persona (ya sea encontrada o creada), asignarla a la reparación
    ReparacionIncidencia::where('id_incidencia', $incidencia->id_incidencia)
        ->update([
            'id_personal_reparacion' => $personal->id_personal_reparacion,
        ]);
}




    public function filtrar(Request $request)
    {
        try {
            $validated = $request->validate([
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date',
                'estado' => 'nullable|string',
                'prioridad' => 'nullable|string',
                'codigo' => 'nullable|string|max:20'
            ]);
    
            $incidencias = Incidencia::with([
                'persona',
                'direccion',
                'usuario.empleadoAutorizado',
                'nivelIncidencia',
                'estadoIncidencia',
                'tipoIncidencia'
            ])
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
                return $query->whereHas('estadoIncidencia', function ($q) use ($request) {
                    $q->where('nombre', $request->estado);
                });
            })
            ->when($request->prioridad && $request->prioridad !== 'Todos', function ($query) use ($request) {
                return $query->whereHas('nivelIncidencia', function ($q) use ($request) {
                    $q->where('nombre', $request->prioridad);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    
            // Transformar los datos para que coincidan con la estructura esperada por el frontend
            $incidenciasTransformadas = $incidencias->map(function ($incidencia) {
                return [
                    'cod_incidencia' => $incidencia->cod_incidencia,
                    'tipo_incidencia' => $incidencia->tipoIncidencia ? [
                        'nombre' => $incidencia->tipoIncidencia->nombre
                    ] : null,                    'descripcion' => $incidencia->descripcion,
                    'created_at' => $incidencia->created_at,
                    'fecha_vencimiento' => $incidencia->fecha_vencimiento,
                    'slug' => $incidencia->slug,
                    'persona' => $incidencia->persona ? [
                        'nombre' => $incidencia->persona->nombre,
                        'apellido' => $incidencia->persona->apellido,
                        'cedula' => $incidencia->persona->cedula
                    ] : null,
                    'usuario' => $incidencia->usuario ? [
                        'empleado_autorizado' => $incidencia->usuario->empleadoAutorizado ? [
                            'nombre' => $incidencia->usuario->empleadoAutorizado->nombre,
                            'apellido' => $incidencia->usuario->empleadoAutorizado->apellido,
                            'cedula' => $incidencia->usuario->empleadoAutorizado->cedula
                        ] : null
                    ] : null,
                    'nivelIncidencia' => $incidencia->nivelIncidencia ? [
                        'nombre' => $incidencia->nivelIncidencia->nombre,
                        'color' => $incidencia->nivelIncidencia->color
                    ] : null,
                    'estadoIncidencia' => $incidencia->estadoIncidencia ? [
                        'nombre' => $incidencia->estadoIncidencia->nombre,
                        'color' => $incidencia->estadoIncidencia->color,
                        'es_resuelto' => $incidencia->estadoIncidencia->es_resuelto
                    ] : null
                ];
            });
    
            return response()->json([
                'success' => true,
                'incidencias' => $incidenciasTransformadas,
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
        // Obtener los parámetros de filtrado
        $fechaInicio = $request->input('fecha_inicio') 
            ? Carbon::parse($request->input('fecha_inicio'))->startOfDay() 
            : Carbon::parse(Incidencia::min('created_at'))->startOfDay();
        
        $fechaFin = $request->input('fecha_fin') 
            ? Carbon::parse($request->input('fecha_fin'))->endOfDay() 
            : Carbon::parse(Incidencia::max('created_at'))->endOfDay();

        // Validar que fecha_inicio no sea mayor que fecha_fin
        if ($fechaInicio > $fechaFin) {
            return back()->withErrors(['error' => 'La fecha de inicio no puede ser mayor que la fecha de fin.']);
        }
        
        // Filtros de estado y prioridad
        $estado = $request->input('estado', 'Todos');
        $prioridad = $request->input('prioridad', 'Todos');
        $codigo = $request->input('codigo', '');

        // Obtener las incidencias filtradas con las relaciones necesarias
        $incidencias = Incidencia::with([
            'persona',
            'usuario.empleadoAutorizado',
            'nivelIncidencia',
            'estadoIncidencia'
        ])
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->when($estado !== 'Todos', function ($query) use ($estado) {
            return $query->whereHas('estadoIncidencia', function ($q) use ($estado) {
                $q->where('nombre', $estado);
            });
        })
        ->when($prioridad !== 'Todos', function ($query) use ($prioridad) {
            return $query->whereHas('nivelIncidencia', function ($q) use ($prioridad) {
                $q->where('nombre', $prioridad);
            });
        })
        ->when(!empty($codigo), function ($query) use ($codigo) {
            return $query->where('cod_incidencia', 'like', "%$codigo%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

        // Verificar si se encontraron incidencias
        if ($incidencias->isEmpty()) {
            return back()->withErrors(['error' => 'No se encontraron incidencias para los filtros seleccionados.']);
        }

        // Generar el nombre del archivo con base en los filtros
        $nombreArchivo = 'reporte_incidencias_' . $fechaInicio->format('Y-m-d') . '_a_' . $fechaFin->format('Y-m-d');
        if ($estado !== 'Todos') {
            $nombreArchivo .= '_estado_' . Str::slug($estado);
        }
        if ($prioridad !== 'Todos') {
            $nombreArchivo .= '_prioridad_' . Str::slug($prioridad);
        }
        if (!empty($codigo)) {
            $nombreArchivo .= '_codigo_' . Str::slug($codigo);
        }
        $nombreArchivo .= '.pdf';

        // Generar el PDF
        $pdf = FacadePdf::loadView('incidencias.pdf_table', [
            'incidencias' => $incidencias,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'estado' => $estado,
            'prioridad' => $prioridad
        ]);

        // Descargar el archivo PDF generado
        return $pdf->download($nombreArchivo);

    } catch (\Exception $e) {
        // Loguear el error para seguimiento
        Log::error('Error al generar el PDF de incidencias:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Retornar error con mensaje
        return back()
            ->withInput()
            ->withErrors(['error' => 'Ocurrió un error al generar el reporte. Por favor intente nuevamente.']);
    }
}


    public function show($slug)
    {
        try {
            // Buscar la incidencia con todas las relaciones necesarias
            $incidencia = Incidencia::with([
                'persona',
                'usuario.empleadoAutorizado',
                'nivelIncidencia',  // Relación con el nivel de prioridad
                'estadoIncidencia',
                'tipoIncidencia',
            ])->where('slug', $slug)->first();
    
            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada');
            }
    
            // Calcular tiempo restante (si la incidencia no está resuelta)
            $tiempoRestante = null;
            if (!$incidencia->estadoIncidencia->es_resuelto && $incidencia->fecha_vencimiento) {
                $tiempoRestante = now()->diff($incidencia->fecha_vencimiento);
            }
    
            // Si la incidencia está vinculada a una persona, cargar la información
            $persona = $incidencia->id_persona ? $incidencia->persona : null;
            
            // Retornar la vista con todos los datos
            return view('incidencias.incidencia', [
                'incidencia' => $incidencia,
                'persona' => $persona,
                'tiempoRestante' => $tiempoRestante,
                'nivel' => $incidencia->nivelIncidencia,  // Acceso directo al nivel
                'estado' => $incidencia->estadoIncidencia,
                'tipo' => $incidencia->tipoIncidencia,  // Acceso directo al tipo de incidencia
                // Acceso directo al estado
            ]);
        } catch (\Exception $e) {
            Log::error('Error en IncidenciaController@show: ' . $e->getMessage());
            abort(500, 'Ocurrió un error al mostrar la incidencia');
        }
    }

    public function ver($slug)
    {
        try {
            // Buscar la incidencia con las relaciones necesarias
            $incidencia = Incidencia::with([
                'persona',
                'usuario.empleadoAutorizado',
                'nivelIncidencia',  // Relación con el nivel de prioridad
                'estadoIncidencia',  // Relación con el estado actual
                'tipoIncidencia',  // Relación con el tipo de incidencia
            ])->where('slug', $slug)->first();
    
            if (!$incidencia) {
                abort(404, 'Incidencia no encontrada.');
            }
    
            // Obtener la reparación asociada si la incidencia está atendida
            $reparacion = null;
            if ($incidencia->estadoIncidencia->nombre == 'atendido') {
                $reparacion = ReparacionIncidencia::where('id_incidencia', $incidencia->id_incidencia)
                    ->with('personalReparacion')  // Aseguramos que cargue la relación con el personal
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
    
    // Validación de fechas con valores por defecto
    $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfYear()))->startOfDay();
    $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()))->endOfDay();
    
    // Validar fechas
    if ($startDate > $endDate) {
        if ($request->ajax()) {
            return response()->json(['error' => 'La fecha de inicio no puede ser posterior a la fecha de fin'], 422);
        }
        return back()->with('error', 'La fecha de inicio no puede ser posterior a la fecha de fin');
    }
    
    // Obtener parámetros de filtro
    $filters = [
        'tipo_incidencia_id' => $request->input('tipo_incidencia_id', ''),
        'denunciante' => $request->input('denunciante', ''),
        'institucion_id' => $request->input('institucion_id', ''),
        'estacion_id' => $request->input('estacion_id', '')
    ];

    // Obtener datos
    $data = $this->getChartData($startDate, $endDate, $filters);

    // Para peticiones AJAX
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'data' => $data,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }

    // Para peticiones normales
    return view('incidencias.grafica_incidencia_resueltas', array_merge($data, [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'tiposIncidencia' => TipoIncidencia::orderBy('nombre')->get(),
        'instituciones' => Institucion::orderBy('nombre')->get(),
        'estaciones' => $filters['institucion_id'] 
            ? InstitucionEstacion::where('id_institucion', $filters['institucion_id'])->orderBy('nombre')->get()
            : collect(),
        'tipoIncidenciaId' => $filters['tipo_incidencia_id'],
        'denunciante' => $filters['denunciante'],
        'institucionId' => $filters['institucion_id'],
        'estacionId' => $filters['estacion_id']
    ]));
}

protected function getChartData($startDate, $endDate, $filters)
{
    // Consultas base
    $queryAtendidas = Incidencia::with(['estadoIncidencia', 'institucion', 'estacion', 'tipoIncidencia'])
        ->whereHas('estadoIncidencia', function($q) {
            $q->where('nombre', 'Atendido');
        })
        ->whereBetween('created_at', [$startDate, $endDate]);

    $queryTotal = Incidencia::with(['institucion', 'estacion', 'tipoIncidencia'])
        ->whereBetween('created_at', [$startDate, $endDate]);

    // Crear una copia para totales por estación
    $queryTotalEstacion = clone $queryTotal;

    // Aplicar filtros - ahora con 3 argumentos
    $this->applyFilters($queryAtendidas, $queryTotal, $filters);
    $this->applyFilters($queryAtendidas, $queryTotalEstacion, $filters);

    // Obtener datos
    $incidenciasAtendidas = $this->getAtendidasData($queryAtendidas);
    $totalesMensuales = $this->getTotalesMensuales($queryTotal);
    $totalesPorEstacion = $this->getTotalesPorEstacion($queryTotalEstacion);

    return $this->prepareChartData($incidenciasAtendidas, $totalesMensuales, $totalesPorEstacion);
}

// Método applyFilters modificado para recibir 3 parámetros
protected function applyFilters(&$queryAtendidas, &$queryTotal, $filters)
{
    // Filtro por tipo de incidencia
    if (!empty($filters['tipo_incidencia_id'])) {
        $callback = function($q) use ($filters) {
            $q->where('id_tipo_incidencia', $filters['tipo_incidencia_id']);
        };
        
        $queryAtendidas->whereHas('tipoIncidencia', $callback);
        $queryTotal->whereHas('tipoIncidencia', $callback);
    }

    // Filtro por denunciante
    if ($filters['denunciante'] === 'con') {
        $queryAtendidas->whereNotNull('id_persona');
        $queryTotal->whereNotNull('id_persona');
    } elseif ($filters['denunciante'] === 'sin') {
        $queryAtendidas->whereNull('id_persona');
        $queryTotal->whereNull('id_persona');
    }

    // Filtro por institución
    if (!empty($filters['institucion_id'])) {
        $queryAtendidas->where('id_institucion', $filters['institucion_id']);
        $queryTotal->where('id_institucion', $filters['institucion_id']);
    }

    // Filtro por estación
    if (!empty($filters['estacion_id'])) {
        $queryAtendidas->where('id_institucion_estacion', $filters['estacion_id']);
        $queryTotal->where('id_institucion_estacion', $filters['estacion_id']);
    }
}

// Los demás métodos se mantienen igual
protected function getAtendidasData($query)
{
    return $query->selectRaw('
            YEAR(created_at) as year, 
            MONTH(created_at) as month,
            id_institucion,
            id_institucion_estacion,
            COUNT(*) as total
        ')
        ->groupBy('year', 'month', 'id_institucion', 'id_institucion_estacion')
        ->orderBy('year')
        ->orderBy('month')
        ->get();
}

protected function getTotalesMensuales($query)
{
    return $query->selectRaw('
            YEAR(created_at) as year, 
            MONTH(created_at) as month,
            COUNT(*) as total
        ')
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get()
        ->keyBy(function($item) {
            return $item->year . '-' . $item->month;
        });
}

protected function getTotalesPorEstacion($query)
{
    return $query->selectRaw('
            YEAR(created_at) as year, 
            MONTH(created_at) as month,
            id_institucion,
            id_institucion_estacion,
            COUNT(*) as total
        ')
        ->groupBy('year', 'month', 'id_institucion', 'id_institucion_estacion')
        ->orderBy('year')
        ->orderBy('month')
        ->get()
        ->keyBy(function($item) {
            $monthName = Carbon::createFromFormat('m', $item->month)->locale('es')->isoFormat('MMMM');
            return $monthName . ' ' . $item->year . '|' . $item->id_institucion . '|' . $item->id_institucion_estacion;
        });
}

protected function prepareChartData($incidenciasAtendidas, $totalesMensuales, $totalesPorEstacion)
{
    $labels = [];
    $dataAtendidas = [];
    $porcentajes = [];
    $detalles = [];

    foreach ($incidenciasAtendidas as $incidencia) {
        $monthName = Carbon::createFromFormat('m', $incidencia->month)->locale('es')->isoFormat('MMMM');
        $key = $monthName . ' ' . $incidencia->year;
        
        $uniqueKey = $key . '|' . $incidencia->id_institucion . '|' . $incidencia->id_institucion_estacion;
        
        $labels[$uniqueKey] = $key;
        $dataAtendidas[$uniqueKey] = $incidencia->total;
        
        // Calcular porcentaje
        $mesKey = $incidencia->year . '-' . $incidencia->month;
        $totalMes = $totalesMensuales[$mesKey]->total ?? 0;
        $porcentajes[$uniqueKey] = $totalMes > 0 ? round(($incidencia->total / $totalMes) * 100, 2) : 0;

        // Obtener total específico por estación
        $totalEstacion = $totalesPorEstacion[$uniqueKey]->total ?? 0;

        // Agregar detalles
        $detalles[$uniqueKey] = [
            'institucion' => $incidencia->institucion->nombre ?? 'N/A',
            'estacion' => $incidencia->estacion->nombre ?? 'N/A',
            'total' => $incidencia->total,
            'total_mes_estacion' => $totalEstacion,
            'tipo_incidencia' => $incidencia->tipoIncidencia->nombre ?? 'N/A'
        ];
    }

    return [
        'labels' => $labels,
        'dataAtendidas' => $dataAtendidas,
        'porcentajes' => $porcentajes,
        'detalles' => $detalles
    ];
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
