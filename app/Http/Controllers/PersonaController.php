<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\updatePersonaRequest;
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
        return view('personas.registrarPersonas');
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
    
            // Comprobamos si ya existe un líder en la comunidad
            $lider = Persona::whereHas('direccion', function ($query) use ($request) {
                $query->where('id_comunidad', $request->input('comunidad'));
            })
            ->where('es_lider', 1)
            ->first();
    
            // Si la persona intenta ser líder y ya existe otro líder, lanzamos un error
            if ($lider && $request->input('lider_comunitario') == 1) {
                return redirect()->route('personas.index')->with('error', 'Ya existe un líder para esa comunidad');
            }
    
            // Asignamos los datos a la persona
            $persona->slug = $slug;
            $persona->nombre = $request->input('nombre');
            $persona->apellido = $request->input('apellido');
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->es_lider = $request->input('lider_comunitario');
            $persona->save();
    
            // Ahora que la persona está creada, creamos la dirección
            $parroquia = $request->input('parroquia');
            $urbanizacion = $request->input('urbanizacion');
            $sector = $request->input('sector');
            $comunidad = $request->input('comunidad');
            $calle = $request->input('calle');
            $manzana = $request->input('manzana');
            $num_casa = $request->input('num_casa');
    
                $direccion = new Direccion();
                $direccion->id_comunidad = $comunidad;
                $direccion->id_sector = $sector;
                $direccion->calle = $calle;
                $direccion->manzana = $manzana;
                $direccion->numero_de_casa = $num_casa;
                $direccion->id_parroquia = $parroquia;
                $direccion->id_urbanizacion = $urbanizacion;
                $direccion->id_persona = $persona->id_persona;  // Asignamos el id_persona
                $direccion->save();
            
    
            // Asociamos la persona con la dirección
            // Usamos la relación hasMany para guardar la dirección relacionada con la persona
            $persona->direccion()->save($direccion);
    
            // Si la persona es un líder, la registramos en la tabla lideres_comunitarios
            if ($persona->es_lider == 1) {
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
            return view('personas.persona', compact('persona'));
        } else {
            return redirect()->route('personas.index');
        }
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

    public function update(updatePersonaRequest $request, $slug)
{
    try {
        $persona = Persona::where('slug', $slug)->first();

        // Si no se encuentra la persona con el slug proporcionado
        if (!$persona) {
            return redirect()->route('personas.index')->with('error', 'Persona no encontrada con el slug: ' . $slug);
        }

        // Actualizamos los campos de la persona
        $persona->nombre = $request->input('nombre');
        $persona->apellido = $request->input('apellido');
        $persona->cedula = $request->input('cedula');
        $persona->correo = $request->input('correo');
        $persona->telefono = $request->input('telefono');
        $persona->id_usuario = Auth::user()->id_usuario;

        // Verificamos y actualizamos el estado de 'es_lider' si ha cambiado
        $esLiderNuevo = $request->input('lider_comunitario');
        if ($persona->es_lider != $esLiderNuevo) {
            $persona->es_lider = $esLiderNuevo;

            if ($esLiderNuevo == 1) {
                // Verificamos si ya existe otro líder en la misma comunidad
                $otroLider = Persona::whereHas('direccion', function ($query) use ($persona) {
                    $query->where('id_comunidad', $persona->direccion->id_comunidad);
                })->where('es_lider', 1)->first();

                if ($otroLider) {
                    return redirect()->route('personas.index')->with('error', 'Ya existe un líder activo para esta comunidad.');
                }

                // Si no existe, asignamos al nuevo líder
                $persona->lider_Comunitario()->updateOrCreate(
                    ['id_persona' => $persona->id_persona],
                    ['estado' => true, 'id_comunidad' => $persona->direccion->id_comunidad]
                );
            } else {
                // Si ya no es líder, lo desactivamos
                $persona->lider_Comunitario()->update(['estado' => false]);
            }
        }

        // Guardamos los cambios en la persona
        $persona->save();

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
