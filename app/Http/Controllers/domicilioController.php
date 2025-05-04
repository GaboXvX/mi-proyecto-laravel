<?php

namespace App\Http\Controllers;

use App\Models\categoriaExclusivaPersona;
use App\Models\categoriaPersona;
use App\Models\Domicilio; // Cambiar Direccion por Domicilio
use App\Models\Lider_Comunitario;
use App\Models\movimiento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class DomicilioController extends Controller
{
    public function index(Request $request, $slug)
    {
        $categorias = CategoriaPersona::all();

        $persona = Persona::where('slug', $slug)->first();

        if (!$persona) {
            return redirect()->back()->withErrors(['error' => 'Persona no encontrada']);
        }

        return view('personas.agregarDomicilio', compact('persona', 'categorias'));
    }

    public function store(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $errors = [];
            
            // Validación básica de domicilio
            $validationErrors = $this->validarDatosDomicilio($request, $id);
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

            // Verificar si el domicilio ya existe
            $domicilioExistente = Domicilio::where('id_persona', $id)
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

            if ($domicilioExistente) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error de validación',
                    'message' => 'El domicilio ya está registrado para esta persona.',
                ], 422);
            }

            // Obtener la persona
            $persona = Persona::findOrFail($id);

            // Crear el domicilio
            $domicilio = $this->crearDomicilio($request, $persona);
            
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
            $this->registrarMovimientoDomicilio($persona, $domicilio, 'Se añadió un nuevo domicilio');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'title' => 'Registro exitoso',
                'message' => 'Domicilio guardado correctamente',
                'redirect_url' => route('personas.show', ['slug' => $persona->slug])
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Error al guardar domicilio: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'title' => 'Error de base de datos',
                'message' => 'Ocurrió un error al guardar el domicilio. Por favor intente nuevamente.'
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

    protected function validarDatosDomicilio($request, $personaId)
    {
        $errors = [];

        // Validar campos básicos de domicilio
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

        // Verificar si ya existe un domicilio principal
        if ($request->es_principal && Domicilio::where('id_persona', $personaId)->where('es_principal', 1)->exists()) {
            $errors['es_principal'] = ['Ya existe un domicilio marcado como principal para esta persona.'];
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

    // Validar que la persona no tenga ya otra categoría exclusiva activa
   

    // Validar que esta categoría no esté ya asignada a esta persona en otra comunidad
    if ($config->una_categoria_por_comunidad_persona && !empty($request->comunidad)) {
        $categoriaRepetida = categoriaExclusivaPersona::where('id_persona', $personaId)
            ->where('id_categoria_persona', $categoria->id_categoria_persona)
            ->where('id_comunidad', '!=', $request->comunidad)
            ->where('es_activo', true)
            ->exists();

        if ($categoriaRepetida) {
            $errors['comunidad'] = [$config->mensaje_error ?? 'Esta categoría ya está asignada a esta persona en otra comunidad'];
        }
    }
}


    protected function aplicarReglasCategoria($categoria, $persona, $request)
    {
        if (!$categoria->reglasConfiguradas) {
            return;
        }
    
        // Desactivar cualquier categoría exclusiva activa para la persona
        categoriaExclusivaPersona::where('id_persona', $persona->id_persona)
            ->where('es_activo', true)
            ->update(['es_activo' => false]);
    
        // Crear nueva categoría exclusiva
        $reglaData = [
            'id_persona' => $persona->id_persona,
            'id_categoria_persona' => $categoria->id_categoria_persona,
            'es_activo' => true,
            'id_usuario' => auth()->id(),
            'fecha_aprobacion' => now(),
            'tipo_regla' => 'asignacion_comunidad'
        ];
    
        if ($categoria->reglasConfiguradas->requiere_comunidad) {
            $reglaData['id_comunidad'] = $request->comunidad;
        }
    
        categoriaExclusivaPersona::create($reglaData);
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
        $domicilio->numero_de_vivienda = $request->numero_de_vivienda;
        $domicilio->bloque = $request->bloque;
        $domicilio->es_principal = $request->es_principal;
        $domicilio->save();
        
        return $domicilio;
    }

    protected function registrarMovimientoDomicilio($persona, $domicilio, $descripcion)
    {
        movimiento::create([
            'id_persona' => $persona->id_persona,
            'id_usuario' => auth()->id(),
            'descripcion' => $descripcion,
            'id_domicilio' => $domicilio->id_domicilio
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
            $domicilio = Domicilio::findOrFail($id);
            $persona = Persona::findOrFail($idPersona);

            // Validación básica de domicilio
            $validationErrors = $this->validarDatosDomicilio($request, $idPersona);
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

            // Verificar si el domicilio ya existe (excluyendo el actual)
            $domicilioExistente = Domicilio::where('id_persona', $idPersona)
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
                ->where('id_domicilio', '!=', $id)
                ->first();

            if ($domicilioExistente) {
                return response()->json([
                    'success' => false,
                    'title' => 'Error de validación',
                    'message' => 'El domicilio ya está registrado para esta persona.',
                ], 422);
            }

            // Actualizar el domicilio
            $domicilio->update([
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
                $this->aplicarReglasCategoria($categoria, $persona, $request);
            } else {
                // Si se cambia a Regular, desactivar cualquier categoría exclusiva para esta comunidad
                categoriaExclusivaPersona::where('id_persona', $persona->id_persona)
                    ->where('id_comunidad', $domicilio->id_comunidad)
                    ->update(['es_activo' => false]);
            }

            // Actualizar categoría de la persona si es diferente
            if ($persona->id_categoria_persona != $request->categoria) {
                $persona->id_categoria_persona = $request->categoria;
                $persona->save();
            }

            // Registrar movimiento
            $this->registrarMovimientoDomicilio($persona, $domicilio, 'Se modificó un domicilio');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'title' => 'Actualización exitosa',
                'message' => 'Domicilio actualizado correctamente',
                'redirect_url' => route('personas.show', ['slug' => $persona->slug])
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar domicilio: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'title' => 'Error de base de datos',
                'message' => 'Ocurrió un error al actualizar el domicilio. Por favor intente nuevamente.'
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
        $domicilioId = $request->input('id_domicilio');
        $domicilio = Domicilio::find($domicilioId);
    
        if ($domicilio) {
            // Desmarcar solo el domicilio principal actual de la persona
            Domicilio::where('id_persona', $domicilio->id_persona)
                ->where('es_principal', 1)
                ->update(['es_principal' => 0]);
    
            // Marcar el nuevo domicilio como principal
            $domicilio->es_principal = 1;
            $domicilio->save();
    
            return redirect()->route('personas.show', ['slug' => $domicilio->persona->slug])->with('success', 'El domicilio se marcó como principal');
        }
    
        return redirect()->back()->with('error', 'Domicilio no encontrado');
    }
}
