<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use App\Models\Institucion;
use App\Models\InstitucionEstacion;
use App\Models\personalReparacion;
use App\Models\User;
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
            'genero' => $empleado->genero, // Nuevo: incluir género
            'id_institucion' => $empleado->id_institucion,
            'id_institucion_estacion' => $empleado->id_institucion_estacion,
        ]);
    }

    return response()->json(['encontrado' => false]);
}

public function index()
    {
        $personal = personalReparacion::with(['institucion', 'institucionEstacion', 'usuario'])->get();
        return view('personal-reparacion.listaPersonal', compact('personal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instituciones = Institucion::all();
        $usuarios = User::all();
        return view('personal-reparacion.agregarPersonal', compact('instituciones', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'id_institucion' => 'required|exists:instituciones,id_institucion',
        'id_institucion_estacion' => 'required|exists:instituciones_estaciones,id_institucion_estacion',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'nacionalidad' => 'required|string|max:255',
        'cedula' => 'required|string|max:255|unique:personal_reparaciones,cedula',
        'telefono' => 'required|string|max:255',
        'genero' => 'required|in:M,F,O',
    ], [
        'id_institucion.required' => 'La institución de apoyo es obligatoria.',
        'id_institucion.exists' => 'La institución seleccionada no es válida.',
        'id_institucion_estacion.required' => 'La estación es obligatoria.',
        'id_institucion_estacion.exists' => 'La estación seleccionada no es válida.',
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
        'apellido.required' => 'El apellido es obligatorio.',
        'apellido.max' => 'El apellido no puede tener más de 255 caracteres.',
        'nacionalidad.required' => 'La nacionalidad es obligatoria.',
        'nacionalidad.max' => 'La nacionalidad no puede tener más de 255 caracteres.',
        'cedula.required' => 'La cédula es obligatoria.',
        'cedula.unique' => 'La cédula ya está registrada.',
        'cedula.max' => 'La cédula no puede tener más de 255 caracteres.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'telefono.max' => 'El teléfono no puede tener más de 255 caracteres.',
        'genero.required' => 'El género es obligatorio.',
        'genero.in' => 'El género seleccionado no es válido.'
    ]);

    // Generar el slug
    $slug = Str::slug(Str::lower($request->input('nombre') . ' ' . $request->input('apellido')));
    $originalSlug = $slug;
    $counter = 1;
    
    while (personalReparacion::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    try {
        $personal = personalReparacion::create([
            ...$validated,
            'slug' => $slug,
            'id_usuario' => auth()->id()
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Personal de reparación creado exitosamente.',
            'redirect' => route('personal-reparacion.index')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al crear el personal de reparación: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(personalReparacion $personalReparacion)
    {
        return view('personal-reparacion.show', compact('personalReparacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(personalReparacion $personalReparacion)
    {
        $instituciones = Institucion::all();
        $estaciones = InstitucionEstacion::where('id_institucion', $personalReparacion->id_institucion)->get();
        $usuarios = User::all();
        
        return view('personal-reparacion.modificarPersonal', compact('personalReparacion', 'instituciones', 'estaciones', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, personalReparacion $personalReparacion)
{
    $validated = $request->validate([
        'id_institucion' => 'required|exists:instituciones,id_institucion',
        'id_institucion_estacion' => 'required|exists:instituciones_estaciones,id_institucion_estacion',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'telefono' => 'required|string|max:255',
        'genero' => 'required|in:M,F,O',
    ], [
        'id_institucion.required' => 'La institución de apoyo es obligatoria.',
        'id_institucion.exists' => 'La institución seleccionada no es válida.',
        'id_institucion_estacion.required' => 'La estación es obligatoria.',
        'id_institucion_estacion.exists' => 'La estación seleccionada no es válida.',
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
        'apellido.required' => 'El apellido es obligatorio.',
        'apellido.max' => 'El apellido no puede tener más de 255 caracteres.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'telefono.max' => 'El teléfono no puede tener más de 255 caracteres.',
        'genero.required' => 'El género es obligatorio.',
        'genero.in' => 'El género seleccionado no es válido.'
    ]);

    try {
        $personalReparacion->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Personal de reparación actualizado exitosamente.',
            'data' => $personalReparacion
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar el personal de reparación: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
   

    /**
     * Obtener estaciones por institución (AJAX)
     */
    /**
 * Obtener estaciones por institución (AJAX)
 */
public function getEstacionesPorInstitucion($institucionId)
{
    try {
        // Validar que el ID de institución sea numérico
        if (!is_numeric($institucionId)) {
            return response()->json([
                'success' => false,
                'message' => 'ID de institución inválido'
            ], 400);
        }

        // Obtener las estaciones relacionadas con la institución
        $estaciones = InstitucionEstacion::where('id_institucion', $institucionId)
            ->select('id_institucion_estacion', 'nombre', 'codigo_estacion')
            ->orderBy('nombre', 'asc')
            ->get();

        // Formatear la respuesta
        $data = $estaciones->map(function ($estacion) {
            return [
                'id' => $estacion->id_institucion_estacion,
                'nombre' => $estacion->nombre,
                'codigo' => $estacion->codigo_estacion,
                'text' => $estacion->nombre . ' (' . $estacion->codigo_estacion . ')' // Opcional: para mostrar más información
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar estaciones: ' . $e->getMessage()
        ], 500);
    }
}
 public function validarCedula(Request $request)
    {
        $cedula = $request->query('cedula');
        $exists = \App\Models\PersonalReparacion::where('cedula', $cedula)->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'La cédula ya está registrada.' : 'Cédula disponible.'
        ]);
    }
public function validarCedulaDirecta($cedula)
{
    $exists = \App\Models\PersonalReparacion::where('cedula', $cedula)->exists();

    return response()->json([
        'success' => true,
        'exists' => $exists,
        'message' => $exists ? 'La cédula ya está registrada.' : 'Cédula disponible.'
    ]);
}

}
