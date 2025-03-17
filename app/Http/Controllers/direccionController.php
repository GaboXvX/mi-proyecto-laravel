<?php

namespace App\Http\Controllers;

use App\Models\categoriaPersona;
use App\Models\Direccion;
use App\Models\Lider_Comunitario;
use App\Models\Persona;
use Illuminate\Http\Request;

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
        $persona = Persona::find($id);
        $direccion = new Direccion();

        // Validación de la solicitud
        $request->validate([
            'estado' => 'required|exists:estados,id_estado',  // Verifica que el id_estado sea válido
            'municipio' => 'required|exists:municipios,id_estado',  // Verifica que el id_municipio sea válido
            'parroquia' => 'required|string|max:255',
            'urbanizacion' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
            'comunidad' => 'required|string|max:255',
            'calle' => 'nullable|string|max:255',
            'manzana' => 'nullable|string|max:255',
            'numero_de_vivienda' => 'required|string|max:255',
            'bloque' => 'nullable|string|max:255',
            'es_principal' => 'required|boolean',
        ]);

        // Verificamos si al menos uno de los campos 'calle' o 'manzana' está lleno
        if (empty($request->input('calle')) && empty($request->input('manzana'))) {
            return redirect()->back()->withErrors(['error' => 'Debe llenar al menos uno de los campos: calle o manzana']);
        }

        // Verificamos si ya existe una dirección principal para la persona
        if ($request->input('es_principal') && Direccion::where('id_persona', $id)->where('es_principal', 1)->exists()) {
            return redirect()->back()->withErrors(['error' => 'Ya existe una dirección marcada como principal para esta persona.'])->withInput();
        }

        // Verificamos si la dirección ya está registrada para la persona con los nuevos campos de estado y municipio
        $direccionExistente = Direccion::where('id_persona', $id)
            ->where('id_estado', $request->input('estado'))
            ->where('id_municipio', $request->input('municipio'))
            ->where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->where('id_comunidad', $request->input('comunidad'))
            ->where('calle', $request->input('calle'))
            ->where('manzana', $request->input('manzana'))
            ->where('numero_de_vivienda', $request->input('numero_de_vivienda'))
            ->first();

        if ($direccionExistente) {
            return redirect()->back()->withErrors(['error' => 'La dirección ya está registrada para esta persona.'])->withInput();
        }

        // Verificamos si ya existe otro líder en la misma comunidad
        $otroLider = Persona::whereHas('direccion', function ($query) use ($request) {
            $query->where('id_comunidad', $request->input('comunidad'))
                ->where('id_estado', $request->input('id_estado'))
                ->where('id_municipio', $request->input('id_municipio'));
        })->where('id_categoriaPersona', 2)->exists();

        // Verificamos si la persona ya es líder de otra comunidad
        $esLiderOtraComunidad = $persona->id_categoriaPersona == 2 && $persona->lider_Comunitario()->where('estado', 1)->exists();

        if ($request->input('categoria') == 2 && ($otroLider || $esLiderOtraComunidad)) {
            return redirect()->back()->withErrors(['error' => 'Ya existe un líder en esa comunidad o la persona ya es líder de otra comunidad.'])->withInput();
        }

        // Si la dirección es principal, desmarcamos cualquier otra dirección principal de la persona
        if ($request->input('es_principal')) {
            Direccion::where('id_persona', $id)->update(['es_principal' => 0]);
        }

        // Asignamos los valores a la dirección
        $direccion->id_comunidad = $request->input('comunidad');
        $direccion->id_sector = $request->input('sector');
        $direccion->calle = $request->input('calle');
        $direccion->manzana = $request->input('manzana');
        $direccion->numero_de_vivienda = $request->input('numero_de_vivienda');
        $direccion->bloque = $request->input('bloque');
        $direccion->id_parroquia = $request->input('parroquia');
        $direccion->id_urbanizacion = $request->input('urbanizacion');
        $direccion->id_persona = $id;  // Asignamos el id_persona
        $direccion->es_principal = $request->input('es_principal');
        $direccion->id_estado = $request->input('estado'); // Asignamos el estado
        $direccion->id_municipio = $request->input('municipio'); // Asignamos el municipio

        // Guardamos la nueva dirección
        $direccion->save();

        // Si la persona es líder, actualizamos su estado
        if ($request->input('categoria') == 2) {
            $persona->id_categoriaPersona = 2;
            $persona->save();

            $liderComunitario = new Lider_Comunitario();
            $liderComunitario->id_persona = $persona->id_persona;
            $liderComunitario->id_comunidad = $request->input('comunidad');
            $liderComunitario->estado = 1;  // El líder está activo
            $liderComunitario->save();
        }

        return redirect()->route('personas.show', ['slug' => $persona->slug])->with('success', 'Dirección registrada exitosamente');
    }




    public function update(Request $request, $id, $idPersona)
    {
        $direccion = Direccion::where('id_direccion', $id)->first();
        $persona = Persona::find($idPersona);

        // Validación de la solicitud
        $request->validate([
            'estado' => 'required|exists:estados,id_estado',  // Verifica que el id_estado sea válido
            'municipio' => 'required|exists:municipios,id_estado',  // Verifica que el id_municipio sea válido
            'parroquia' => 'required|string|max:255',
            'urbanizacion' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
            'comunidad' => 'required|string|max:255',
            'calle' => 'nullable|string|max:255',
            'manzana' => 'nullable|string|max:255',
            'bloque' => 'nullable|string|max:255',  // Nuevo campo
            'numero_de_vivienda' => 'required|string|max:255',  // Reemplaza a numero_de_casa
        ]);

        // Verificamos si al menos uno de los campos 'calle' o 'manzana' está lleno
        if (empty($request->input('calle')) && empty($request->input('manzana'))) {
            return redirect()->back()->withErrors(['error' => 'Debe llenar al menos uno de los campos: calle o manzana']);
        }

        // Verificamos si la dirección ya está registrada para la persona
        $direccionExistente = Direccion::where('id_persona', $direccion->id_persona)
            ->where('id_estado', $request->input('estado'))  // Comprobamos que el estado sea el mismo
            ->where('id_municipio', $request->input('municipio'))  // Comprobamos que el municipio sea el mismo
            ->where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->where('id_comunidad', $request->input('comunidad'))
            ->where('calle', $request->input('calle'))
            ->where('manzana', $request->input('manzana'))
            ->where('bloque', $request->input('bloque'))  // Verificamos el nuevo campo
            ->where('numero_de_vivienda', $request->input('numero_de_vivienda'))  // Reemplazamos numero_de_casa
            ->where('id_direccion', '!=', $id) // Excluimos la dirección actual
            ->first();

        if ($direccionExistente) {
            return redirect()->back()->withErrors(['error' => 'La dirección ya está registrada para esta persona.'])->withInput();
        }
        $bloque = $request->input('bloque');
        if (empty($bloque)) {
            $bloque = null;
        }
        // Verificamos si la persona ya es líder de una comunidad
        $esLider = $persona->id_categoriaPersona == 2 && $persona->lider_Comunitario()->where('estado', 1)->exists();

        // Verificamos si ya existe otro líder en la misma comunidad, pero excluimos a la persona que está siendo actualizada
        $otroLider = Persona::whereHas('direccion', function ($query) use ($request) {
            $query->where('id_comunidad', $request->input('comunidad'));
        })->where('id_categoriaPersona', 2)
            ->where('id_persona', '!=', $idPersona)  // Excluimos a la persona actual
            ->exists();

        // Verificamos si la persona ya es líder de otra comunidad
        $esLiderOtraComunidad = $persona->id_categoriaPersona == 2 && $persona->lider_Comunitario()->where('estado', 1)->where('id_comunidad', '!=', $request->input('comunidad'))->exists();

        // Priorizar la condición de actualizar la misma dirección en la que es líder
        if ($esLider && $direccion->id_comunidad == $request->input('comunidad')) {
            // Si se está actualizando la misma dirección, no se toma en cuenta la condición de otro líder
            $otroLider = false;
        } else {
            if ($esLider && ($otroLider || $esLiderOtraComunidad)) {
                return redirect()->back()->withErrors(['error' => 'Ya existe un líder en esa comunidad o la persona ya es líder de otra comunidad.'])->withInput();
            } elseif ($esLider) {
                return redirect()->back()->withErrors(['error' => 'Esta persona ya es líder de una comunidad.'])->withInput();
            } elseif ($otroLider) {
                return redirect()->back()->withErrors(['error' => 'Ya existe un líder en esa comunidad.'])->withInput();
            }
        }

        // Si no hay líder para esa comunidad y la persona no es líder de ninguna, asignamos el puesto de líder
        if (!$esLider && !$otroLider && !$esLiderOtraComunidad && $request->input('categoria') == 2) {
            $persona->id_categoriaPersona = 2;
            $persona->save();

            // Verificamos si la persona ya fue líder en esa comunidad y su estado está en 0
            $liderExistente = $persona->lider_Comunitario()->where('id_comunidad', $request->input('comunidad'))->first();
            if ($liderExistente) {
                // Reactivamos el estado del líder
                $liderExistente->estado = 1;
                $liderExistente->save();
            } else {
                // Creamos un nuevo registro de líder
                $liderComunitario = new Lider_Comunitario();
                $liderComunitario->id_persona = $persona->id_persona;
                $liderComunitario->id_comunidad = $request->input('comunidad');
                $liderComunitario->estado = 1;  // El líder está activo
                $liderComunitario->save();
            }
        }

        // Si la categoría de la persona cambia a regular, desactivamos su estado en la tabla de líderes
        if ($request->input('categoria') == 1) {
            $persona->id_categoriaPersona = 1;
            // Cambiamos el estado del líder en la tabla 'lideres_comunitarios' a 0
            $persona->lider_Comunitario()->where('id_comunidad', $direccion->id_comunidad)->update(['estado' => 0]);
            $persona->save();
        }

        // Actualizamos la dirección con los nuevos datos
        $direccion->id_parroquia = $request->input('parroquia');
        $direccion->id_urbanizacion = $request->input('urbanizacion');
        $direccion->id_sector = $request->input('sector');
        $direccion->id_comunidad = $request->input('comunidad');
        $direccion->calle = $request->input('calle');
        $direccion->manzana = $request->input('manzana');
        $direccion->bloque = $bloque;
        $direccion->numero_de_vivienda = $request->input('numero_de_vivienda');  // Reemplazamos el número de casa
        $direccion->id_estado = $request->input('estado'); // Asignamos el estado
        $direccion->id_municipio = $request->input('municipio'); // Asignamos el municipio

        $direccion->save();

        return redirect()->route('personas.show', ['slug' => $persona->slug])->with('success', 'Dirección actualizada exitosamente');
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
