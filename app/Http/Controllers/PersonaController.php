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

            // Verificamos si la dirección ya está registrada para la persona
            $direccionExistente = Direccion::where('id_persona', $persona->id_persona)
                ->where('id_estado', $request->input('estado')) // Verificamos el estado
                ->where('id_municipio', $request->input('municipio')) // Verificamos el municipio
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
            $persona->genero = $request->input('genero'); // Procesar género
            $persona->altura = $request->input('altura'); // Procesar altura
            $persona->fecha_nacimiento = $request->input('fecha_nacimiento'); // Procesar fecha de nacimiento
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->id_categoriaPersona = $request->input('categoria');
            $persona->save();

            // Asignamos los valores de la dirección
            $estado = $request->input('estado');
            $municipio = $request->input('municipio');
            $parroquia = $request->input('parroquia');
            $urbanizacion = $request->input('urbanizacion');
            $sector = $request->input('sector');
            $comunidad = $request->input('comunidad');
            $calle = $request->input('calle');
            $manzana = $request->input('manzana');
            $num_vivienda = $request->input('num_vivienda');
            $bloque = $request->input('bloque');
            $es_principal = $request->input('es_principal', 0); // Capturamos si es principal o secundaria

            // Asignamos los datos a la dirección
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
            $direccion->id_estado = $estado; // Asignamos el estado
            $direccion->id_municipio = $municipio; // Asignamos el municipio
            $direccion->save();

            // Asociamos la persona con la dirección
            $persona->direccion()->save($direccion);

            // Verificamos si ya existe otro líder en la misma comunidad
            if ($persona->id_categoriaPersona == 2) { // Suponiendo que la categoría 2 es la de líder comunitario
                $otroLider = Lider_Comunitario::where('id_comunidad', $comunidad)
                    ->where('estado', 1)
                    ->first();

                if ($otroLider) {
                    return back()->withErrors(['error' => 'Ya existe un líder activo para esta comunidad.'])->withInput();
                }

                // Si la persona es un líder, la registramos en la tabla lideres_comunitarios
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
        $categorias = categoriaPersona::all();
        $persona = Persona::where('slug', $slug)->firstOrFail();

        if ($persona) {
            // Paginate the addresses
            $direcciones = $persona->direccion()->paginate(5);

            // Determine if the person is a leader in any of the paginated addresses
            foreach ($direcciones as $direccion) {
                $direccion->esLider = $persona->id_categoriaPersona == 2 && $persona->lider_Comunitario()->where('id_comunidad', $direccion->id_comunidad)->where('estado', 1)->exists();
            }

            return view('personas.persona', compact('persona', 'categorias', 'direcciones'));
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

    public function update(UpdatePersonaRequest $request, $slug)
    {
        try {
            // Buscar la persona usando el slug
            $persona = Persona::where('slug', $slug)->first();
    
            // Si no se encuentra la persona
            if (!$persona) {
                return redirect()->route('personas.index')->with('error', 'Persona no encontrada con el slug: ' . $slug);
            }
    
            // Actualizamos los campos de la persona
            $persona->nombre = $request->input('nombre');
            $persona->apellido = $request->input('apellido');
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            
            // Nuevos campos: Género y Fecha de nacimiento
            $persona->genero = $request->input('genero');  // Asumimos que "genero" es un campo del formulario
            $persona->fecha_nacimiento = $request->input('fecha_nacimiento');  // Asumimos que "fecha_nacimiento" es un campo del formulario
            
            // Guardamos los cambios
            $persona->save();
    
            return redirect()->route('personas.index')->with('success', 'Persona actualizada con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('personas.index')->with('error', 'Error al actualizar la persona: ' . $e->getMessage());
        }
    }

    public function buscar(Request $request)
    {
        $query = $request->input('query');
        $personas = Persona::where('cedula', 'LIKE', "%{$query}%")->get();

        return response()->json($personas);
    }
}
