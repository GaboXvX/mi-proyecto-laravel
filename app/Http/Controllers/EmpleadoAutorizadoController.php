<?php

namespace App\Http\Controllers;

use App\Models\EmpleadoAutorizado;
use App\Models\User;
use App\Models\Cargo;
use App\Models\movimiento;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
=======
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Institucion;
>>>>>>> origin/newbran

class EmpleadoAutorizadoController extends Controller
{
    public function index()
    {
        // Traer todos los empleados autorizados con su usuario (si existe) y su cargo
        $empleados = EmpleadoAutorizado::with(['usuario', 'cargo'])->get();
        return view('empleados.listaEmpleados', compact('empleados'));
    }

    public function create()
    {
        $cargos = Cargo::all();
        return view('empleados.crearEmpleado', compact('cargos'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'cedula' => 'required|string|max:20|unique:empleados_autorizados,cedula',
                'cargo_id' => 'required|exists:cargos_empleados_autorizados,id_cargo',
                'genero' => 'required|in:M,F',
                'nacionalidad'=>'required|in:V,E',
<<<<<<< HEAD
=======
                'nacionalidad'=>'required|in:V,E',
>>>>>>> origin/newbran
                'telefono' => 'required|string|max:20'
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
                'apellido.required' => 'El apellido es obligatorio.',
                'apellido.max' => 'El apellido no puede tener más de 255 caracteres.',
                'cedula.required' => 'La cédula es obligatoria.',
                'cedula.unique' => 'La cédula ya está registrada.',
                'cedula.max' => 'La cédula no puede tener más de 20 caracteres.',
                'cargo_id.required' => 'El cargo es obligatorio.',
                'cargo_id.exists' => 'El cargo seleccionado no es válido.',
                'genero.required' => 'El género es obligatorio.',
                'genero.in' => 'El género seleccionado no es válido.',
                'nacionalidad.in'=>'La nacionalidad no es validad',
<<<<<<< HEAD
=======
                'nacionalidad.in'=>'La nacionalidad no es validad',
>>>>>>> origin/newbran
                'telefono.required' => 'El teléfono es obligatorio.',
                'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.'
            ]);

            $empleado = new EmpleadoAutorizado();
            $empleado->nombre = $request->nombre;
            $empleado->apellido = $request->apellido;
            $empleado->cedula = $request->cedula;
            $empleado->nacionalidad = $request->nacionalidad;
<<<<<<< HEAD
=======
            $empleado->nacionalidad = $request->nacionalidad;
>>>>>>> origin/newbran
            $empleado->id_cargo = $request->cargo_id;
            $empleado->genero = $request->genero;
            $empleado->telefono = $request->telefono;
            $empleado->es_activo = true; // Asignar por defecto como activo
<<<<<<< HEAD
=======
            $empleado->es_activo = true; // Asignar por defecto como activo
>>>>>>> origin/newbran
            $empleado->save();

            return response()->json([
                'success' => true,
                'message' => 'Empleado registrado correctamente',
                'redirect_url' => route('usuarios.index') // Cambié usuarios.index por empleados.index para consistencia
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $empleado = EmpleadoAutorizado::with(['usuario', 'cargo'])->findOrFail($id);
        return view('empleados.verEmpleado', compact('empleado'));
    }

    public function edit($id)
    {
        $empleado = EmpleadoAutorizado::findOrFail($id);
        $cargos = Cargo::all();
        return view('empleados.editarEmpleado', compact('empleado', 'cargos'));
    }

    public function update(Request $request, $id)
    {
        $empleado = EmpleadoAutorizado::findOrFail($id);
        try{
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cargo_id' => 'required|exists:cargos_empleados_autorizados,id_cargo',
            'genero' => 'required|in:M,F',
            'telefono' => 'required|string|max:20'
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.max' => 'El apellido no puede tener más de 255 caracteres.',
            'cargo_id.required' => 'El cargo es obligatorio.',
            'cargo_id.exists' => 'El cargo seleccionado no es válido.',
            'genero.required' => 'El género es obligatorio.',
            'genero.in' => 'El género seleccionado no es válido.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.'
        ]);
        $empleado->nombre = $request->nombre;
        $empleado->apellido = $request->apellido;
        $empleado->id_cargo = $request->cargo_id;
        $empleado->genero = $request->genero;
        $empleado->telefono = $request->telefono;
        $empleado->save();

        return response()->json([
            'success' => true,
            'message' => 'Empleado registrado correctamente',
            'redirect_url' => route('usuarios.index') // Cambié usuarios.index por empleados.index para consistencia
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error inesperado: ' . $e->getMessage()
        ], 500);
    }
}
    

    public function verificarCedula(Request $request)
    {
        $cedula = $request->input('cedula');
        $empleado = EmpleadoAutorizado::where('cedula', $cedula)->first();
        if ($empleado) {
            return response()->json([
                'existe' => true,
                'empleado' => [
                    'nombre' => $empleado->nombre,
                    'apellido' => $empleado->apellido,
                    'nacionalidad' => $empleado->nacionalidad,
<<<<<<< HEAD
=======
                    'nacionalidad' => $empleado->nacionalidad,
>>>>>>> origin/newbran
                    'genero' => $empleado->genero,
                    'telefono' => $empleado->telefono,
                    'id_cargo' => $empleado->id_cargo
                ]
            ]);
        } else {
            return response()->json(['existe' => false]);
        }
    }
    // Agrega estos métodos al final de la clase EmpleadoAutorizadoController

public function retirar(Request $request, $id)
{
    $request->validate([
        'observacion' => 'required|string|max:500'
    ]);

    $empleado = EmpleadoAutorizado::findOrFail($id);
    
    if (!$empleado->es_activo) {
        return response()->json([
            'success' => false,
            'message' => 'El empleado ya está retirado'
        ], 400);
    }

    DB::transaction(function () use ($empleado, $request) {
        // Marcar como inactivo
        $empleado->es_activo = false;
        $empleado->save();

        // Registrar observación
        $empleado->observaciones()->create([
            'observacion' => $request->observacion,
            'tipo' => 'retiro'
        ]);
        if ($user = $empleado->user()->first()) {  // Carga explícitamente el modelo
            if($user->id_estado_usuario !=3 || $user && $user->id_estado_usuario != 4 ){
$user->id_estado_usuario = 2;
    $user->save();
            }
    
}
        // Registrar movimiento si tiene usuario asociado
        movimiento::create([
            'id_usuario' => auth()->id(),
                'descripcion' => 'se desincorporo a ' .$empleado->nombre." ".$empleado->apellido." ".$empleado->nacionalidad."-".$empleado->cedula,
                'id_usuario_afectado'=> $empleado->usuario->id_usuario ?? null,
            ]);
    });

    return response()->json([
        'success' => true,
        'message' => 'Empleado retirado correctamente'
    ]);
}

public function incorporar(Request $request, $id)
{
    $request->validate([
        'observacion' => 'required|string|max:500'
    ]);

    $empleado = EmpleadoAutorizado::findOrFail($id);
    
    if ($empleado->es_activo) {
        return response()->json([
            'success' => false,
            'message' => 'El empleado ya está activo'
        ], 400);
    }

    DB::transaction(function () use ($empleado, $request) {
        // Marcar como activo
        $empleado->es_activo = true;
        $empleado->save();

        // Registrar observación
        $empleado->observaciones()->create([
            'observacion' => $request->observacion,
            'tipo' => 'incorporacion'
        ]);
         if ($user = $empleado->user()->first()) {  // Carga explícitamente el modelo
            if($user->id_estado_usuario !=3){
$user->id_estado_usuario = 1;
    $user->save();
            }}
        // Registrar movimiento si tiene usuario asociado
        movimiento::create([
            'id_usuario' => auth()->id(),
                'descripcion' => 'se incorporo a ' .$empleado->nombre." ".$empleado->apellido." ".$empleado->nacionalidad."-".$empleado->cedula,
                'id_usuario_afectado'=> $empleado->usuario->id_usuario ?? null,
            ]);
        
    });

    return response()->json([
        'success' => true,
        'message' => 'Empleado incorporado correctamente'
    ]);
}
// En EmpleadoAutorizadoController.php
public function historial($id)
{
    $empleado = EmpleadoAutorizado::with(['usuario', 'observaciones'])->findOrFail($id);
    
    // Colección para todos los eventos
    $eventos = collect();
    
    // 1. Evento de creación del empleado
    $eventos->push([
        'tipo' => 'creacion_empleado',
        'fecha' => $empleado->created_at,
        'titulo' => 'Empleado creado',
        'descripcion' => 'Registro inicial del empleado',
        'icono' => 'user-plus',
        'color' => 'primary'
    ]);
    
    // 2. Evento de creación de usuario (si existe)
    if ($empleado->usuario) {
        $eventos->push([
            'tipo' => 'creacion_usuario',
            'fecha' => $empleado->usuario->created_at,
            'titulo' => 'Solicitud de acceso creada',
            'descripcion' => 'Solicitud de acceso al sistema registrada',
            'icono' => 'file-alt',
            'color' => 'info'
        ]);
        
        // 3. Eventos de aceptación/rechazo (de movimientos)
        $movimientos = movimiento::where('id_usuario_afectado', $empleado->usuario->id_usuario)
            ->where(function($query) {
                $query->where('descripcion', 'like', '%acept%')
                      ->orWhere('descripcion', 'like', '%rechaz%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($movimiento) {
                $esAceptacion = str_contains(strtolower($movimiento->descripcion), 'acept');
                
                return [
                    'tipo' => $esAceptacion ? 'aceptacion' : 'rechazo',
                    'fecha' => $movimiento->created_at,
                    'titulo' => 'Solicitud ' . ($esAceptacion ? 'aceptada' : 'rechazada'),
                    'descripcion' => $movimiento->descripcion,
                    'icono' => $esAceptacion ? 'check-circle' : 'times-circle',
                    'color' => $esAceptacion ? 'success' : 'danger',
                    'usuario' => $movimiento->usuario->nombre_usuario ?? 'Sistema'
                ];
            });
        
        $eventos = $eventos->merge($movimientos);
    }
    
    // 4. Eventos de incorporación/retiro (de observaciones)
    $observaciones = $empleado->observaciones
        ->map(function($observacion) {
            return [
                'tipo' => $observacion->tipo,
                'fecha' => $observacion->created_at,
                'titulo' => $observacion->tipo == 'retiro' ? 'Desincorporación' : 'Incorporación',
                'descripcion' => $observacion->observacion,
                'icono' => $observacion->tipo == 'retiro' ? 'user-minus' : 'user-plus',
                'color' => $observacion->tipo == 'retiro' ? 'danger' : 'success'
            ];
        });
    
    $eventos = $eventos->merge($observaciones);
    
    // Ordenar todos los eventos por fecha (más reciente primero)
    $eventos = $eventos->sortByDesc('fecha')->values();
    
    // Paginación manual
    $page = request()->get('page', 1);
    $perPage = 5;
    $paginated = new LengthAwarePaginator(
        $eventos->forPage($page, $perPage),
        $eventos->count(),
        $perPage,
        $page,
        ['path' => request()->url()]
    );
    
    return view('empleados.historial', [
        'empleado' => $empleado,
        'historial' => $paginated
    ]);
}
<<<<<<< HEAD
=======

public function descargarHistorial($id)
{
    $empleado = EmpleadoAutorizado::with(['usuario', 'observaciones'])->findOrFail($id);

    // Lógica del historial (la misma del método `historial`)
    $eventos = collect();

    $eventos->push([
        'tipo' => 'creacion_empleado',
        'fecha' => $empleado->created_at,
        'titulo' => 'Empleado creado',
        'descripcion' => 'Registro inicial del empleado',
        'icono' => 'user-plus',
        'color' => 'primary'
    ]);

    if ($empleado->usuario) {
        $eventos->push([
            'tipo' => 'creacion_usuario',
            'fecha' => $empleado->usuario->created_at,
            'titulo' => 'Solicitud de acceso creada',
            'descripcion' => 'Solicitud de acceso al sistema registrada',
            'icono' => 'file-alt',
            'color' => 'info'
        ]);

        $movimientos = movimiento::where('id_usuario_afectado', $empleado->usuario->id_usuario)
            ->where(function($query) {
                $query->where('descripcion', 'like', '%acept%')
                      ->orWhere('descripcion', 'like', '%rechaz%');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($movimiento) {
                $esAceptacion = str_contains(strtolower($movimiento->descripcion), 'acept');

                return [
                    'tipo' => $esAceptacion ? 'aceptacion' : 'rechazo',
                    'fecha' => $movimiento->created_at,
                    'titulo' => 'Solicitud ' . ($esAceptacion ? 'aceptada' : 'rechazada'),
                    'descripcion' => $movimiento->descripcion,
                    'icono' => $esAceptacion ? 'check-circle' : 'times-circle',
                    'color' => $esAceptacion ? 'success' : 'danger',
                    'usuario' => $movimiento->usuario->nombre_usuario ?? 'Sistema'
                ];
            });

        $eventos = $eventos->merge($movimientos);
    }

    $observaciones = $empleado->observaciones->map(function($observacion) {
        return [
            'tipo' => $observacion->tipo,
            'fecha' => $observacion->created_at,
            'titulo' => $observacion->tipo == 'retiro' ? 'Desincorporación' : 'Incorporación',
            'descripcion' => $observacion->observacion,
            'icono' => $observacion->tipo == 'retiro' ? 'user-minus' : 'user-plus',
            'color' => $observacion->tipo == 'retiro' ? 'danger' : 'success'
        ];
    });

    $eventos = $eventos->merge($observaciones)->sortByDesc('fecha')->values();

    // Membrete y pie
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

    $pdf = Pdf::loadView('empleados.historial_pdf', [
        'empleado' => $empleado,
        'historial' => $eventos,
        'membrete' => $membrete,
        'pie_html' => $pie_html,
        'logoBase64' => $logoBase64,
    ])->setPaper('a4', 'portrait'); // puedes usar 'landscape' si prefieres

    return $pdf->download('historial_' . $empleado->cedula . '.pdf');
}
>>>>>>> origin/newbran
}
