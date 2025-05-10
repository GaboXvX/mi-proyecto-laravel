<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\updatePersonaRequest;
use App\Models\categoriaExclusivaPersona;
use App\Models\categoriaPersona;
use App\Models\Comunidad;
use App\Models\Domicilio; // Cambiar Direccion por Domicilio
use App\Models\Lider_Comunitario;
use App\Models\movimiento;
use App\Models\Notificacion;
use App\Models\Parroquia;
use App\Models\Persona;
use App\Models\ReglaEspecial;
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
            
            // Validación de categoría y sus reglas
            $categoria = CategoriaPersona::with('reglasConfiguradas')->find($request->categoria);
            
            if (!$categoria) {
                $errors['categoria'] = ['La categoría seleccionada no existe'];
            } else {
                $this->validarReglasCategoria($categoria, $request, $errors);
            }
            
            // Si hay errores, retornarlos de forma clara
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error de validación',
                    'message' => 'Por favor corrige los siguientes errores:',
                    'errors' => $errors
                ], 422);
            }

            // Proceso de creación si no hay errores
            $persona = $this->crearPersona($request);
            $domicilio = $this->crearDomicilio($request, $persona);
            
            // Aplicar reglas de categoría si existen y la categoría no es "Regular"
            if ($categoria->reglasConfiguradas && $categoria->nombre_categoria ) {
                $this->aplicarReglasCategoria($categoria, $persona, $request);
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
    
    // Validar fecha de nacimiento
   
    
    return $errors;
}

protected function validarReglasCategoria($categoria, $request, &$errors)
{
    $config = $categoria->reglasConfiguradas;
    if (!$config) {
        return;
    }
    // Validar si requiere comunidad
    if ($config->requiere_comunidad && empty($request->comunidad)) {
        $errors['comunidad'] = [$config->mensaje_error ?? 'Esta categoría requiere que seleccione una comunidad'];
    }
    
    // Validar unicidad en comunidad
    if ($config->unico_en_comunidad && $request->comunidad) {
        $existente = categoriaExclusivaPersona::where('id_categoria_persona', $categoria->id_categoria_persona)
            ->where('id_comunidad', $request->comunidad)
            ->where('es_activo', true)
            ->exists();
            
        if ($existente) {
            $errors['categoria'] = [$config->mensaje_error ?? 'Ya existe un ' . $categoria->nombre_categoria . ' en la comunidad seleccionada'];
        }
    }
    
    // Validar unicidad en sistema
    if ($config->unico_en_sistema) {
        $existente = categoriaExclusivaPersona::where('id_categoria_persona', $categoria->id_categoria_persona)
            ->where('es_activo', true)
            ->exists();
            
        if ($existente) {
            $errors['categoria'] = [$config->mensaje_error ?? 'Solo puede haber un ' . $categoria->nombre_categoria . ' registrado en el sistema'];
        }
    }
}
    
    protected function aplicarReglasCategoria($categoria, $persona, $request)
    {
         $reglas = $categoria->reglasConfiguradas;

    // Si no hay reglas configuradas, no aplicar nada
    if (!$reglas) {
        return;
    }
        $reglaData = [
            'id_persona' => $persona->id_persona,
            'id_categoria_persona' => $categoria->id_categoria_persona,
            'es_activo' => true,
            'id_usuario' => auth()->id(),
            'fecha_aprobacion' => now()
        ];
        
        // Si la categoría requiere comunidad, la añadimos
        if ($categoria->reglasConfiguradas->requiere_comunidad) {
            $reglaData['id_comunidad'] = $request->comunidad;
            $reglaData['tipo_regla'] = 'asignacion_comunidad';
        } else {
            $reglaData['tipo_regla'] = 'asignacion_sistema';
        }
        
        // Crear la regla especial
       categoriaExclusivaPersona::create($reglaData);
        
        // Registrar movimiento específico
        $this->registrarMovimiento($persona, 'Asignación de categoría: ' . $categoria->nombre_categoria);
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
        $persona->id_categoria_persona = $request->categoria;
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
        $categorias = categoriaPersona::all();
        $personas = Persona::orderBy('id_persona', 'desc')->paginate(10);
        return view('personas.listaPersonas', compact('personas', 'categorias'));
    }

    public function show($slug)
    {
        $categorias = categoriaPersona::all();
        $persona = Persona::with('categoria')->where('slug', $slug)->firstOrFail();
        $domicilios = Domicilio::where('id_persona', $persona->id_persona)
            ->with('estado', 'municipio', 'parroquia', 'urbanizacion', 'sector', 'comunidad')
            ->paginate(10);

        return view('personas.persona', compact('persona', 'domicilios', 'categorias'));
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
            $slug= $this->generarSlug($request->input('nombre'));
            $persona->slug =$slug;
            $persona->nombre = Str::lower($request->input('nombre')) ;
            $persona->apellido = Str::lower($request->input('apellido')) ;
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

        // Cargar incidencias con paginación
        $incidencias = $persona->incidencia()->orderBy('created_at', 'desc')->paginate(10);

        return view('personas.incidencias', compact('persona', 'incidencias'));
    }
}
