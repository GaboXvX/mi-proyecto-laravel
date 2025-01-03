<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Models\direccion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
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
        
        $comunidades = [
            'Comunidad A' => ['Sector 1', 'Sector 2', 'Sector 3'],
            'Comunidad B' => ['Sector 4', 'Sector 5'],
            'Comunidad C' => ['Sector 6', 'Sector 7', 'Sector 8'],
        ];
        return view('lideres.registrarlideres',compact('comunidades'));
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
            $municipio = 'sucre';
            $comunidad = $request->input('comunidad');
            $sector = $request->input('sector');
            $calle = $request->input('calle');
            $manzana = $request->input('manzana');
            $num_casa = $request->input('num_casa');

            // Buscar la dirección en la base de datos
            $direccion = Direccion::where('estado', $estado)
                ->where('municipio', $municipio)
                ->where('comunidad', $comunidad)
                ->where('sector', $sector)
                ->where('calle', $calle)
                ->where('manzana', $manzana)
                ->where('numero_de_casa', $num_casa)
                ->first();

            // Si la dirección no existe, crearla
            if (!$direccion) {
                $direccion = new Direccion();
                $direccion->estado = $estado;
                $direccion->municipio = $municipio;
                $direccion->comunidad = $comunidad;
                $direccion->sector = $sector;
                $direccion->calle = $calle;
                $direccion->manzana = $manzana;
                $direccion->numero_de_casa = $num_casa;
                $direccion->save();
            }

        $slug = Str::slug($request->input('nombre'));
        $originalSlug = $slug;
        $counter = 1;
        while (lider_comunitario::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
       
        $usuario=Auth::user()->id_usuario;
        $lider = new lider_comunitario();
        $lider->slug = $slug;
        $lider->nombre = $request->input('nombre');
        $lider->apellido = $request->input('apellido');
        $lider->cedula = $request->input('cedula');
        $lider->correo = $request->input('correo');
        $lider->telefono = $request->input('telefono');
        $lider->id_direccion = $direccion->id_direccion; 
        $lider->id_usuario=$usuario;
        $lider->save();
        $movimiento= new movimiento();
        $movimiento->id_usuario=Auth::user()->id_usuario;
        $movimiento->id_lider=$lider->id_lider;
        $camposCreado = [
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'cedula' => $request->input('cedula'),
            'correo' => $request->input('correo'),
            'telefono' => $request->input('telefono'),
            'comunidad' => $direccion->comunidad,
            'sector'=>$direccion->sector,
            'calle'=>$direccion->calle,
            'manzana'=>$direccion->manzana,
            'numero de casa'=>$direccion->numero_de_casa,
        ];
        $movimiento->accion = 'se ha creado un registro';
        $movimiento->valor_anterior = json_encode($camposCreado);  
        $movimiento->save();
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

           
            $direccion = Direccion::where('estado', $estado)
                ->where('municipio', $municipio)
                ->where('comunidad', $comunidad)
                ->where('sector', $sector)
                ->where('calle', $calle)
                ->where('manzana', $manzana)
                ->where('numero_de_casa', $num_casa)
                ->first();

            // Si la dirección no existe, crearla
          
            
            $lider = Lider_Comunitario::where('slug', $slug)->first();
    
            if (!$lider) {
                return redirect()->route('lideres.index')->with('error', 'Líder no encontrado con el slug: ' . $slug);
            }
            $camposModificados = [];
            $camposAntiguos = [
            'nombre' => $lider->nombre,
            'apellido' => $lider->apellido,
            'cedula' => $lider->cedula,
            'correo' => $lider->correo,
            'telefono' => $lider->telefono,
            'comunidad' => $lider->direccion->comunidad,
            'sector'=>$lider->direccion->sector,
            'calle'=>$lider->direccion->calle,
            'manzana'=>$lider->direccion->manzana,
            'numero de casa'=>$lider->direccion->numero_de_casa,
            ];
            if (!$direccion) {
                $direccion = new Direccion();
                $direccion->estado = $estado;
                $direccion->municipio = $municipio;
                $direccion->comunidad = $comunidad;
                $direccion->sector = $sector;
                $direccion->calle = $calle;
                $direccion->manzana = $manzana;
                $direccion->numero_de_casa = $num_casa;
                $direccion->save();
            }

            if ($lider->nombre !== $request->input('nombre')) {
                $camposModificados['nombre'] = $request->input('nombre');
                $lider->nombre = $request->input('nombre'); 
            }
    
            if ($lider->apellido !== $request->input('apellido')) {
                $camposModificados['apellido'] = $request->input('apellido');
                $lider->apellido = $request->input('apellido'); 
            }
    
            if ($lider->cedula != $request->input('cedula')) {
                $camposModificados['cedula'] = $request->input('cedula');
                $lider->cedula = $request->input('cedula'); 
            }
    
            if ($lider->correo !== $request->input('correo')) {
                $camposModificados['correo'] = $request->input('correo');
                $lider->correo = $request->input('correo'); 
            }
    
            if ($lider->telefono != $request->input('telefono')) {
                $camposModificados['telefono'] = $request->input('telefono');
                $lider->telefono = $request->input('telefono'); 
            }
            
            if ($lider->direccion->comunidad !== $request->input('comunidad')) {
                $camposModificados['comunidad'] = $request->input('comunidad');
                $lider->direccion->comunidad = $request->input('comunidad'); 
            }
            
            // Comparar y actualizar el campo 'sector'
            if ($lider->direccion->sector !== $request->input('sector')) {
                $camposModificados['sector'] = $request->input('sector');
                $lider->direccion->sector = $request->input('sector');
            }
            
            // Comparar y actualizar el campo 'calle'
            if ($lider->direccion->calle !== $request->input('calle')) {
                $camposModificados['calle'] = $request->input('calle');
                $lider->direccion->calle = $request->input('calle'); 
            }
            
            // Comparar y actualizar el campo 'manzana'
            if ($lider->direccion->manzana !== $request->input('manzana')) {
                $camposModificados['manzana'] = $request->input('manzana');
                $lider->direccion->manzana = $request->input('manzana');
            }
            
            // Comparar y actualizar el campo 'numero de casa'
            if ($lider->direccion->numero_de_casa != $request->input('num_casa')) {
                $camposModificados['numero de casa'] = $request->input('num_casa');
                $lider->direccion->numero_de_casa = $request->input('num_casa');
            }
            $lider->id_usuario = Auth::user()->id_usuario;
                $lider->id_direccion = $direccion->id_direccion;
                $lider->save();
                
            if (!empty($camposModificados)) {
                $movimiento = new Movimiento();
                $movimiento->id_usuario = Auth::user()->id_usuario;
                $movimiento->id_lider = $lider->id_lider;
                $movimiento->accion = 'se ha actualizado un registro';
                $movimiento->valor_nuevo = json_encode($camposModificados); 
                $movimiento->valor_anterior=json_encode($camposAntiguos);
                $movimiento->save();
            } 
          
            return redirect()->route('lideres.index')->with('success', 'Líder actualizado correctamente');
        } catch (\Exception $e) {

            return redirect()->route('lideres.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
    }
    
}