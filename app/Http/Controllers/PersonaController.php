<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Models\Direccion;
use App\Models\Domicilio;
use App\Models\incidencia;
use App\Models\lider_comunitario;
use App\Models\LiderComunitario;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class PersonaController extends Controller
{
    public function create()
    {
        $lideres=Lider_Comunitario::all();
        $direcciones = Direccion::all();
        return view('personas.registrarPersonas', compact('direcciones','lideres'));
    }

    public function store(StorePersonaRequest $request)
{
    try {
        $estado = 'sucre';
        $municipio = 'sucre';
        $comunidad = $request->input('comunidad');
        $sector = $request->input('sector');
        $calle = $request->input('calle');
        $manzana = $request->input('manzana');
        $num_casa = $request->input('num_casa');

        $domicilio = new Domicilio(); 
        $domicilio->calle = $calle;
        $domicilio->manzana = $manzana;
        $domicilio->numero_de_casa = $num_casa;
        $domicilio->save();

        $direccion = Direccion::where('estado', $estado)
            ->where('municipio', $municipio)
            ->where('comunidad', $comunidad)
            ->where('sector', $sector)
            ->firstOrFail();

        $persona = new Persona();
        $slug = Str::slug($request->input('nombre'));
        $originalSlug = $slug;
        $counter = 1;
        
        while (Persona::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $usuario=Auth::user()->id_usuario;
        $persona->slug = $slug;
        $persona->nombre = $request->input('nombre');
        $persona->apellido = $request->input('apellido');
        $persona->cedula = $request->input('cedula');
        $persona->correo = $request->input('correo');
        $persona->telefono = $request->input('telefono');
        $persona->id_direccion = $direccion->id_direccion;  
        $persona->id_usuario = $usuario;
        $persona->id_lider = $request->input('lider_comunitario');
        $persona->id_domicilio = $domicilio->id_domicilio;
        $persona->save();

        
        return redirect()->route('personas.index')->with('success', 'Datos enviados correctamente');
        
    } catch (\Exception $e) {
        
        
        return redirect()->route('personas.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
    }
}


    public function index()
    {
        $personas = Persona::orderBy('id_persona', 'desc')->get();
        return view('personas.listaPersonas', compact('personas'));
    }
    public function show($slug)
    {
        
        $persona = Persona::where('slug', $slug)->firstOrFail();
    
        return view('personas.persona', compact('persona'));
    }



    public function destroy($slug)
    {
        try {
            $persona = Persona::where('slug', $slug)->firstOrFail();
            $persona->delete();

            return redirect()->route('personas.index')->with('success', 'Registro eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('personas.index')->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    public function edit($slug)
    {
        $lideres=lider_comunitario::all();
        $direcciones= Direccion::all();
        $persona = Persona::where('slug', $slug)->firstOrFail();
        return view('personas.modificarPersonas', compact('persona','direcciones','lideres'));
    }


    public function update(Request $request, $slug)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|integer|unique:personas,cedula,' . $slug . ',slug',
            'lider_comunitario' => 'nullable|string|max:255',
            'correo' => 'required|email|max:255|unique:personas,correo,' . $slug . ',slug',
            'telefono' => 'required|digits_between:10,15',
        ];
    
        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.integer' => 'La cédula debe ser un número entero.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser una dirección válida.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.digits_between' => 'El número de teléfono debe tener entre 10 y 15 dígitos.',
        ];
    
        $request->validate($rules, $messages);
    
        try {
            $estado = 'sucre';
            $municipio = 'sucre';
            $comunidad = $request->input('comunidad');
            $sector = $request->input('sector');
            $calle = $request->input('calle');
            $manzana = $request->input('manzana');
            $num_casa = $request->input('num_casa');
    
            $originalSlug = $slug;
    
            $domicilio = Domicilio::updateOrCreate(
                ['id_domicilio' => $request->input('id_domicilio')],
                [
                    'calle' => $calle,
                    'manzana' => $manzana,
                    'numero_de_casa' => $num_casa
                ]
            );
    
            $direccion = Direccion::where('estado', $estado)
                ->where('municipio', $municipio)
                ->where('comunidad', $comunidad)
                ->where('sector', $sector)
                ->firstOrFail();
    
            $persona = Persona::where('slug', $originalSlug)->first();
            
            if (!$persona) {
                return redirect()->route('personas.index')->with('error', 'Persona no encontrada con el slug: ' . $originalSlug);
            }
            $persona->id_usuario=Auth::user()->id_usuario;
            $persona->nombre = $request->input('nombre');
            $persona->apellido = $request->input('apellido');
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->id_direccion = $direccion->id_direccion; 
            $persona->id_lider = $request->input('lider_comunitario');
            $persona->id_domicilio = $domicilio->id_domicilio;
            $persona->save();
    
            return redirect()->route('personas.index')->with('success', 'Datos actualizados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('personas.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
    }
    

    
    public function buscar(Request $request)
{
    $cedula = $request->input('buscar');
    $persona = Persona::where('cedula', $cedula)->first();
    if (!$persona) {
        return view('personas.busqueda')->with('persona', null);
    }
    return view('personas.busqueda')->with('persona', $persona);
}
}
