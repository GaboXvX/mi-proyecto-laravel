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
        $categorias=categoriaPersona::all();

        $persona = Persona::where('slug', $slug)->first();
    
        if (!$persona) {
            return redirect()->back()->withErrors(['error' => 'Persona no encontrada']);
        }
    
        return view('personas.agregarDireccion', compact('persona','categorias'));
    }
    
    public function store(Request $request, $id)
    {
        $persona = Persona::find($id);
        $direccion = new Direccion();

        $request->validate([
            'parroquia' => 'required|string|max:255',
            'urbanizacion' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
            'comunidad' => 'required|string|max:255',
            'calle' => 'nullable|string|max:255',
            'manzana' => 'nullable|string|max:255',
            'numero_de_casa' => 'required|string|max:255',
            'categoria' => 'required|integer',
        ]);

        if (empty($request->input('calle')) && empty($request->input('manzana'))) {
            return redirect()->back()->withErrors(['error' => 'Debe llenar al menos uno de los campos: calle o manzana']);
        }

        // Verificamos si la dirección ya está registrada para la persona
        $direccionExistente = Direccion::where('id_persona', $id)
            ->where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->where('id_comunidad', $request->input('comunidad'))
            ->where('calle', $request->input('calle'))
            ->where('manzana', $request->input('manzana'))
            ->where('numero_de_casa', $request->input('numero_de_casa'))
            ->first();

        if ($direccionExistente) {
            return redirect()->back()->withErrors(['error' => 'La dirección ya está registrada para esta persona.'])->withInput();
        }

        // Verificamos si ya existe otro líder en la misma comunidad
        $otroLider = Persona::whereHas('direccion', function ($query) use ($request) {
            $query->where('id_comunidad', $request->input('comunidad'));
        })->where('id_categoriaPersona', 2)->exists();

        if ($request->input('categoria') == 2 && $otroLider) {
            return redirect()->back()->withErrors(['error' => 'Ya existe un líder en esa comunidad.'])->withInput();
        }

        $direccion->id_parroquia = $request->input('parroquia');
        $direccion->id_urbanizacion = $request->input('urbanizacion');
        $direccion->id_sector = $request->input('sector');
        $direccion->id_comunidad = $request->input('comunidad');
        $direccion->calle = $request->input('calle');
        $direccion->manzana = $request->input('manzana');
        $direccion->numero_de_casa = $request->input('numero_de_casa');
        $direccion->id_persona = $id;

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

    public function edit(Request $request, $slug)
    {
        $categorias = categoriaPersona::all();
        $persona = Persona::where('slug', $slug)->first();
        
        if (!$persona) {
            return redirect()->back()->withErrors(['error' => 'Persona no encontrada']);
        }
    
        // Determinamos si la persona es líder comunitario en alguna de sus direcciones
        foreach ($persona->direccion as $direccion) {
            $direccion->esLider = $persona->id_categoriaPersona == 2 && $persona->lider_Comunitario()->where('id_comunidad', $direccion->id_comunidad)->where('estado', 1)->exists();
        }
    
        return view('personas.modificarDirecciones', compact('persona', 'categorias'));
    }

    public function update(Request $request, $id, $idPersona)
    {
        $direccion = Direccion::where('id_direccion', $id)->first();
        $persona = Persona::find($idPersona);
    
        $request->validate([
            'parroquia' => 'required|string|max:255',
            'urbanizacion' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
            'comunidad' => 'required|string|max:255',
            'calle' => 'nullable|string|max:255',
            'manzana' => 'nullable|string|max:255',
            'numero_de_casa' => 'required|string|max:255',
        ]);
    
        if (empty($request->input('calle')) && empty($request->input('manzana'))) {
            return redirect()->back()->withErrors(['error' => 'Debe llenar al menos uno de los campos: calle o manzana']);
        }
    
        // Verificamos si la dirección ya está registrada para la persona
        $direccionExistente = Direccion::where('id_persona', $direccion->id_persona)
            ->where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->where('id_comunidad', $request->input('comunidad'))
            ->where('calle', $request->input('calle'))
            ->where('manzana', $request->input('manzana'))
            ->where('numero_de_casa', $request->input('numero_de_casa'))
            ->where('id_direccion', '!=', $id) // Excluimos la dirección actual
            ->first();
    
        if ($direccionExistente) {
            return redirect()->back()->withErrors(['error' => 'La dirección ya está registrada para esta persona.'])->withInput();
        }
    
        // Verificamos si la persona ya es líder de una comunidad
        $esLider = $persona->id_categoriaPersona == 2 && $persona->lider_Comunitario()->where('estado', 1)->exists();
    
        // Verificamos si ya existe otro líder en la misma comunidad, pero excluimos a la persona que está siendo actualizada
        $otroLider = Persona::whereHas('direccion', function ($query) use ($request) {
            $query->where('id_comunidad', $request->input('comunidad'));
        })->where('id_categoriaPersona', 2)
          ->where('id_persona', '!=', $idPersona)  // Excluimos a la persona actual
          ->exists();
    
        // Priorizar la condición de actualizar la misma dirección en la que es líder
        if ($esLider && $direccion->id_comunidad == $request->input('comunidad')) {
            // Si se está actualizando la misma dirección, no se toma en cuenta la condición de otro líder
            $otroLider = false;
        }else{
    
        if ($esLider && $otroLider) {
            return redirect()->back()->withErrors(['error' => 'Ya existe un líder en esa comunidad y esta persona ya es líder de una comunidad.'])->withInput();
        } elseif ($esLider) {
            return redirect()->back()->withErrors(['error' => 'Esta persona ya es líder de una comunidad.'])->withInput();
        } elseif ($otroLider) {
            return redirect()->back()->withErrors(['error' => 'Ya existe un líder en esa comunidad.'])->withInput();
        }
    }
        // Si no hay líder para esa comunidad y la persona no es líder de ninguna, asignamos el puesto de líder
        if (!$esLider && !$otroLider && $request->input('categoria') == 2) {
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
    
        $direccion->id_parroquia = $request->input('parroquia');
        $direccion->id_urbanizacion = $request->input('urbanizacion');
        $direccion->id_sector = $request->input('sector');
        $direccion->id_comunidad = $request->input('comunidad');
        $direccion->calle = $request->input('calle');
        $direccion->manzana = $request->input('manzana');
        $direccion->numero_de_casa = $request->input('numero_de_casa');
    
        $direccion->save();
    
        return redirect()->route('personas.show', ['slug' => $persona->slug])->with('success', 'Dirección actualizada exitosamente');
    }
    
}