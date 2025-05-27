<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\categoriaExclusivaPersona;
use App\Models\Direccion;
use App\Models\direccionIncidencia;
use App\Models\Estado;
use App\Models\estadoIncidencia;
use App\Models\incidencia;
use App\Models\incidencia_persona;
use App\Models\IncidenciaGeneral;
use App\Models\Institucion;
use App\Models\institucionApoyo;
use App\Models\InstitucionEstacion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\nivelIncidencia;
use App\Models\Notificacion;
use App\Models\Persona;
use App\Models\personalReparacion;
use App\Models\PruebaFotografica;
use App\Models\ReparacionIncidencia;
use App\Models\tipoIncidencia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            'direccionIncidencia',
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
        ; // Paginación para mejorar la carga

        return view('incidencias.listaincidencias', compact('incidencias', 'estados', 'niveles'));
    } catch (\Exception $e) {
        Log::error('Error al cargar la lista de incidencias:', ['error' => $e->getMessage()]);
        return back()->withErrors(['error' => 'Error al cargar la lista de incidencias.']);
    }
}
    public function crear($slug)
    {
        $direcciones = direccionIncidencia::all();
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
        $direcciones = direccionIncidencia::all();
        $estados = Estado::all();
        $prioridades = nivelIncidencia::all();
        $tipos = tipoIncidencia::all();
        // Retornar la vista para registrar una incidencia general
        return view('incidencias.registrarIncidenciaGeneral', compact('instituciones', 'direcciones', 'estados', 'prioridades', 'tipos'));
    }

  public function store(Request $request)
{
    try {
        // Validar los datos del request incluyendo las instituciones de apoyo
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
            'instituciones_apoyo' => 'nullable|array',
            'instituciones_apoyo.*' => 'exists:instituciones,id_institucion',
            'estaciones_apoyo' => 'nullable|array',
            'estaciones_apoyo.*' => 'nullable|exists:instituciones_estaciones,id_institucion_estacion',
            // Para imágenes opcionales (ninguna obligatoria):
            'pruebasAntes' => 'nullable|array|max:3',
            'pruebasAntes.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            // Si quieres que al menos una imagen sea obligatoria, descomenta la siguiente línea:
            // 'pruebasAntes.0' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $duplicada = $this->esIncidenciaDuplicada($request);
        if ($duplicada) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una incidencia similar registrada.',
                'is_duplicate' => true,
                'codigo_duplicado' => $duplicada->cod_incidencia,
                'slug_duplicado' => $duplicada->slug,
                'ver_url' => route('incidencias.ver', $duplicada->slug),
                'duplicate_data' => [
                    'codigo' => $duplicada->cod_incidencia,
                    'descripcion' => $duplicada->descripcion,
                    'fecha_creacion' => $duplicada->created_at->format('d/m/Y H:i'),
                    'estado' => $duplicada->estadoIncidencia->nombre,
                    'prioridad' => $duplicada->nivelIncidencia->nombre,
                    'comunidad' => $duplicada->direccionIncidencia->comunidad ? $duplicada->direccionIncidencia->comunidad->nombre : null,
                ]
            ], 422);
        }

        // Buscar o crear la dirección
        $direccion = direccionIncidencia::firstOrCreate(
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
        $incidencia->id_direccion_incidencia = $direccion->id_direccion_incidencia;
        $incidencia->id_usuario = auth()->id();
        $incidencia->id_institucion = $request->input('institucion');
        $incidencia->id_institucion_estacion = $request->input('estacion');
        $incidencia->fecha_vencimiento = $fechaVencimiento;
        $incidencia->save();
if ($request->hasFile('pruebasAntes')) {
            foreach ($request->file('pruebasAntes') as $key => $file) {
                if ($file) {
                    $path = $file->store('pruebasAntes', 'public');
                    
                    PruebaFotografica::create([
                        'id_incidencia' => $incidencia->id_incidencia,
                        'ruta' => $path,
                        'observacion' => 'Imagen ' . ($key + 1) . ' de la incidencia',
                        'etapa_foto' => 'Antes',
                    ]);
                }
            }
        }

        // Guardar imágenes de la incidencia si existen
        if ($request->hasFile('pruebas_fotograficas')) {
            foreach ($request->file('pruebas_fotograficas') as $key => $file) {
                if ($file) {
                    $ruta = $file->store('pruebasAntes', 'public');
                    PruebaFotografica::create([
                        'id_incidencia' => $incidencia->id_incidencia,
                        'ruta' => $ruta,
                        'observacion' => 'Imagen ' . ($key + 1) . ' de la incidencia',
                        'etapa_foto' => 'Antes',
                    ]);
                }
            }
        }

        // Registrar instituciones de apoyo si existen
        if ($request->has('instituciones_apoyo')) {
            $institucionesApoyo = $request->input('instituciones_apoyo');
            $estacionesApoyo = $request->input('estaciones_apoyo', []);

            foreach ($institucionesApoyo as $index => $idInstitucion) {
                $idEstacion = $estacionesApoyo[$index] ?? null;

                institucionApoyo::create([
                    'id_incidencia' => $incidencia->id_incidencia,
                    'id_institucion' => $idInstitucion,
                    'id_institucion_estacion' => $idEstacion,
                    
                ]);
            }
        }

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
// En IncidenciaController.php

protected function esIncidenciaDuplicada(Request $request, $excludeId = null)
{
    // Buscar direcciones con los mismos datos (excepto punto de referencia)
    $direcciones = \App\Models\direccionIncidencia::where([
        'calle' => $request->input('calle'),
        'id_estado' => $request->input('estado'),
        'id_municipio' => $request->input('municipio'),
        'id_parroquia' => $request->input('parroquia'),
        'id_urbanizacion' => $request->input('urbanizacion'),
        'id_sector' => $request->input('sector'),
        'id_comunidad' => $request->input('comunidad'),
    ])->get();

    $puntoReferenciaNuevo = trim(mb_strtolower($request->input('punto_de_referencia', '')));
    $umbralSimilitud = 80; // porcentaje mínimo de similitud

    foreach ($direcciones as $direccion) {
        // Comparar punto de referencia con similitud de texto
        $puntoExistente = trim(mb_strtolower($direccion->punto_de_referencia ?? ''));
        similar_text($puntoReferenciaNuevo, $puntoExistente, $porcentaje);
        if ($porcentaje >= $umbralSimilitud) {
            // Buscar incidencia activa en esa dirección
            $query = \App\Models\incidencia::with(['estadoIncidencia', 'institucionesApoyo'])
                ->where('id_direccion_incidencia', $direccion->id_direccion_incidencia)
                ->where('id_institucion', $request->input('institucion'))
                ->where('id_institucion_estacion', $request->input('estacion'))
                ->where('id_tipo_incidencia', $request->input('tipo_incidencia'))
                ->where('id_nivel_incidencia', $request->input('nivel_prioridad'));
            if ($excludeId) {
                $query->where('id_incidencia', '!=', $excludeId);
            }
            $query->whereHas('estadoIncidencia', function($q) {
                $q->where('nombre', '!=', 'Atendido')
                  ->where('nombre', '!=', 'Resuelto');
            });
            $incidencia = $query->first();
            if ($incidencia) {
                return $incidencia;
            }
        }
    }
    return null;
}

    public function descargar($slug)
    {
        try {
            // Buscar la incidencia
            $incidencia = Incidencia::with(['persona', 'direccionIncidencia.estado', 'direccionIncidencia.municipio', 'direccionIncidencia.parroquia', 'direccionIncidencia.urbanizacion', 'direccionIncidencia.sector', 'institucion', 'institucionEstacion','institucionesApoyo.institucion', 
            'institucionesApoyo.Estacion', ])
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
    'direccionIncidencia.estado',
    'direccionIncidencia.municipio',
    'direccionIncidencia.parroquia',
    'direccionIncidencia.urbanizacion',
    'direccionIncidencia.sector',
    'direccionIncidencia.comunidad',
    'institucion',
    'institucionEstacion.municipio',
    'nivelIncidencia',
    'estadoIncidencia',
    'usuario',
    'tipoIncidencia',
    'institucionesApoyo.institucion',  // Cambiado de 'institucionApoyo' a 'institucionesApoyo'
    'institucionesApoyo.estacion'     // Agregado para cargar la relación de estación
])->where('slug', $slug)->first();
    
            if (!$incidencia) {
                return redirect()->route('incidencias.index')
                    ->withErrors(['error' => 'Incidencia no encontrada o no disponible para edición.']);
            }
    
            // Verificar permisos de edición
            // if (!auth()->user()->can('editar incidencias') && 
            //     $incidencia->id_usuario !== auth()->id()) {
            //     return redirect()->route('incidencias.ver', $incidencia->slug)
            //         ->withErrors(['error' => 'No tiene permisos para editar esta incidencia.']);
            // }
    
            // Obtener datos necesarios para los selects
            $instituciones = Institucion::orderBy('nombre')->get();
            $estaciones = InstitucionEstacion::with('municipio')
                ->orderBy('nombre')
                ->get();
                
            $prioridades = NivelIncidencia::all();
            $estados = EstadoIncidencia::orderBy('nombre')->get();
            $tipos = tipoIncidencia::orderBy('nombre')->get();
            // Obtener estaciones relacionadas con la institución actual (para precargar)
            $estacionesRelacionadas = $incidencia->id_institucion 
                ? InstitucionEstacion::where('id_institucion', $incidencia->id_institucion)
                    ->with('municipio')
                    ->get()
                : collect();
            
            // NUEVO: Pasar imágenes actuales (solo 'Antes')
            $imagenesAntes = $incidencia->pruebasFotograficas->where('etapa_foto', 'Antes')->values();
            return view('incidencias.editarIncidencia', [
                'incidencia' => $incidencia,
                'tipos'=>$tipos,
                'instituciones' => $instituciones,
                'estaciones' => $estaciones,
                'prioridades' => $prioridades,
                'estados' => $estados,
                'estacionesRelacionadas' => $estacionesRelacionadas,
                'estadoActual' => $incidencia->direccionIncidencia->estado ?? null,
                'municipioActual' => $incidencia->direccionIncidencia->municipio ?? null,
                'parroquiaActual' => $incidencia->direccionIncidencia->parroquia ?? null,
                'urbanizacionActual' => $incidencia->direccionIncidencia->urbanizacion ?? null,
                'sectorActual' => $incidencia->direccionIncidencia->sector ?? null,
                'comunidadActual' => $incidencia->direccionIncidencia->comunidad ?? null,
                'imagenesAntes' => $imagenesAntes,
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
        $incidencia = Incidencia::with([
            'nivelIncidencia', 
            'tipoIncidencia', 
            'institucionesApoyo',
            'direccionIncidencia',
            'estadoIncidencia'
        ])->where('slug', $slug)->first();

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
            'tipo_incidencia' => 'required|exists:tipos_incidencias,id_tipo_incidencia',
            'instituciones_apoyo' => 'nullable|array',
            'instituciones_apoyo.*' => 'exists:instituciones,id_institucion',
            'estaciones_apoyo' => 'nullable|array',
            'estaciones_apoyo.*' => 'nullable|exists:instituciones_estaciones,id_institucion_estacion',
        ]);

        // Verificar si hay cambios relevantes que podrían crear un duplicado
        $hayCambiosRelevantes = $this->hayCambiosRelevantes($incidencia, $request);
        
        if ($hayCambiosRelevantes) {
            $posibleDuplicado = $this->esIncidenciaDuplicada($request, $incidencia->id_incidencia);
            
            if ($posibleDuplicado && $posibleDuplicado->id_incidencia != $incidencia->id_incidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una incidencia similar registrada.',
                    'is_duplicate' => true,
                    'codigo_duplicado' => $posibleDuplicado->cod_incidencia,
                    'slug_duplicado' => $posibleDuplicado->slug,
                    'ver_url' => route('incidencias.ver', $posibleDuplicado->slug),
                    'duplicate_data' => [
                        'codigo' => $posibleDuplicado->cod_incidencia,
                        'descripcion' => $posibleDuplicado->descripcion,
                        'comunidad' => $posibleDuplicado->direccionIncidencia->comunidad ? $posibleDuplicado->direccionIncidencia->comunidad->nombre : null,
                        'fecha_creacion' => $posibleDuplicado->created_at->format('d/m/Y H:i'),
                        'estado' => $posibleDuplicado->estadoIncidencia->nombre,
                        'prioridad' => $posibleDuplicado->nivelIncidencia->nombre
                    ]
                ], 422);
            }
        }

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
        $direccion = direccionIncidencia::firstOrCreate([
            'calle' => $request->input('calle'),
            'id_estado' => $request->input('estado'),
            'id_municipio' => $request->input('municipio'),
            'id_parroquia' => $request->input('parroquia'),
            'id_urbanizacion' => $request->input('urbanizacion'),
            'id_sector' => $request->input('sector'),
            'id_comunidad' => $request->input('comunidad'),
            'punto_de_referencia' => $request->input('punto_de_referencia'),
        ]);

        // Mantener el estado actual si no es pendiente
        $estadoIncidencia = $incidencia->estadoIncidencia->nombre === 'Pendiente' 
            ? estadoIncidencia::where('nombre', 'Pendiente')->first()
            : $incidencia->estadoIncidencia;

        // Actualizar los datos de la incidencia
        $datosActualizacion = [
            'slug' => $nuevoSlug,
            'id_persona' => $request->input('id_persona'),
            'id_tipo_incidencia' => $tipoIncidencia->id_tipo_incidencia,
            'descripcion' => Str::lower($request->input('descripcion')),
            'id_nivel_incidencia' => $nivel->id_nivel_incidencia,
            'id_direccion_incidencia' => $direccion->id_direccion_incidencia,
            'id_institucion' => $request->input('institucion'),
            'id_institucion_estacion' => $request->input('estacion'),
            'id_estado_incidencia' => $estadoIncidencia->id_estado_incidencia,
        ];

        // Actualizar fecha de vencimiento solo si cambió la prioridad
        if ($incidencia->id_nivel_incidencia != $nivel->id_nivel_incidencia) {
            $datosActualizacion['fecha_vencimiento'] = now()->addHours($nivel->horas_vencimiento);
        }

        // Iniciar transacción para asegurar integridad de datos
        DB::beginTransaction();

        // Actualizar la incidencia
        $incidencia->update($datosActualizacion);

        // Manejar instituciones de apoyo
        $this->actualizarInstitucionesApoyo($incidencia, $request);

        // --- NUEVO: Manejar subida de nuevas imágenes 'Antes' ---
        $imagenesActuales = PruebaFotografica::where('id_incidencia', $incidencia->id_incidencia)
            ->where('etapa_foto', 'Antes')
            ->count();
        $maxImagenes = 3;
        $imagenesRestantes = $maxImagenes - $imagenesActuales;

        if ($imagenesRestantes > 0 && $request->hasFile('pruebasAntes')) {
            $archivos = $request->file('pruebasAntes');
            $guardadas = 0;
            foreach ($archivos as $key => $file) {
                if ($file && $imagenesActuales + $guardadas < $maxImagenes) {
                    $path = $file->store('pruebasAntes', 'public');
                    PruebaFotografica::create([
                        'id_incidencia' => $incidencia->id_incidencia,
                        'ruta' => $path,
                        'observacion' => 'Imagen ' . ($imagenesActuales + $guardadas + 1) . ' de la incidencia',
                        'etapa_foto' => 'Antes',
                    ]);
                    $guardadas++;
                }
            }
        }
        // --- FIN NUEVO ---

        // --- REEMPLAZO DE IMÁGENES EXISTENTES ---
        if ($request->has('reemplazo_imagen')) {
            foreach ($request->file('reemplazo_imagen', []) as $id => $file) {
                if ($file && is_numeric($id)) {
                    $img = PruebaFotografica::find($id);
                    if ($img && $img->id_incidencia == $incidencia->id_incidencia && $img->etapa_foto === 'Antes') {
                        // Eliminar archivo anterior
                        if ($img->ruta && Storage::disk('public')->exists($img->ruta)) {
                            Storage::disk('public')->delete($img->ruta);
                        }
                        // Guardar nuevo archivo
                        $nuevaRuta = $file->store('pruebasAntes', 'public');
                        $img->ruta = $nuevaRuta;
                        $img->observacion = 'Imagen reemplazada el ' . now()->format('d/m/Y H:i');
                        $img->save();
                    }
                }
            }
        }
        // --- FIN REEMPLAZO DE IMÁGENES ---

        // Registrar movimiento
        Movimiento::create([
            'id_incidencia' => $incidencia->id_incidencia,
            'id_usuario' => auth()->id(),
            'descripcion' => 'Incidencia actualizada',
            'fecha_movimiento' => now(),
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => '✅ Incidencia actualizada correctamente.',
            'redirect_url' => route('incidencias.show', $nuevoSlug),
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al actualizar la incidencia:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => '⚠️ Error al actualizar la incidencia: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Verifica si hay cambios relevantes que podrían crear un duplicado
 */
protected function hayCambiosRelevantes(Incidencia $incidencia, Request $request): bool
{
    // Comparar campos principales
    $camposPrincipales = [
        'descripcion' => $incidencia->descripcion !== Str::lower($request->input('descripcion')),
        'nivel_prioridad' => $incidencia->id_nivel_incidencia != $request->input('nivel_prioridad'),
        'tipo_incidencia' => $incidencia->id_tipo_incidencia != $request->input('tipo_incidencia'),
        'institucion' => $incidencia->id_institucion != $request->input('institucion'),
        'estacion' => $incidencia->id_institucion_estacion != $request->input('estacion'),
    ];

    // Comparar datos de dirección
    $direccion = $incidencia->direccionIncidencia;
    $camposDireccion = [
        'calle' => $direccion->calle !== $request->input('calle'),
        'estado' => $direccion->id_estado != $request->input('estado'),
        'municipio' => $direccion->id_municipio != $request->input('municipio'),
        'parroquia' => $direccion->id_parroquia != $request->input('parroquia'),
        'urbanizacion' => $direccion->id_urbanizacion != $request->input('urbanizacion'),
        'sector' => $direccion->id_sector != $request->input('sector'),
        'comunidad' => $direccion->id_comunidad != $request->input('comunidad'),
        'punto_referencia' => $direccion->punto_de_referencia != $request->input('punto_de_referencia'),
    ];

    // Verificar cambios en instituciones de apoyo
    $institucionesActuales = $incidencia->institucionesApoyo->pluck('id_institucion')->toArray();
    $institucionesNuevas = $request->input('instituciones_apoyo', []);
    
    $cambioInstituciones = count(array_diff($institucionesActuales, $institucionesNuevas)) > 0 || 
                         count(array_diff($institucionesNuevas, $institucionesActuales)) > 0;

    // Si hay cambios en algún campo relevante
    return in_array(true, $camposPrincipales, true) || 
           in_array(true, $camposDireccion, true) || 
           $cambioInstituciones;
}

protected function actualizarInstitucionesApoyo(Incidencia $incidencia, Request $request)
{
    try {
        // Obtener las instituciones de apoyo actuales
        $institucionesActuales = $incidencia->institucionesApoyo->pluck('id_institucion')->toArray();
        
        // Obtener las nuevas instituciones de apoyo del request
        $institucionesNuevas = $request->input('instituciones_apoyo', []);
        $estacionesNuevas = $request->input('estaciones_apoyo', []);
        
        // Identificar qué eliminar y qué agregar
        $paraEliminar = array_diff($institucionesActuales, $institucionesNuevas);
        $paraAgregar = array_diff($institucionesNuevas, $institucionesActuales);
        
        // Eliminar las que ya no están
        if (!empty($paraEliminar)) {
            institucionApoyo::where('id_incidencia', $incidencia->id_incidencia)
                ->whereIn('id_institucion', $paraEliminar)
                ->delete();
        }
        
        // Agregar las nuevas
        foreach ($institucionesNuevas as $index => $idInstitucion) {
            // Verificar si ya existe esta combinación
            $existe = institucionApoyo::where('id_incidencia', $incidencia->id_incidencia)
                ->where('id_institucion', $idInstitucion)
                ->where('id_institucion_estacion', $estacionesNuevas[$index] ?? null)
                ->exists();
                
            if (!$existe) {
                institucionApoyo::create([
                    'id_incidencia' => $incidencia->id_incidencia,
                    'id_institucion' => $idInstitucion,
                    'id_institucion_estacion' => $estacionesNuevas[$index] ?? null,
                ]);
            }
        }
        
    } catch (\Exception $e) {
        Log::error('Error al actualizar instituciones de apoyo', [
            'error' => $e->getMessage(),
            'incidencia_id' => $incidencia->id_incidencia,
            'data' => $request->all()
        ]);
        throw $e;
    }
}


public function atenderGuardar(Request $request, $slug)
{
    $request->validate([
        'descripcion' => 'required|string|max:1000',
        'pruebas_fotograficas.0' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        'pruebas_fotograficas.1' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        'pruebas_fotograficas.2' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        'institucion' => 'required|exists:instituciones,id_institucion',
        'estacion' => 'required|exists:instituciones_estaciones,id_institucion_estacion',
    ]);

    try {
        $incidencia = Incidencia::where('slug', $slug)->first();
        if (!$incidencia) {
            return response()->json(['message' => 'Incidencia no encontrada.'], 404);
        }
        // NO asignar por defecto la institución responsable de la incidencia
        $institucion = Institucion::find($request->input('institucion'));
        $institucionEstacion = InstitucionEstacion::find($request->input('estacion'));
        $slug = $this->generarSlugUnico($slug);

        // Guardar el personal de reparación con la institución/estación seleccionadas
        $personal = $this->registrarPersonalReparacion($request, $institucion, $institucionEstacion, $incidencia);

        // Guardar la reparación con referencia a la prueba fotográfica principal
        $reparacion = ReparacionIncidencia::create([
            'id_incidencia' => $incidencia->id_incidencia,
            'id_usuario' => auth()->id(),
            'descripcion' => $request->input('descripcion'),
            'slug' => $slug,
            'id_personal_reparacion' => $personal->id_personal_reparacion ?? null,
        ]);

        // Actualizar estado y registrar movimiento
        $incidencia->id_estado_incidencia = estadoIncidencia::where('nombre', 'Atendido')->first()->id_estado_incidencia;
        $incidencia->save();
        $fotos = $request->file('pruebas_fotograficas', []);
        $idPruebaPrincipal = null;
        foreach ($fotos as $i => $foto) {
            if ($foto) {
                $ruta = $foto->store('pruebas', 'public');
                $prueba = \App\Models\PruebaFotografica::create([
                    'id_reparacion' => $reparacion->id_reparacion,
                    'observacion' => 'Foto ' . ($i + 1),
                    'ruta' => $ruta,
                    'etapa_foto' => 'atencion',
                ]);
                if ($i === 0) {
                    $idPruebaPrincipal = $prueba->id_prueba_fotografica;
                }
            }
        }
        movimiento::create([
            'id_incidencia' => $incidencia->id_incidencia,
            'id_usuario' => auth()->id(),
            'descripcion' => 'Incidencia atendida con pruebas fotográficas',
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
         return $personal;
    }

    // Ahora que tenemos a la persona (ya sea encontrada o creada), asignarla a la reparación
  return $personal;
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
                'direccionIncidencia',
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
            'institucionesApoyo.institucion', // Carga las instituciones de apoyo y su relación con Institucion
            'institucionesApoyo.Estacion',    // Carga la relación con Estacion de cada institución de apoyo
            'institucion',                   // Institución principal
            'institucionEstacion',            // Estación principal
            'direccionIncidencia.estado',     // Relaciones para la dirección
            'direccionIncidencia.municipio',
            'direccionIncidencia.parroquia',
            'direccionIncidencia.urbanizacion',
            'direccionIncidencia.sector'
        ])->where('slug', $slug)->first();

        if (!$incidencia) {
            abort(404, 'Incidencia no encontrada');
        }

        // Calcular tiempo restante (si la incidencia no está resuelta)
        $tiempoRestante = null;
        if (!$incidencia->estadoIncidencia->es_resuelto && $incidencia->fecha_vencimiento) {
            $tiempoRestante = now()->diff($incidencia->fecha_vencimiento);
        }

        // Retornar la vista con todos los datos
        return view('incidencias.incidencia', [
            'incidencia' => $incidencia,
            'tiempoRestante' => $tiempoRestante,
            'nivel' => $incidencia->nivelIncidencia,
            'estado' => $incidencia->estadoIncidencia,
            'tipo' => $incidencia->tipoIncidencia,
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
            'nivelIncidencia',
            'estadoIncidencia',
            'tipoIncidencia',
            'institucionesApoyo.institucion',
            'institucionesApoyo.Estacion',
            'pruebasFotograficas' // Añade esta relación
        ])->where('slug', $slug)->first();

        if (!$incidencia) {
            abort(404, 'Incidencia no encontrada.');
        }

        // Obtener la reparación asociada si la incidencia está atendida
        $reparacion = null;
        if (strtolower($incidencia->estadoIncidencia->nombre) == 'atendido') {
            $reparacion = ReparacionIncidencia::where('id_incidencia', $incidencia->id_incidencia)
                ->with('personalReparacion', 'pruebasFotograficas')
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
            $incidencia = Incidencia::with(['direccionIncidencia', 'institucion', 'institucionEstacion'])
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
public function downloadPdf($id)
{
    $incidencia = Incidencia::with([
        'pruebasFotograficas',
        'tipoIncidencia',
        'nivelIncidencia',
        'estadoIncidencia',
        'institucion',
        'estacion.municipio',
        'direccionIncidencia.estado',
        'direccionIncidencia.municipio',
        'direccionIncidencia.parroquia',
        'direccionIncidencia.urbanizacion',
        'direccionIncidencia.sector',
        'direccionIncidencia.comunidad',
        'usuario.empleadoAutorizado',
        'institucionesApoyo.institucion',
        'institucionesApoyo.Estacion',
        'movimiento',
        'reparacion.pruebasFotograficas',
    ])->findOrFail($id);

    // Obtener la institución propietaria (donde es_propietario = 1)
    $institucionPropietaria = Institucion::where('es_propietario', 1)->first();

    // Obtener el logo de la institución propietaria y convertirlo a base64
    $logoBase64 = null;
    if ($institucionPropietaria && $institucionPropietaria->logo_path) {
        $logoPath = public_path('storage/' . $institucionPropietaria->logo_path);
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }
    }

    // Obtener el encabezado HTML de la institución propietaria (membrete)
    $membrete = $institucionPropietaria ? $institucionPropietaria->encabezado_html : '';

    // Obtener el pie de página HTML de la institución propietaria
    $pie_html = $institucionPropietaria->pie_html ?? 'Generado el '.now()->format('d/m/Y H:i:s');

    // Filtrar imágenes 'Antes'
    $imagenesAntes = $incidencia->pruebasFotograficas->where('etapa_foto', 'Antes');

    // Obtener la reparación asociada si existe
    $reparacion = $incidencia->reparacion ?? null;

    // Pasar los datos a la vista del PDF
    return FacadePdf::loadView('incidencias.DetallesIncidencia', [
        'incidencia' => $incidencia,
        'imagenesAntes' => $imagenesAntes,
        'logoBase64' => $logoBase64,
        'membrete' => $membrete,
        'pie_html' => $pie_html,  // ¡Este es el parámetro que faltaba!
        'reparacion' => $reparacion,
    ])
    ->setOption('isRemoteEnabled', true)
    ->setOption('isHtml5ParserEnabled', true)
    ->setOption('defaultFont', 'DejaVu Sans')
    ->download('incidencia_' . $incidencia->cod_incidencia . '.pdf');
}

public function downloadReport(Request $request)
{
    // Obtener los filtros del request
    $filters = [
        'start_date' => $request->input('start_date', now()->subMonth()->format('Y-m-d')),
        'end_date' => $request->input('end_date', now()->format('Y-m-d')),
        'tipo_incidencia_id' => $request->input('tipo_incidencia_id'),
        'nivel_incidencia_id' => $request->input('nivel_incidencia_id'),
        'institucion_id' => $request->input('institucion_id'),
        'estacion_id' => $request->input('estacion_id')
    ];

    // Consulta base con los filtros aplicados
    $query = Incidencia::query()
        ->when($filters['start_date'] && $filters['end_date'], function($q) use ($filters) {
            $q->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay()
            ]);
        })
        ->when($filters['tipo_incidencia_id'], function($q) use ($filters) {
            $q->where('id_tipo_incidencia', $filters['tipo_incidencia_id']);
        })
        ->when($filters['nivel_incidencia_id'], function($q) use ($filters) {
            $q->where('id_nivel_incidencia', $filters['nivel_incidencia_id']);
        })
        ->when($filters['institucion_id'], function($q) use ($filters) {
            $q->where('id_institucion', $filters['institucion_id']);
        })
        ->when($filters['estacion_id'], function($q) use ($filters) {
            $q->where('id_institucion_estacion', $filters['estacion_id']);
        });

    // Obtener el estado "Atendido" y "Pendiente"
    $estadoAtendido = estadoIncidencia::where('nombre', 'Atendido')->first();
    $estadoPendiente = estadoIncidencia::where('nombre', 'Pendiente')->first();

    // Obtener la institución propietaria (MINAGUAS)
    $institucionPropietaria = Institucion::where('es_propietario', 1)->first();
    
    // Generar membrete HTML
    $membreteHtml = '';
    if ($institucionPropietaria) {
        // Logo en base64 si existe
        if ($institucionPropietaria->logo_path && Storage::exists('public/'.$institucionPropietaria->logo_path)) {
            $logoPath = Storage::path('public/'.$institucionPropietaria->logo_path);
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $membreteHtml .= '<img src="data:image/'.$extension.';base64,'.$logoData.'" style="height: 60px; margin-bottom: 10px;"><br>';
            }
        }
        
        // Encabezado HTML
        $membreteHtml .= '<div style="text-align: center;">';
        $membreteHtml .= '<strong>'.$institucionPropietaria->nombre.'</strong><br>';
        $membreteHtml .= '<strong>Sistema de Gestión de Incidencias</strong><br>';
        $membreteHtml .= '<small>Reporte estadístico generado automáticamente</small>';
        $membreteHtml .= '</div>';
    } else {
        $membreteHtml = '<div style="text-align: center;"><strong>Sistema de Gestión de Incidencias</strong><br>MINAGUAS</div>';
    }

    // Obtener los datos para el reporte
    $data = [
        'totalIncidencias' => $query->count(),
        'incidenciasAtendidas' => $estadoAtendido ? $query->clone()->where('id_estado_incidencia', $estadoAtendido->id_estado_incidencia)->count() : 0,
        'incidenciasPendientes' => $estadoPendiente ? $query->clone()->where('id_estado_incidencia', $estadoPendiente->id_estado_incidencia)->count() : 0,
        'incidenciasPorVencer' => $query->clone()
            ->where('id_estado_incidencia', $estadoPendiente->id_estado_incidencia ?? 0)
            ->whereBetween('fecha_vencimiento', [now(), now()->addMinutes(5)])
            ->count(),
        'incidenciasPorEstado' => [
            'labels' => estadoIncidencia::orderBy('id_estado_incidencia')->pluck('nombre'),
            'values' => estadoIncidencia::orderBy('id_estado_incidencia')->get()->map(function($estado) use ($query) {
                return $query->clone()->where('id_estado_incidencia', $estado->id_estado_incidencia)->count();
            }),
            'colors' => estadoIncidencia::orderBy('id_estado_incidencia')->pluck('color'),
            'detalles' => []
        ],
        'incidenciasPorNivel' => [
            'labels' => nivelIncidencia::orderBy('id_nivel_incidencia')->pluck('nombre'),
            'values' => nivelIncidencia::orderBy('id_nivel_incidencia')->get()->map(function($nivel) use ($query) {
                return $query->clone()->where('id_nivel_incidencia', $nivel->id_nivel_incidencia)->count();
            }),
            'colors' => nivelIncidencia::orderBy('id_nivel_incidencia')->pluck('color')
        ],
        'startDate' => $filters['start_date'],
        'endDate' => $filters['end_date'],
        'filters' => $filters,
        'membrete' => $membreteHtml,
        'institucionFiltrada' => Institucion::find($filters['institucion_id'])
    ];

    // Crear el PDF con opciones mejoradas
    $pdf = FacadePdf::loadView('graficos.incidencias_pdf', $data)
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('defaultFont', 'DejaVu Sans')
        ->setPaper('a4', 'portrait');

    return $pdf->download('reporte_incidencias_'.now()->format('YmdHis').'.pdf');
}
}