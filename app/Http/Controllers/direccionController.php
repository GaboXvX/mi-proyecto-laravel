<?php

namespace App\Http\Controllers;

use App\Models\categoriaExclusivaPersona;
use App\Models\categoriaPersona;
use App\Models\Direccion;
use App\Models\Lider_Comunitario;
use App\Models\movimiento;
use App\Models\Persona;
use App\Models\ReglaEspecial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;

class direccionController extends Controller
{
    public function index(Request $request, $slug)
    {
        $categorias = CategoriaPersona::all();

        $persona = Persona::where('slug', $slug)->first();

        if (!$persona) {
            return redirect()->back()->withErrors(['error' => 'Persona no encontrada']);
        }

        return view('personas.agregarDireccion', compact('persona', 'categorias'));
    }

   public function store(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $errors = [];
        
        // Validación básica de dirección
        $validationErrors = $this->validarDatosDireccion($request, $id);
        if (!empty($validationErrors)) {
            $errors = array_merge($errors, $validationErrors);
        }
        
        // Validación de categoría y sus reglas
        $categoria = CategoriaPersona::find($request->categoria);

        if (!$categoria) {
            $errors['categoria'] = ['La categoría seleccionada no existe'];
        } elseif ($categoria->reglasConfiguradas === null && $categoria->nombre_categoria !== 'Regular') {
            $errors['categoria'] = ['La categoría seleccionada no tiene reglas configuradas'];
        } else {
            $this->validarReglasCategoria($categoria, $request, $errors, $id);
        }
        
        // Si hay errores, retornarlos de forma clara
        if (!empty($errors)) {
            return $this->returnValidationError($errors);
        }

        // Verificar si la dirección ya existe
        $direccionExistente = Direccion::where('id_persona', $id)
            ->where('id_estado', $request->estado)
            ->where('id_municipio', $request->municipio)
            ->where('id_parroquia', $request->parroquia)
            ->where('id_urbanizacion', $request->urbanizacion)
            ->where('id_sector', $request->sector)
            ->where('id_comunidad', $request->comunidad)
            ->where('calle', $request->calle)
            ->where('manzana', $request->manzana)
            ->where('numero_de_vivienda', $request->numero_de_vivienda)
            ->where('bloque', $request->bloque)
            ->first();

        if ($direccionExistente) {
            return response()->json([
                'success' => false,
                'title' => 'Error de validación',
                'message' => 'La dirección ya está registrada para esta persona.',
            ], 422);
        }

        // Obtener la persona
        $persona = Persona::findOrFail($id);

        // Crear la dirección
        $direccion = $this->crearDireccion($request, $persona);
        
        // Aplicar reglas de categoría si existen y la categoría no es "Regular"
        if ($categoria && $categoria->reglasConfiguradas && $categoria->nombre_categoria !== 'Regular') {
            $this->aplicarReglasCategoria($categoria, $persona, $request);
        }
        
        // Actualizar categoría de la persona si es diferente
        if ($persona->id_categoria_persona != $request->categoria) {
            $persona->id_categoria_persona = $request->categoria;
            $persona->save();
        }
        
        // Registrar movimiento
        $this->registrarMovimientoDireccion($persona, $direccion, 'se añadió una nueva dirección');
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'title' => 'Registro exitoso',
            'message' => 'Dirección guardada correctamente',
            'redirect_url' => route('personas.show', ['slug' => $persona->slug])
        ], 201);

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        Log::error('Error al guardar dirección: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'title' => 'Error de base de datos',
            'message' => 'Ocurrió un error al guardar la dirección. Por favor intente nuevamente.'
        ], 500);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error inesperado: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'title' => 'Error inesperado',
            'message' => 'Ocurrió un error inesperado. Por favor intente nuevamente.'
        ], 500);
    }
}

// Métodos auxiliares:

protected function validarDatosDireccion($request, $personaId)
{
    $errors = [];

    // Validar campos básicos de dirección
    $validator = FacadesValidator::make($request->all(), [
        'estado' => 'required|exists:estados,id_estado',
        'municipio' => 'required|exists:municipios,id_municipio',
        'parroquia' => 'required|string|max:255',
        'urbanizacion' => 'required|string|max:255',
        'sector' => 'required|string|max:255',
        'comunidad' => 'required|string|max:255',
        'calle' => 'nullable|string|max:255',
        'manzana' => 'nullable|string|max:255',
        'numero_de_vivienda' => 'required|string|max:255',
        'bloque' => 'nullable|string|max:255',
        'categoria' => 'required|integer|exists:categorias_personas,id_categoria_persona',
    ]);

    if ($validator->fails()) {
        $errors = array_merge($errors, $validator->errors()->toArray());
    }

    // Verificar si ya existe una dirección principal
    if ($request->es_principal && Direccion::where('id_persona', $personaId)->where('es_principal', 1)->exists()) {
        $errors['es_principal'] = ['Ya existe una dirección marcada como principal para esta persona.'];
    }

    return $errors;
}

protected function validarReglasCategoria($categoria, $request, &$errors, $personaId)
{
    $config = $categoria->reglasConfiguradas;

    // Validar si requiere comunidad
    if ($config->requiere_comunidad && empty($request->comunidad)) {
        $errors['comunidad'] = [$config->mensaje_error ?? 'Esta categoría requiere que seleccione una comunidad'];
    }

    // Validar unicidad en comunidad
    if ($config->unico_en_comunidad && $request->comunidad) {
        $existente = categoriaExclusivaPersona::where('id_categoria_persona', $categoria->id_categoria_persona)
            ->where('id_comunidad', $request->comunidad)
            ->where('es_activo', true)
            ->where('id_persona', '!=', $personaId) // Excluir a la persona actual
            ->exists();

        if ($existente) {
            $errors['categoria'] = [$config->mensaje_error ?? 'Ya existe un ' . $categoria->nombre_categoria . ' en la comunidad seleccionada'];
        }
    }
}

protected function aplicarReglasCategoria($categoria, $persona, $request)
{
    // Si la categoría no tiene reglas configuradas, no registrar nada
    if (!$categoria->reglasConfiguradas) {
        return;
    }

    // Primero desactivar cualquier regla previa para esta persona y categoría
    categoriaExclusivaPersona::where('id_persona', $persona->id_persona)
        ->where('id_categoria_persona', $categoria->id_categoria_persona)
        ->update(['es_activo' => false]);

    // Crear nueva regla especial
    $reglaData = [
        'id_persona' => $persona->id_persona,
        'id_categoria_persona' => $categoria->id_categoria_persona,
        'es_activo' => true,
        'id_usuario' => auth()->id(),
        'fecha_aprobacion' => now(),
        'tipo_regla' => 'asignacion_comunidad'
    ];

    // Si la categoría requiere comunidad, la añadimos
    if ($categoria->reglasConfiguradas->requiere_comunidad) {
        $reglaData['id_comunidad'] = $request->comunidad;
    }

    categoriaExclusivaPersona::create($reglaData);
}

protected function crearDireccion($request, $persona)
{
    $direccion = new Direccion();
    $direccion->id_persona = $persona->id_persona;
    $direccion->id_estado = $request->estado;
    $direccion->id_municipio = $request->municipio;
    $direccion->id_parroquia = $request->parroquia;
    $direccion->id_urbanizacion = $request->urbanizacion;
    $direccion->id_sector = $request->sector;
    $direccion->id_comunidad = $request->comunidad;
    $direccion->calle = $request->calle;
    $direccion->manzana = $request->manzana;
    $direccion->numero_de_vivienda = $request->numero_de_vivienda;
    $direccion->bloque = $request->bloque;
    $direccion->es_principal = $request->es_principal;
    $direccion->save();
    
    return $direccion;
}

protected function registrarMovimientoDireccion($persona, $direccion, $descripcion)
{
    movimiento::create([
        'id_persona' => $persona->id_persona,
        'id_usuario' => auth()->id(),
        'descripcion' => $descripcion,
        'id_direccion' => $direccion->id_direccion
    ]);
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
    

public function update(Request $request, $id, $idPersona)
{
    DB::beginTransaction();
    try {
        $errors = [];
        $direccion = Direccion::findOrFail($id);
        $persona = Persona::findOrFail($idPersona);

        // Validación básica de dirección
        $validationErrors = $this->validarDatosDireccion($request, $idPersona);
        if (!empty($validationErrors)) {
            $errors = array_merge($errors, $validationErrors);
        }

        // Validación de categoría y sus reglas
        $categoria = CategoriaPersona::find($request->categoria);

        if (!$categoria) {
            $errors['categoria'] = ['La categoría seleccionada no existe'];
        } elseif ($categoria->reglasConfiguradas === null && $categoria->nombre_categoria !== 'Regular') {
            $errors['categoria'] = ['La categoría seleccionada no tiene reglas configuradas'];
        } else {
            $this->validarReglasCategoria($categoria, $request, $errors, $idPersona);
        }

        // Si hay errores, retornarlos de forma clara
        if (!empty($errors)) {
            return $this->returnValidationError($errors);
        }

        // Verificar si la dirección ya existe (excluyendo la actual)
        $direccionExistente = Direccion::where('id_persona', $idPersona)
            ->where('id_estado', $request->estado)
            ->where('id_municipio', $request->municipio)
            ->where('id_parroquia', $request->parroquia)
            ->where('id_urbanizacion', $request->urbanizacion)
            ->where('id_sector', $request->sector)
            ->where('id_comunidad', $request->comunidad)
            ->where('calle', $request->calle)
            ->where('manzana', $request->manzana)
            ->where('numero_de_vivienda', $request->numero_de_vivienda)
            ->where('bloque', $request->bloque)
            ->where('id_direccion', '!=', $id)
            ->first();

        if ($direccionExistente) {
            return response()->json([
                'success' => false,
                'title' => 'Error de validación',
                'message' => 'La dirección ya está registrada para esta persona.',
            ], 422);
        }

        // Actualizar la dirección
        $direccion->update([
            'id_estado' => $request->estado,
            'id_municipio' => $request->municipio,
            'id_parroquia' => $request->parroquia,
            'id_urbanizacion' => $request->urbanizacion,
            'id_sector' => $request->sector,
            'id_comunidad' => $request->comunidad,
            'calle' => $request->calle,
            'manzana' => $request->manzana,
            'bloque' => $request->bloque,
            'numero_de_vivienda' => $request->numero_de_vivienda
        ]);

        // Aplicar reglas de categoría si existen y la categoría no es "Regular"
        if ($categoria && $categoria->reglasConfiguradas && $categoria->nombre_categoria !== 'Regular') {
            // Verificar si ya tiene esta categoría activa en esta comunidad
            $categoriaActiva = categoriaExclusivaPersona::where('id_persona', $persona->id_persona)
                ->where('id_categoria_persona', $categoria->id_categoria_persona)
                ->where('id_comunidad', $direccion->id_comunidad)
                ->where('es_activo', true)
                ->exists();
        
            if (!$categoriaActiva) {
                $this->aplicarReglasCategoria($categoria, $persona, $request);
            }
        } else {
            // Si se cambia a Regular, desactivar cualquier categoría exclusiva para esta comunidad
            categoriaExclusivaPersona::where('id_persona', $persona->id_persona)
                ->where('id_comunidad', $direccion->id_comunidad)
                ->update(['es_activo' => false]);
        }

        // Actualizar categoría de la persona si es diferente
        if ($persona->id_categoria_persona != $request->categoria) {
            $persona->id_categoria_persona = $request->categoria;
            $persona->save();
        }

        // Registrar movimiento
        $this->registrarMovimientoDireccion($persona, $direccion, 'se modificó una dirección');
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'title' => 'Actualización exitosa',
            'message' => 'Dirección actualizada correctamente',
            'redirect_url' => route('personas.show', ['slug' => $persona->slug])
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        Log::error('Error al actualizar dirección: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'title' => 'Error de base de datos',
            'message' => 'Ocurrió un error al actualizar la dirección. Por favor intente nuevamente.'
        ], 500);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error inesperado: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'title' => 'Error inesperado',
            'message' => 'Ocurrió un error inesperado. Por favor intente nuevamente.'
        ], 500);
    }
}

    public function checkLiderStatus(Request $request)
    {
        $personaId = $request->input('persona_id');
        $comunidadId = $request->input('comunidad_id');

        $persona = Persona::find($personaId);
        $esLider = $persona->lider_Comunitario()->where('id_comunidad', $comunidadId)->where('estado', 1)->exists();

        return response()->json(['esLider' => $esLider]);
    }

    public function marcarPrincipal(Request $request)
    {
        $direccionId = $request->input('id_direccion');
        $direccion = Direccion::find($direccionId);
    
        if ($direccion) {
            // Desmarcar solo la dirección principal actual de la persona
            Direccion::where('id_persona', $direccion->id_persona)
                ->where('es_principal', 1)
                ->update(['es_principal' => 0]);
    
            // Marcar la nueva dirección como principal
            $direccion->es_principal = 1;
            $direccion->save();
    
            return redirect()->route('personas.show', ['slug' => $direccion->persona->slug])->with('success', 'La dirección se marcó como principal');
        }
    
        return redirect()->back()->with('error', 'Dirección no encontrada');
    }
}
