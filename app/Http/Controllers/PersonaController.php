<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\updatePersonaRequest;
use App\Models\categoriaExclusivaPersona;
use App\Models\categoriaPersona;
use App\Models\Comunidad;
use App\Models\Domicilio;
use App\Models\movimiento;
use App\Models\Notificacion;
use App\Models\Parroquia;
use App\Models\Persona;
use App\Models\Sector;
use App\Models\Urbanizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PersonaController extends Controller
{
    public function create()
    {
        $categorias = categoriaPersona::all();
        return view('personas.registrarPersonas', compact('categorias'));
    }

   public function store(Request $request)
{
    DB::beginTransaction();
    try {
        $errors = [];
        
        // Validación básica de datos personales
        $validationErrors = $this->validarDatosPersonales($request);
        if (!empty($validationErrors)) {
            $errors = array_merge($errors, $validationErrors);
        }
        
        // Validar que el domicilio sea principal si se asigna categoría
        if ($request->id_categoria_persona && $request->id_categoria_persona !== "0" && $request->es_principal != 1) {
            $errors['es_principal'] = ['Para asignar una categoría, el domicilio debe ser principal'];
        }
        
        // Validación de categoría
        $this->validarCategoria($request, $errors);
        
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'title' => 'Error de validación',
                'message' => 'Por favor corrige los siguientes errores:',
                'errors' => $errors
            ], 422);
        }

        // Proceso de creación
        $persona = $this->crearPersona($request);
        $domicilio = $this->crearDomicilio($request, $persona);
        
        // Asignar categoría si no es "Ninguno"
        if ($request->id_categoria_persona && $request->id_categoria_persona !== "0") {
            $persona->id_categoria_persona = $request->id_categoria_persona;
            $persona->save();
            
            // Crear registro en categorias_exclusivas_personas
            categoriaExclusivaPersona::create([
                'id_persona' => $persona->id_persona,
                'id_categoria_persona' => $request->id_categoria_persona,
                'id_comunidad' => $request->comunidad,
                'tipo_regla' => 'asignacion_directa',
                'valor_regla' => 'activo',
                'es_activo' => true,
                'fecha_aprobacion' => now(),
                'id_usuario' => auth()->id()
            ]);
        }
        
        $this->registrarMovimiento($persona, 'Registro de persona');
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'title' => 'Registro exitoso',
            'message' => 'La persona ha sido registrada correctamente',
            'redirect_url' => route('personas.index')
        ], 201);

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        Log::error('Error en la base de datos: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'title' => 'Error de base de datos',
            'message' => 'Ocurrió un error al guardar los datos. Por favor intente nuevamente.'
        ], 500);
    }
}

protected function validarCategoria($request, &$errors)
{
    $idCategoria = $request->id_categoria_persona;
    $idComunidad = $request->comunidad;
    
    if ($idCategoria === "0" || empty($idCategoria)) {
        return;
    }
    
    $categoria = CategoriaPersona::with('reglasConfiguradas')->find($idCategoria);
    
    if (!$categoria) {
        $errors['id_categoria_persona'] = ['La categoría seleccionada no existe'];
        return;
    }
    
    // Validar reglas de la categoría
    if ($categoria->reglasConfiguradas) {
        $reglas = $categoria->reglasConfiguradas;
        
        // Validar si requiere comunidad
        if ($reglas->requiere_comunidad && !$idComunidad) {
            $errors['id_categoria_persona'] = ['Esta categoría requiere seleccionar una comunidad'];
            return;
        }
        
        // Validar si es única en el sistema
        if ($reglas->unico_en_sistema) {
            $existe = categoriaExclusivaPersona::where('id_categoria_persona', $idCategoria)
                ->where('es_activo', true)
                ->exists();
                
            if ($existe) {
                $errors['id_categoria_persona'] = ['Esta categoría ya está asignada a otra persona en el sistema'];
                return;
            }
        }
        
        // Validar si es única en la comunidad
        if ($idComunidad && $reglas->unico_en_comunidad) {
            $existe = categoriaExclusivaPersona::where('id_categoria_persona', $idCategoria)
                ->where('id_comunidad', $idComunidad)
                ->where('es_activo', true)
                ->exists();
                
            if ($existe) {
                $errors['id_categoria_persona'] = ['Esta categoría ya está asignada a otra persona en esta comunidad'];
                return;
            }
        }
    }
}
    
    // Métodos auxiliares:
    
    protected function validarDatosPersonales($request)
    {
        $errors = [];
        
        // Validar cédula
        if (empty($request->cedula)) {
            $errors['cedula'] = ['La cédula es obligatoria'];
        } elseif (strlen($request->cedula) < 6) {
            $errors['cedula'] = ['La cédula debe tener al menos 6 caracteres'];
        } elseif (Persona::where('cedula', $request->cedula)->exists()) {
            $errors['cedula'] = ['Esta cédula ya está registrada en el sistema'];
        }
        
        // Validar correo
        if (empty($request->correo)) {
            $errors['correo'] = ['El correo electrónico es obligatorio'];
        } elseif (!filter_var($request->correo, FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = ['Ingrese un correo electrónico válido'];
        } elseif (Persona::where('correo', $request->correo)->exists()) {
            $errors['correo'] = ['Este correo electrónico ya está registrado'];
        }
        
        return $errors;
    }
    
    protected function crearPersona($request)
    {
        $persona = new Persona();
        $persona->slug = $this->generarSlug($request->nombre);
        $persona->nombre = Str::lower($request->nombre);
        $persona->apellido = Str::lower($request->apellido);
        $persona->cedula = $request->cedula;
        $persona->correo = $request->correo;
        $persona->telefono = $request->telefono;
        $persona->genero = $request->genero;
        $persona->id_usuario = auth()->id();
        $persona->save();
        
        return $persona;
    }
    
    protected function crearDomicilio($request, $persona)
    {
        $domicilio = new Domicilio();
        $domicilio->id_persona = $persona->id_persona;
        $domicilio->id_estado = $request->estado;
        $domicilio->id_municipio = $request->municipio;
        $domicilio->id_parroquia = $request->parroquia;
        $domicilio->id_urbanizacion = $request->urbanizacion;
        $domicilio->id_sector = $request->sector;
        $domicilio->id_comunidad = $request->comunidad;
        $domicilio->calle = $request->calle;
        $domicilio->manzana = $request->manzana;
        $domicilio->bloque = $request->bloque;
        $domicilio->numero_de_vivienda = $request->num_vivienda;
        $domicilio->es_principal = $request->es_principal ?? 0;
        $domicilio->save();

        return $domicilio;
    }
    
    protected function registrarMovimiento($persona, $descripcion)
    {
        movimiento::create([
            'id_persona' => $persona->id_persona,
            'id_usuario' => auth()->id(),
            'descripcion' => $descripcion
        ]);
    }
    
    protected function generarSlug($nombre)
    {
        $slug = Str::slug($nombre);
        $count = Persona::where('slug', $slug)->count();
    
        if ($count > 0) {
            $originalSlug = $slug;
            $counter = 1;
    
            while (Persona::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        
        return $slug;
    }

    protected function returnValidationError($errors)
    {
        return response()->json([
            'success' => false,
            'title' => 'Error de validación',
            'message' => 'Por favor corrige los siguientes errores:',
            'errors' => $errors
        ], 422);
    }

    public function index()
    {
        $personas = Persona::orderBy('id_persona', 'desc')->paginate(10);
        return view('personas.listaPersonas', compact('personas'));
    }

    public function show($slug)
    {
        $persona = Persona::where('slug', $slug)->firstOrFail();
        $categorias = categoriaPersona::all();
        $domicilios = Domicilio::where('id_persona', $persona->id_persona)
            ->with('estado', 'municipio', 'parroquia', 'urbanizacion', 'sector', 'comunidad')
            ->paginate(10);

        return view('personas.persona', compact('persona', 'domicilios', 'categorias'));
    }

    public function edit($slug)
    {
        $persona = Persona::where('slug', $slug)->first();

        if ($persona) {
            return view('personas.modificarPersonas', compact('persona'));
        } else {
            return redirect()->route('personas.index');
        }
    }

    public function obtenerDomicilios($id)
    {
        $persona = Persona::with([
            'domicilios.estado',
            'domicilios.municipio',
            'domicilios.parroquia',
            'domicilios.urbanizacion',
            'domicilios.sector',
            'domicilios.comunidad'
        ])->findOrFail($id);

        return response()->json($persona->domicilios);
    }

    public function update(UpdatePersonaRequest $request, $slug)
    {
        try {
            $persona = Persona::where('slug', $slug)->first();

            if (!$persona) {
                return redirect()->route('personas.show', ['slug' => $slug])
                    ->with('error', 'Persona no encontrada con el slug: ' . $slug);
            }
            $slug = $this->generarSlug($request->input('nombre'));
            $persona->slug = $slug;
            $persona->nombre = Str::lower($request->input('nombre'));
            $persona->apellido = Str::lower($request->input('apellido'));
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->genero = $request->input('genero');
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
        $incidencias = $persona->incidencia()->orderBy('created_at', 'desc')->paginate(10);

        return view('personas.incidencias', compact('persona', 'incidencias'));
    }
}