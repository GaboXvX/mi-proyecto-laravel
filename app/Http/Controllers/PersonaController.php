<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\updatePersonaRequest;
use App\Models\categoriaPersona;
use App\Models\Comunidad;
use App\Models\Direccion;
use App\Models\Lider_Comunitario;
use App\Models\movimiento;
use App\Models\Notificacion;
use App\Models\Parroquia;
use App\Models\Persona;
use App\Models\Sector;
use App\Models\Urbanizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PersonaController extends Controller
{
    public function create()
    {
        $categorias = categoriaPersona::all();
        return view('personas.registrarPersonas', compact('categorias'));
    }

    public function store(StorePersonaRequest $request)
    {
        try {
            // Validación adicional manual
            $errors = [];
            
            // Validar cédula
            if (Persona::where('cedula', $request->cedula)->exists()) {
                $errors['cedula'] = ['La cédula ya está registrada'];
            }
            
            // Validar correo
            if (Persona::where('correo', $request->correo)->exists()) {
                $errors['correo'] = ['El correo electrónico ya está registrado'];
            }
            
           

            // Validar líder comunitario
            if ($request->categoria == 2) {
                $liderExistente = Lider_Comunitario::where('id_comunidad', $request->comunidad)
                    ->where('estado', 1)
                    ->exists();
                    
                if ($liderExistente) {
                    $errors['categoria'] = ['Ya existe un líder activo para esta comunidad'];
                }
            }
            
            if (!empty($errors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Errores de validación',
                    'errors' => $errors
                ], 422);
            }

            // Resto de tu lógica de creación...
            $persona = new Persona();
            $slug = Str::slug($request->input('nombre'));
            $count = Persona::where('slug', $slug)->count();

            if ($count > 0) {
                $originalSlug = $slug;
                $counter = 1;

                while (Persona::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $persona->slug = $slug;
            $persona->nombre = Str::lower( $request->input('nombre'));
            $persona->apellido =Str::lower( $request->input('apellido'));
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->genero = $request->input('genero');
            $persona->altura = $request->input('altura')." cm";
            $persona->fecha_nacimiento = $request->input('fecha_nacimiento');
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->id_categoriaPersona = $request->input('categoria');
            $persona->save();

            $direccion = new Direccion();
            $direccion->id_comunidad = $request->input('comunidad');
            $direccion->id_sector = $request->input('sector');
            $direccion->calle = $request->input('calle');
            $direccion->manzana = $request->input('manzana');
            $direccion->numero_de_vivienda = $request->input('num_vivienda');
            $direccion->bloque = $request->input('bloque');
            $direccion->id_parroquia = $request->input('parroquia');
            $direccion->id_urbanizacion = $request->input('urbanizacion');
            $direccion->id_persona = $persona->id_persona;
            $direccion->es_principal = $request->input('es_principal', 0);
            $direccion->id_estado = $request->input('estado');
            $direccion->id_municipio = $request->input('municipio');
            $direccion->save();

            $persona->direccion()->save($direccion);

            if ($persona->id_categoriaPersona == 2) {
                $liderComunitario = new Lider_Comunitario();
                $liderComunitario->id_persona = $persona->id_persona;
                $liderComunitario->id_comunidad = $request->input('comunidad');
                $liderComunitario->estado = 1;
                $liderComunitario->save();
                $movimiento = new movimiento();
                $movimiento->id_persona = $persona->id_persona;
                $movimiento->id_usuario = auth()->user()->id_usuario;
                $movimiento->descripcion = 'se registro como lider comunitario';
                $movimiento->save();
               
            }
            $movimiento = new movimiento();
                $movimiento->id_persona = $persona->id_persona;
                $movimiento->id_usuario = auth()->user()->id_usuario;
                $movimiento->descripcion = 'se registro una persona';
                $movimiento->save();
               
            return response()->json([
                'status' => 'success',
                'message' => 'Persona registrada exitosamente',
                'data' => [
                    'persona' => $persona,
                    'direccion' => $direccion
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error inesperado en el servidor',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    public function index()
    {
        $categorias = categoriaPersona::all();
        $personas = Persona::orderBy('id_persona', 'desc')->paginate(10);
        return view('personas.listaPersonas', compact('personas', 'categorias'));
    }

    public function show($slug)
    {
        $categorias = categoriaPersona::all();
        $persona = Persona::where('slug', $slug)->firstOrFail();

        if ($persona) {
            $direcciones = $persona->direccion()->paginate(5);

            if (request()->ajax()) {
                return response()->json([
                    'direcciones' => view('partials.direcciones-list', compact('direcciones'))->render(),
                    'pagination' => view('partials.pagination-links', compact('direcciones'))->render()
                ]);
            }

            return view('personas.persona', compact('persona', 'categorias', 'direcciones'));
        } else {
            return redirect()->route('personas.index');
        }
    }

    public function edit($slug)
    {
        $categorias = categoriaPersona::all();
        $persona = Persona::where('slug', $slug)->first();

        if ($persona) {
            return view('personas.modificarPersonas', compact('persona', 'categorias'));
        } else {
            return redirect()->route('personas.index');
        }
    }
    public function obtenerDirecciones($id)
{
    $persona = Persona::with([
        'direccion.estado',
        'direccion.municipio',
        'direccion.parroquia',
        'direccion.urbanizacion',
        'direccion.sector',
        'direccion.comunidad'
    ])->findOrFail($id);

    return response()->json($persona->direccion);
}
    public function update(UpdatePersonaRequest $request, $slug)
    {
        try {
            $persona = Persona::where('slug', $slug)->first();

            if (!$persona) {
                return redirect()->route('personas.show', ['slug' => $slug])
                    ->with('error', 'Persona no encontrada con el slug: ' . $slug);
            }

            $persona->nombre = Str::lower($request->input('nombre')) ;
            $persona->apellido = Str::lower($request->input('apellido')) ;
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->genero = $request->input('genero');
            $persona->fecha_nacimiento = $request->input('fecha_nacimiento');
            $persona->altura = $request->input('altura') . " cm";
            $persona->save();
            $movimiento = new movimiento();
            $movimiento->id_persona = $persona->id_persona;
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->descripcion = 'se modifico una persona';
            $movimiento->save();
           
            return redirect()->route('personas.show', ['slug' => $slug])
                ->with('success', 'Persona actualizada con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('personas.show', ['slug' => $slug])
                ->with('error', 'Error al actualizar la persona: ' . $e->getMessage());
        }
    }

    public function buscar(Request $request)
    {
        $query = $request->input('query');
        $personas = Persona::where('cedula', 'LIKE', "%{$query}%")->get();
        return response()->json($personas);
    }

    public function validarCedula(Request $request)
    {
        $exists = Persona::where('cedula', $request->input('cedula'))->exists();
        return response()->json(['exists' => $exists]);
    }

    public function validarCorreo(Request $request)
    {
        $exists = Persona::where('correo', $request->input('correo'))->exists();
        return response()->json(['exists' => $exists]);
    }

    public function getPersonaData($slug)
    {
        $persona = Persona::where('slug', $slug)->firstOrFail();
        return response()->json($persona);
    }

    public function verIncidencias($slug)
{
    $persona = Persona::where('slug', $slug)->firstOrFail();

    // Cargar incidencias con paginación
    $incidencias = $persona->incidencias()->orderBy('created_at', 'desc')->paginate(10);

    return view('personas.incidencias', compact('persona', 'incidencias'));
}
}
