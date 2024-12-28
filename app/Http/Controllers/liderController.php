<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Models\direccion;
use App\Models\Domicilio;
use App\Models\lider_comunitario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class liderController extends Controller
{
    public function index(){
        $lideres=lider_comunitario::all();
        return view('lideres.listalideres',compact('lideres'));
    }
    public function create()
    {
        $direcciones = Direccion::all();
        return view('lideres.registrarlideres',compact('direcciones'));
    }

    public function store( Request $request)
{
    $rules = [
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'cedula' => 'required|numeric|digits:8|unique:lider_comunitario,cedula', 
        'correo' => 'required|email|max:255|unique:lider_comunitario,correo',
        'telefono' => 'nullable|numeric|digits_between:7,15',
    ];
    
    $messages = [
        'cedula.unique' => 'Esta cédula ya está registrada.',
        'correo.unique' => 'Este correo ya está registrado.',
        'correo.email' => 'El correo debe ser una dirección de correo electrónico válida.',
    ];
    
    $request->validate($rules, $messages);

    try {
        $estado = 'sucre';
        $municipio ='sucre';
        $comunidad = $request->input('comunidad');
        $sector = $request->input('sector');
        $direccion = direccion::firstOrCreate(
            ['estado' => $estado, 'municipio' => $municipio, 'comunidad' => $comunidad, 'sector' => $sector,],
            ['estado' => $estado, 'municipio' => $municipio, 'comunidad' => $comunidad, 'sector' => $sector,]
        );
        $slug = Str::slug($request->input('nombre'));
        $originalSlug = $slug;
        $counter = 1;
        while (lider_comunitario::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $calle = $request->input('calle');
        $manzana = $request->input('manzana');
        $num_casa = $request->input('num_casa');

        $domicilio = new Domicilio(); 
        $domicilio->calle = $calle;
        $domicilio->manzana = $manzana;
        $domicilio->numero_de_casa = $num_casa;
        $domicilio->save();

        $usuario=Auth::user()->id_usuario;
        $lider = new lider_comunitario();
        $lider->slug = $slug;
        $lider->nombre = $request->input('nombre');
        $lider->apellido = $request->input('apellido');
        $lider->cedula = $request->input('cedula');
        $lider->correo = $request->input('correo');
        $lider->telefono = $request->input('telefono');
        $lider->id_direccion = $direccion->id_direccion; 
        $lider->id_domicilio=$domicilio->id_domicilio;
        $lider->id_usuario=$usuario;
        $lider->save();

        return redirect()->route('lideres.index')->with('success', 'Datos enviados correctamente');
    } catch (\Exception $e) {
        return redirect()->route('lideres.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
    }
}
public function show($slug){
    $lider= lider_comunitario::where('slug', $slug)->firstOrFail();
    
    return view('lideres.lider', compact('lider'));
}
public function buscar(Request $request)
{
    $cedula = $request->input('buscar');
    $lider= lider_comunitario::where('cedula', $cedula)->first();
    if (!$lider) {
        return view('lideres.busquedaLider')->with('lider', null);
    }
    return view('lideres.busquedaLider')->with('lider', $lider);
}
public function edit($slug)
    {
        $lideres=lider_comunitario::all();
        $direcciones= Direccion::all();
        $lider = lider_comunitario::where('slug', $slug)->firstOrFail();
        return view('lideres.modificarLider', compact('lider','direcciones','lideres'));
    }

    public function update(Request $request, $slug)
    {
       
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|integer|unique:lider_comunitario,cedula,' . $slug . ',slug',
            'correo' => 'required|email|max:255|unique:lider_comunitario,correo,' . $slug . ',slug',
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
    
            
            $lider = Lider_Comunitario::where('slug', $slug)->first();
    
            if (!$lider) {
                return redirect()->route('lideres.index')->with('error', 'Líder no encontrado con el slug: ' . $slug);
            }
    
            $usuario=Auth::user()->id_usuario;
            $lider->nombre = $request->input('nombre');
            $lider->apellido = $request->input('apellido');
            $lider->cedula = $request->input('cedula');
            $lider->correo = $request->input('correo');
            $lider->telefono = $request->input('telefono');
            $lider->id_direccion = $direccion->id_direccion;
            $lider->id_domicilio = $domicilio->id_domicilio;
            $lider->id_usuario=$usuario;
            $lider->save();  
    
            
            return redirect()->route('lideres.index')->with('success', 'Líder actualizado correctamente');
        } catch (\Exception $e) {

            return redirect()->route('lideres.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
    }
    
}