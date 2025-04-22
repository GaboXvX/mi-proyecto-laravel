<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\Direccion;
use App\Models\incidencia;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\Notificacion;
use App\Models\Persona;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IncidenciaController extends Controller
{
    

    public function index(Request $request) 
    {
        // Cargar las incidencias junto con las relaciones necesarias
        $incidencias = Incidencia::with(['usuario.empleadoAutorizado', 'lider.personas']) // Incluir usuario.empleadoAutorizado y lider.personas
            ->when($request->codigo, function ($query, $codigo) {
                return $query->where('cod_incidencia', 'like', "%$codigo%");
            })
            ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
            })
            ->when($request->estado && $request->estado !== 'Todos', function ($query, $estado) {
                return $query->where('estado', $estado);
            })
            ->orderBy('id_incidencia', 'desc')
            ->get();
        
        // Retornar la vista con las incidencias
        return view('incidencias.listaincidencias', compact('incidencias'));
    }
    
    public function crear($slug)
    {

        $persona = Persona::where('slug', $slug)->first();


        return view('incidencias.registrarIncidencia', compact('persona'));
    }
    public function create($slug)
    {

        $persona = Persona::where('slug', $slug)->first();

        $lider = lider_comunitario::where('slug', $slug)->first();

        if ($lider) {

            return view('incidencias.registrarIncidencialider', compact('lider'));
        }

        if ($persona) {
            return view('incidencias.registrarIncidencia', compact('persona'));
        }
    }



    public function store(StoreIncidenciaRequest $request)
    {
        try {
            Log::info('Datos recibidos para registrar incidencia:', $request->all());
    
            // Crear una nueva incidencia
            $incidencia = new Incidencia;
    
            // Generar un slug único
            $slug = Str::slug(Str::lower($request->input('descripcion')));
            $originalSlug = $slug;
            $counter = 1;
    
            while (Incidencia::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
    
            // Generar un código único para la incidencia
            $codigo = Str::random(8);
            while (Incidencia::where('cod_incidencia', $codigo)->exists()) {
                $codigo = Str::random(8);
            }
    
            $incidencia->slug = $slug;
            $incidencia->cod_incidencia = $codigo;
    
            // Validar y obtener la persona asociada
            $persona = Persona::find($request->input('id_persona'));
            if (!$persona) {
                return response()->json([
                    'success' => false,
                    'message' => 'La persona seleccionada no existe.',
                ], 400);
            }
    
            // Obtener la dirección asociada a la incidencia
            $direccion = Direccion::find($request->input('direccion'));
            if (!$direccion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La dirección seleccionada no existe.',
                ], 400);
            }
    
            // Buscar al líder según la comunidad de la dirección y su estado activo
            $lider = Lider_Comunitario::where('id_comunidad', $direccion->id_comunidad)
                ->where('estado', 1) // Verificamos que el líder esté activo
                ->first();
    
            if ($lider) {
                // Asignamos el líder a la incidencia
                $incidencia->id_lider = $lider->id_lider;
            } else {
                // Si no hay un líder activo, asignamos NULL
                $incidencia->id_lider = null;
            }
    
            // Asignar los valores a la incidencia
            $incidencia->id_persona = $persona->id_persona; // Asociar la persona
            $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
            $incidencia->descripcion = Str::lower($request->input('descripcion'));
            $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
            $incidencia->estado = 'Por atender'; // Estado inicial
            $incidencia->id_direccion = $direccion->id_direccion; // Asociar la dirección
            $incidencia->id_usuario = auth()->id(); // Usuario autenticado
    
            // Guardar la incidencia
            $incidencia->save();
    
            // Depuración: Verifica si los datos se guardaron
            if (!$incidencia->exists) {
                throw new \Exception('La incidencia no se guardó correctamente.');
            }
    
            Log::info('Incidencia registrada correctamente:', $incidencia->toArray());
            
            // Registrar movimiento
            $movimiento = new movimiento();
            $movimiento->id_incidencia = $incidencia->id_incidencia;
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->descripcion = 'Se registró una incidencia';
            $movimiento->save();
    
    
            // Retornar la respuesta con la URL de redirección
            return response()->json([
                'success' => true,
                'message' => 'Incidencia registrada correctamente.',
                'redirect_url' => route('incidencias.show', [
                    'slug' => $persona->slug,
                    'incidencia_slug' => $incidencia->slug
                ])
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
        // Buscar la incidencia por el slug
        $incidencia = Incidencia::with(['persona', 'direccion', 'lider.personas'])->where('slug', $slug)->first();

        // Verificar si la incidencia existe
        if (!$incidencia) {
            abort(404, 'Incidencia no encontrada.');
        }

        // Generar el PDF con el formato individual
        $pdf = FacadePdf::loadView('incidencias.incidencia_pdf', compact('incidencia'))
                        ->setPaper('a4', 'portrait');

        // Descargar el PDF con el código de incidencia como nombre
        return $pdf->download('incidencia-' . $incidencia->cod_incidencia . '.pdf');
    }
    


   

    public function edit($slug, $persona_slug = null)
    {
        $incidencia = Incidencia::where('slug', $slug)->firstOrFail();
        $persona = null;
    
        if ($persona_slug) {
            $persona = Persona::where('slug', $persona_slug)->first();
        } else {
            $persona = Persona::find($incidencia->id_persona);
        }
    
        // Obtener las direcciones asociadas a la persona o un array vacío si no hay direcciones
        $direcciones = $persona ? $persona->direcciones ?? collect() : collect();
    
        if (!$persona) {
            return view('incidencias.modificarincidencialider', compact('incidencia', 'direcciones'));
        }
    
        return view('incidencias.modificarincidencia', compact('incidencia', 'persona', 'direcciones'));
    }
    

    public function update(StoreIncidenciaRequest $request, $id)
    {
        try {
            // Buscar la incidencia
            $incidencia = Incidencia::findOrFail($id);

            // Generar un slug único
            $slug = Str::slug(Str::lower($request->input('descripcion')));
            $originalSlug = $slug;
            $counter = 1;

            // Verificar si el slug ya existe en otra incidencia
            while (Incidencia::where('slug', $slug)->where('id_incidencia', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Validar la dirección
            $direccion = Direccion::find($request->input('direccion'));
            if (!$direccion) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ La dirección seleccionada no existe.',
                ], 400);
            }

            // Buscar al líder de la comunidad asociada
            $lider = Lider_Comunitario::where('id_comunidad', $direccion->id_comunidad)
                ->where('estado', 1)
                ->first();

            // Asignar el líder si existe, de lo contrario, dejarlo como null
            $incidencia->id_lider = $lider ? $lider->id_lider : null;
            $incidencia->slug = $slug;
            $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
            $incidencia->descripcion = Str::lower($request->input('descripcion'));
            $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
            $incidencia->estado = $request->input('estado');
            $incidencia->id_direccion = $request->input('direccion');
            
            // Guardar los cambios en la incidencia
            $incidencia->save();

            // Registrar el movimiento
            $movimiento = new movimiento();
            $movimiento->id_incidencia = $incidencia->id_incidencia;
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->descripcion = 'Se actualizó una incidencia';
            $movimiento->save();
         
            // Retornar respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => '✅ Incidencia actualizada correctamente.',
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => '⚠️ Error al actualizar la incidencia: ' . $e->getMessage(),
            ], 500);
        }
    }
    



   
    public function atender($slug)
    {
        try {
            $incidencia = Incidencia::where('slug', $slug)->firstOrFail();
            
            // Verificar permisos
            if (!auth()->user()->can('cambiar estado de incidencias')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción'
                ], 403);
            }
    
            // Cambiar el estado
            $incidencia->estado = 'Atendido';
            $incidencia->save();
    
            // Registrar movimiento
            $movimiento = new Movimiento();
            $movimiento->id_incidencia = $incidencia->id_incidencia;
            $movimiento->id_usuario = auth()->id();
            $movimiento->descripcion = 'Incidencia marcada como atendida';
            $movimiento->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Incidencia marcada como atendida correctamente',
                'incidencia' => [
                    'slug' => $incidencia->slug,
                    'estado' => $incidencia->estado
                ]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al atender la incidencia: ' . $e->getMessage()
            ], 500);
        }
    }


    public function filtrar(Request $request)
{
    $validated = $request->validate([
        'codigo' => 'nullable|string',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date',
        'estado' => 'nullable|string|in:Atendido,Por atender,Todos',
    ]);

    $query = Incidencia::with([
        'usuario.empleadoAutorizado', 
        'lider.personas'
    ]);

    if ($request->codigo) {
        $query->where('cod_incidencia', 'like', "%{$request->codigo}%");
    }

    if ($request->fecha_inicio && $request->fecha_fin) {
        $query->whereBetween('created_at', [
            Carbon::parse($request->fecha_inicio)->startOfDay(),
            Carbon::parse($request->fecha_fin)->endOfDay()
        ]);
    }

    if ($request->estado && $request->estado !== 'Todos') {
        $query->where('estado', $request->estado);
    }

    $incidencias = $query->get()->map(function ($incidencia) {
        return [
            'cod_incidencia' => $incidencia->cod_incidencia,
            'tipo_incidencia' => $incidencia->tipo_incidencia,
            'descripcion' => $incidencia->descripcion,
            'nivel_prioridad' => $incidencia->nivel_prioridad,
            'estado' => $incidencia->estado,
            'created_at' => $incidencia->created_at,
            'slug' => $incidencia->slug,
            'usuario' => $incidencia->usuario ? [
                'empleado_autorizado' => $incidencia->usuario->empleadoAutorizado ? [
                    'nombre' => $incidencia->usuario->empleadoAutorizado->nombre,
                    'apellido' => $incidencia->usuario->empleadoAutorizado->apellido,
                    'cedula' => $incidencia->usuario->empleadoAutorizado->cedula
                ] : null
            ] : null,
            'lider' => $incidencia->lider ? [
                'personas' => $incidencia->lider->personas ? [
                    'nombre' => $incidencia->lider->personas->nombre,
                    'apellido' => $incidencia->lider->personas->apellido,
                    'cedula' => $incidencia->lider->personas->cedula
                ] : null
            ] : null
        ];
    });

    return response()->json(['incidencias' => $incidencias]);
}

    public function generarPDF(Request $request)
    {
        // Obtener las fechas del request con valores por defecto (todas las incidencias si no se especifican fechas)
        $fechaInicio = $request->input('fecha_inicio') ?: Incidencia::min('created_at');
        $fechaFin = $request->input('fecha_fin') ?: Incidencia::max('created_at');
        $estado = $request->input('estado', 'Todos');

        // Validar las fechas
        if (!$fechaInicio || !$fechaFin) {
            return back()->withErrors(['error' => 'Debe seleccionar un rango de fechas válido.']);
        }

        // Construir la consulta para filtrar las incidencias
        $query = Incidencia::with(['persona', 'lider'])->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        if ($estado !== 'Todos') {
            $query->where('estado', $estado);
        }

        $incidencias = $query->get();

        // Verificar si hay incidencias para generar el PDF
        if ($incidencias->isEmpty()) {
            return back()->withErrors(['error' => 'No se encontraron incidencias para el rango de fechas seleccionado.']);
        }

        // Generar el PDF con la tabla completa
        $pdf = FacadePdf::loadView('incidencias.pdf_table', compact('incidencias', 'fechaInicio', 'fechaFin'));

        return $pdf->download('reporte_incidencias.pdf');
    }

    public function show($slug, $incidencia_slug)
    {
        $incidencia = Incidencia::where('slug', $incidencia_slug)->first();
        
        if (!$incidencia) {
            abort(404, 'Incidencia no encontrada');
        }

        $persona = Persona::where('slug', $slug)->first();
        $lider = Persona::whereHas('direccion', function ($query) use ($persona) {
            $query->where('id_comunidad', $persona->direccion->first()->id_comunidad ?? null);
        })->first();
        
        if ($persona) {
            if ($incidencia->id_persona !== $persona->id_persona) {
                abort(404, 'Incidencia no encontrada para esta persona.');
            }
            return view('incidencias.incidencia', compact('incidencia', 'persona', 'lider'));
        }
    }



//    public function download(Request $request)
// {
//     $validated = $request->validate([
//         'fecha_inicio' => 'nullable|date',
//         'fecha_fin' => 'nullable|date',
//         'estado' => 'nullable|string|in:Atendido,Por atender,Todos',
//     ]);

//     $fechaInicio = $request->input('fecha_inicio') ?: Carbon::now()->startOfYear()->toDateString();
//     $fechaFin = $request->input('fecha_fin') ?: Carbon::now()->endOfMonth()->toDateString();
//     $estado = $request->input('estado', 'Todos');

//     $query = Incidencia::with(['persona', 'lider'])
//         ->whereBetween('created_at', [$fechaInicio, $fechaFin]);

//     if ($estado != 'Todos') {
//         $query->where('estado', $estado == 'Por atender' ? 'Por atender' : $estado);
//     }

//     $incidencias = $query->get();

//     if ($incidencias->isEmpty()) {
//         return response()->json(['message' => 'No se encontraron incidencias en este periodo.'], 404);
//     }

//     $pdf = FacadePdf::loadView('incidencias.pdf_table', compact('incidencias', 'fechaInicio', 'fechaFin'));

//     return $pdf->download('incidencias-' . $fechaInicio . '_a_' . $fechaFin . '.pdf');
// }



    public function showChart(Request $request)
{
    
    Carbon::setLocale('es'); 

    
    $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfYear()));
    $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()));

   
    $tipoIncidencia = $request->input('tipo_incidencia', '');

   
    $queryAtendidas = Incidencia::where('estado', 'Atendido')
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
        $incidencia = incidencia::where('cod_incidencia', $codigo)->first();
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

 
    $incidencias = Incidencia::whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->where('estado', 'por atender')
        ->get();

    
    return response()->json([
        'incidencias' => $incidencias
    ]);
}

}
