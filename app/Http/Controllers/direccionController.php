<?php

namespace App\Http\Controllers;

use App\Models\categoriaPersona;
use App\Models\Direccion;
use App\Models\Lider_Comunitario;
use App\Models\movimiento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;

class direccionController extends Controller
{
    public function index(Request $request, $slug)
    {
        $categorias = categoriaPersona::all();

        $persona = Persona::where('slug', $slug)->first();

        if (!$persona) {
            return redirect()->back()->withErrors(['error' => 'Persona no encontrada']);
        }

        return view('personas.agregarDireccion', compact('persona', 'categorias'));
    }

    public function store(Request $request, $id)
    {
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
            'es_principal' => 'required|boolean',
            'categoria' => 'required|integer|exists:categorias_personas,id_categoriaPersona',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $persona = Persona::findOrFail($id);

            // Verificar si ya existe una dirección principal
            if ($request->input('es_principal') && Direccion::where('id_persona', $id)->where('es_principal', 1)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ya existe una dirección marcada como principal para esta persona.'
                ], 422);
            }

            // Verificar si la dirección ya está registrada
            $direccionExistente = Direccion::where('id_persona', $id)
                ->where('id_estado', $request->input('estado'))
                ->where('id_municipio', $request->input('municipio'))
                ->where('id_parroquia', $request->input('parroquia'))
                ->where('id_urbanizacion', $request->input('urbanizacion'))
                ->where('id_sector', $request->input('sector'))
                ->where('id_comunidad', $request->input('comunidad'))
                ->where('calle', $request->input('calle'))
                ->where('manzana', $request->input('manzana'))
                ->where('bloque', $request->input('bloque'))
                ->where('numero_de_vivienda', $request->input('numero_de_vivienda'))
                ->first();

            if ($direccionExistente) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La dirección ya está registrada para esta persona.'
                ], 422);
            }

            // Manejar el estado de líder comunitario
            $categoria = $request->input('categoria');
            $idComunidad = $request->input('comunidad');

            if ($categoria == 2) {
                // Verificar si ya es líder en OTRA comunidad diferente
                $liderEnOtraComunidad = Lider_Comunitario::where('id_persona', $persona->id_persona)
                    ->where('estado', 1)
                    ->where('id_comunidad', '!=', $idComunidad)
                    ->exists();

                if ($liderEnOtraComunidad) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Esta persona ya es líder en otra comunidad. No puede ser líder en más de una comunidad.'
                    ], 422);
                }

                // Verificar si ya existe un registro de líder comunitario para ESTA comunidad
                $liderActual = Lider_Comunitario::where('id_persona', $persona->id_persona)
                    ->where('id_comunidad', $idComunidad)
                    ->first();

                if (!$liderActual) {
                    // Registrar como líder comunitario si no existe
                    $persona->id_categoriaPersona = 2;
                    $persona->save();

                    $liderComunitario = new Lider_Comunitario();
                    $liderComunitario->id_persona = $persona->id_persona;
                    $liderComunitario->id_comunidad = $idComunidad;
                    $liderComunitario->estado = 1;
                    $liderComunitario->save();
                } elseif ($liderActual->estado == 0) {
                    // Reactivar el estado de líder si estaba desactivado
                    $liderActual->estado = 1;
                    $liderActual->save();
                }
            } elseif ($categoria == 1) {
                // Solo actualizamos la categoría de la persona si no es líder en ninguna comunidad
                if (!$persona->lider_Comunitario()->where('estado', 1)->exists()) {
                    $persona->id_categoriaPersona = 1;
                    $persona->save();
                }

                // No desactivamos el estado de líder para la comunidad
                // ya que otras direcciones podrían seguir siendo líderes
            }

            // Crear la dirección
            $direccion = new Direccion();
            $direccion->id_persona = $id;
            $direccion->id_estado = $request->input('estado');
            $direccion->id_municipio = $request->input('municipio');
            $direccion->id_parroquia = $request->input('parroquia');
            $direccion->id_urbanizacion = $request->input('urbanizacion');
            $direccion->id_sector = $request->input('sector');
            $direccion->id_comunidad = $request->input('comunidad');
            $direccion->calle = $request->input('calle');
            $direccion->manzana = $request->input('manzana');
            $direccion->numero_de_vivienda = $request->input('numero_de_vivienda');
            $direccion->bloque = $request->input('bloque');
            $direccion->es_principal = $request->input('es_principal');
            $direccion->save();

            // Registrar movimiento
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->id_persona = $persona->id_persona;
            $movimiento->descripcion = 'se añadió una nueva dirección';
            $movimiento->id_direccion = $direccion->id_direccion;
            $movimiento->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Dirección guardada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'exception',
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function update(Request $request, $id, $idPersona)
    {
        $direccion = Direccion::where('id_direccion', $id)->first();
        $persona = Persona::find($idPersona);

        $validator = FacadesValidator::make($request->all(), [
            'estado' => 'required|exists:estados,id_estado',
            'municipio' => 'required|exists:municipios,id_municipio',
            'parroquia' => 'required|string|max:255',
            'urbanizacion' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
            'comunidad' => 'required|string|max:255',
            'calle' => 'nullable|string|max:255',
            'manzana' => 'nullable|string|max:255',
            'bloque' => 'nullable|string|max:255',
            'numero_de_vivienda' => 'required|string|max:255',
            'categoria' => 'required|integer|exists:categorias_personas,id_categoriaPersona',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar si la dirección ya está registrada
            $direccionExistente = Direccion::where('id_persona', $direccion->id_persona)
                ->where('id_estado', $request->input('estado'))
                ->where('id_municipio', $request->input('municipio'))
                ->where('id_parroquia', $request->input('parroquia'))
                ->where('id_urbanizacion', $request->input('urbanizacion'))
                ->where('id_sector', $request->input('sector'))
                ->where('id_comunidad', $request->input('comunidad'))
                ->where('calle', $request->input('calle'))
                ->where('manzana', $request->input('manzana'))
                ->where('bloque', $request->input('bloque'))
                ->where('numero_de_vivienda', $request->input('numero_de_vivienda'))
                ->where('id_direccion', '!=', $id)
                ->first();

            if ($direccionExistente) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La dirección ya está registrada para esta persona.'
                ], 422);
            }

            // Manejar el cambio de estado de líder comunitario
            $categoria = $request->input('categoria');
            if ($categoria == 2) {
                // Verificar si la persona ya es líder en otra comunidad
                $liderExistente = Lider_Comunitario::where('id_persona', $idPersona)
                    ->where('estado', 1)
                    ->where('id_comunidad', '!=', $request->input('comunidad'))
                    ->exists();

                if ($liderExistente) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Esta persona ya es líder en otra comunidad.'
                    ], 422);
                }

                // Registrar como líder comunitario si no lo es
                $liderActual = $persona->lider_Comunitario()->where('id_comunidad', $request->input('comunidad'))->first();
                if (!$liderActual) {
                    $persona->id_categoriaPersona = 2;
                    $persona->save();

                    $liderComunitario = new Lider_Comunitario();
                    $liderComunitario->id_persona = $persona->id_persona;
                    $liderComunitario->id_comunidad = $request->input('comunidad');
                    $liderComunitario->estado = 1;
                    $liderComunitario->save();
                } elseif ($liderActual->estado == 0) {
                    // Reactivar el estado de líder si estaba desactivado
                    $liderActual->estado = 1;
                    $liderActual->save();
                }
            } elseif ($categoria == 1) {
                // Si la categoría cambia a regular, desactivar el estado de líder
                $persona->id_categoriaPersona = 1;
                $persona->lider_Comunitario()->where('id_comunidad', $direccion->id_comunidad)->update(['estado' => 0]);
                $persona->save();
            }

            // Actualizar la dirección
            $direccion->id_estado = $request->input('estado');
            $direccion->id_municipio = $request->input('municipio');
            $direccion->id_parroquia = $request->input('parroquia');
            $direccion->id_urbanizacion = $request->input('urbanizacion');
            $direccion->id_sector = $request->input('sector');
            $direccion->id_comunidad = $request->input('comunidad');
            $direccion->calle = $request->input('calle');
            $direccion->manzana = $request->input('manzana');
            $direccion->bloque = $request->input('bloque');
            $direccion->numero_de_vivienda = $request->input('numero_de_vivienda');
            $direccion->save();

            // Registrar movimiento
            $movimiento = new movimiento();
            $movimiento->id_usuario = auth()->user()->id_usuario;
            $movimiento->id_persona = $persona->id_persona;
            $movimiento->descripcion = 'se modificó una dirección';
            $movimiento->id_direccion = $direccion->id_direccion;
            $movimiento->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Dirección actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'exception',
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
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
