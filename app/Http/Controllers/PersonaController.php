<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\updatePersonaRequest;
use App\Models\categoriaPersona;
use App\Models\Comunidad;
use App\Models\Direccion;
use App\Models\Lider_Comunitario;
use App\Models\movimiento;
use App\Models\Parroquia;
use App\Models\Persona;
use App\Models\Sector;
use App\Models\Urbanizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PersonaController extends Controller
{
    public function create()
    {
        $categorias=categoriaPersona::all();
        return view('personas.registrarPersonas',compact('categorias'));
    }

    public function store(StorePersonaRequest $request)
    {
        try {
            // Primero creamos la persona
            $persona = new Persona();
            $slug = Str::slug($request->input('nombre'));
            $count = Persona::where('slug', $slug)->count();
    
            // Si el slug ya existe, lo modificamos
            if ($count > 0) {
                $originalSlug = $slug;
                $counter = 1;
    
                while (Persona::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
    
            // Verificamos si ya existe otro líder en la misma comunidad
            if ($request->input('categoria') == 2) { // Suponiendo que la categoría 2 es la de líder comunitario
                $otroLider = Persona::whereHas('direccion', function ($query) use ($request) {
                    $query->where('id_comunidad', $request->input('comunidad'));
                })->where('id_categoriaPersona', 2)->first();
    
                if ($otroLider) {
                    return back()->withErrors(['error' => 'Ya existe un líder activo para esta comunidad.'])->withInput();
                }
            }
    
            // Verificamos si la dirección ya está registrada para la persona
            $direccionExistente = Direccion::where('id_persona', $persona->id_persona)
                ->where('id_parroquia', $request->input('parroquia'))
                ->where('id_urbanizacion', $request->input('urbanizacion'))
                ->where('id_sector', $request->input('sector'))
                ->where('id_comunidad', $request->input('comunidad'))
                ->where('calle', $request->input('calle'))
                ->where('manzana', $request->input('manzana'))
                ->where('numero_de_vivienda', $request->input('num_vivienda'))
                ->first();
    
            if ($direccionExistente) {
                return back()->withErrors(['error' => 'La dirección ya está registrada para esta persona.'])->withInput();
            }
    
            // Asignamos los datos a la persona
            $persona->slug = $slug;
            $persona->nombre = $request->input('nombre');
            $persona->apellido = $request->input('apellido');
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->id_categoriaPersona = $request->input('categoria'); // Asignamos la categoría seleccionada
            $persona->save();
    
            // Ahora que la persona está creada, creamos la dirección
            $parroquia = $request->input('parroquia');
            $urbanizacion = $request->input('urbanizacion');
            $sector = $request->input('sector');
            $comunidad = $request->input('comunidad');
            $calle = $request->input('calle');
            $manzana = $request->input('manzana');
            $num_vivienda = $request->input('num_vivienda');
            $bloque = $request->input('bloque');
            $es_principal = $request->input('es_principal', 0); // Capturamos si es principal o secundaria
    
                $direccion = new Direccion();
                $direccion->id_comunidad = $comunidad;
                $direccion->id_sector = $sector;
                $direccion->calle = $calle;
                $direccion->manzana = $manzana;
                $direccion->numero_de_vivienda = $num_vivienda;
                $direccion->bloque = $bloque;
                $direccion->id_parroquia = $parroquia;
                $direccion->id_urbanizacion = $urbanizacion;
                $direccion->id_persona = $persona->id_persona;  // Asignamos el id_persona
                $direccion->es_principal = $es_principal;
                $direccion->save();
            
    
            // Asociamos la persona con la dirección
            // Usamos la relación hasMany para guardar la dirección relacionada con la persona
            $persona->direccion()->save($direccion);
    
            // Si la persona es un líder, la registramos en la tabla lideres_comunitarios
            if ($persona->id_categoriaPersona == 2) { // Suponiendo que la categoría 2 es la de líder comunitario
                $liderComunitario = new Lider_Comunitario();
                $liderComunitario->id_persona = $persona->id_persona;
                $liderComunitario->id_comunidad = $comunidad;
                $liderComunitario->estado = 1;  // El líder está activo
                $liderComunitario->save();
            }
    
            return redirect()->route('personas.index')->with('success', 'Datos enviados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('personas.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
        }
    }
    
    

    public function index()
    {
        $personas = Persona::orderBy('id_persona', 'desc')->paginate(10);

        return view('personas.listaPersonas', compact('personas'));
    }

    public function show($slug)
    {
        $persona = Persona::where('slug', $slug)->firstOrFail();
        if ($persona) {
            // Determinamos si la persona es líder comunitario en alguna de sus direcciones
            foreach ($persona->direccion as $direccion) {
                $direccion->esLider = $persona->id_categoriaPersona == 2 && $persona->lider_Comunitario()->where('id_comunidad', $direccion->id_comunidad)->where('estado', 1)->exists();
            }
            return view('personas.persona', compact('persona'));
        } else {
            return redirect()->route('personas.index');
        }
    }

   
    public function edit($slug)
    {
        $categorias=categoriaPersona::all();

        $persona = Persona::where('slug', $slug)->first();
        if ($persona) {
            return view('personas.modificarPersonas', compact('persona','categorias'));
        } else {
            return redirect()->route('personas.index');
        }
    }

    public function update(updatePersonaRequest $request, $slug)
{
    try {
        $persona = Persona::where('slug', $slug)->first();

        // Si no se encuentra la persona con el slug proporcionado
        if (!$persona) {
            return redirect()->route('personas.index')->with('error', 'Persona no encontrada con el slug: ' . $slug);
        }

        // Verificamos si ya existe otro líder en la misma comunidad
        if ($request->input('categoria') == 2 && $persona->id_categoriaPersona != 2) { // Suponiendo que la categoría 2 es la de líder comunitario
            $otroLider = Persona::whereHas('direccion', function ($query) use ($persona) {
                $query->where('id_comunidad', $persona->direccion->first()->id_comunidad);
            })->where('id_categoriaPersona', 2)->first();

            if ($otroLider) {
                return back()->withErrors(['error' => 'Ya existe un líder activo para esta comunidad.'])->withInput();
            }
        }

        // Verificamos si la dirección ya está registrada para la persona
        $direccionExistente = Direccion::where('id_persona', $persona->id_persona)
            ->where('id_parroquia', $request->input('parroquia'))
            ->where('id_urbanizacion', $request->input('urbanizacion'))
            ->where('id_sector', $request->input('sector'))
            ->where('id_comunidad', $request->input('comunidad'))
            ->where('calle', $request->input('calle'))
            ->where('manzana', $request->input('manzana'))
            ->where('numero_de_vivienda', $request->input('num_casa'))
            ->where('id_direccion', '!=', $persona->direccion->first()->id_direccion) // Excluimos la dirección actual
            ->first();

        if ($direccionExistente) {
            return back()->withErrors(['error' => 'La dirección ya está registrada para esta persona.'])->withInput();
        }

        // Actualizamos los campos de la persona
        $persona->nombre = $request->input('nombre');
        $persona->apellido = $request->input('apellido');
        $persona->cedula = $request->input('cedula');
        $persona->correo = $request->input('correo');
        $persona->telefono = $request->input('telefono');
        $persona->id_usuario = Auth::user()->id_usuario;
        $persona->id_categoriaPersona = $request->input('categoria'); // Actualizamos la categoría seleccionada

        // Verificamos y actualizamos el estado de 'es_lider' si ha cambiado
        $esLiderNuevo = $request->input('categoria') == 2; // Suponiendo que la categoría 2 es la de líder comunitario
        if ($persona->id_categoriaPersona != $request->input('categoria')) {
            if ($esLiderNuevo) {
                // Si no existe, asignamos al nuevo líder
                $persona->lider_Comunitario()->updateOrCreate(
                    ['id_persona' => $persona->id_persona],
                    ['estado' => true, 'id_comunidad' => $persona->direccion->first()->id_comunidad]
                );
            } else {
                // Si ya no es líder, lo desactivamos
                $persona->lider_Comunitario()->update(['estado' => false]);
            }
        } else if ($persona->id_categoriaPersona == 2 && $request->input('categoria') != 2) {
            // Si la persona ya no es líder, desactivamos su estado en la tabla lideres_comunitarios
            $persona->lider_Comunitario()->update(['estado' => false]);
        }

        // Guardamos los cambios en la persona
        $persona->save();

        // Si se asigna un nuevo líder a la misma comunidad, creamos un nuevo registro en la tabla lideres_comunitarios
        if ($esLiderNuevo) {
            $nuevoLider = new Lider_Comunitario();
            $nuevoLider->id_persona = $persona->id_persona;
            $nuevoLider->id_comunidad = $persona->direccion->first()->id_comunidad;
            $nuevoLider->estado = 1; // El líder está activo
            $nuevoLider->save();
        }

        // Si la categoría de la persona es regular (1), desactivamos su estado en la tabla lideres_comunitarios
        if ($request->input('categoria') == 1) {
            $persona->lider_Comunitario()->update(['estado' => false]);
        }

        return redirect()->route('personas.index')->with('success', 'Datos de la persona actualizados correctamente');
    } catch (\Exception $e) {
        // Si ocurre un error, mostramos el mensaje de error
        return redirect()->route('personas.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
    }
}


    
    
    

    public function buscar(Request $request)
    {
        $cedula = $request->input('buscar');
        $persona = Persona::where('cedula', $cedula)->first();

        if (!$persona) {
            return redirect()->route('personas.index')->with('error', 'Persona no encontrada');
        }

        return view('personas.listapersonas')->with('personas', [$persona]);
    }
}
