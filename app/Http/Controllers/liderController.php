<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeLiderRequest;
use App\Http\Requests\updateLiderRequest;
use App\Models\Comunidad;
use Illuminate\Support\Str;
use App\Models\direccion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\Parroquia;
use App\Models\Persona;
use App\Models\Sector;
use App\Models\Urbanizacion;
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
        
        return view('lideres.registrarlideres');
    }

    public function store(storeLiderRequest $request)
{
  

    try {
        $estado = 'sucre';
        $municipio = 'sucre';
        
        
        $parroquia = $request->input('parroquia');
        $urbanizacion = $request->input('urbanizacion');
        $sector = $request->input('sector');
        $comunidad = $request->input('comunidad');
        $calle = $request->input('calle');
        $manzana = $request->input('manzana');
        $num_casa = $request->input('num_casa');
        

        $direccion = Direccion::where('estado', $estado)
            ->where('municipio', $municipio)
              ->where('id_parroquia',$parroquia)
             ->where('id_urbanizacion',$urbanizacion) 
           ->where('id_sector', $sector)
             ->where('id_comunidad', $comunidad)
            ->where('calle', $calle)
            ->where('manzana', $manzana)
            ->where('numero_de_casa', $num_casa)
            ->first();
        
   
        if (!$direccion) {
            $direccion = new Direccion();
            $direccion->estado = $estado;
            $direccion->municipio = $municipio;
            $direccion->id_comunidad = $comunidad;
            $direccion->id_sector = $sector;
            $direccion->calle = $calle;
            $direccion->manzana = $manzana;
            $direccion->numero_de_casa = $num_casa;
            $direccion->id_parroquia = $parroquia;
            $direccion->id_urbanizacion = $urbanizacion;
            $direccion->save();
        }else{
            return redirect()->back()->with('error', 'Ya existe un líder registrado para esta dirección.');
        }

        
        $slug = Str::slug($request->input('nombre'));
        $count = Persona::where('slug', $slug)->count() + Lider_Comunitario::where('slug', $slug)->count();

        if ($count > 0) {

            $originalSlug = $slug;
            $counter = 1;
    
           
            while (Persona::where('slug', $slug)->exists() || Lider_Comunitario::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

   
        $usuario = Auth::user()->id_usuario;

      
        $lider = new lider_comunitario();
        $lider->slug = $slug;
        $lider->nombre = $request->input('nombre');
        $lider->apellido = $request->input('apellido');
        $lider->cedula = $request->input('cedula');
        $lider->correo = $request->input('correo');
        $lider->telefono = $request->input('telefono');
        $lider->id_direccion = $direccion->id_direccion;
        $lider->id_comunidad = $comunidad;
        $lider->id_usuario = $usuario;
        $lider->save();

       
        $movimiento = new movimiento();
        $movimiento->id_usuario = Auth::user()->id_usuario;
        $movimiento->id_lider = $lider->id_lider;

       
        $camposCreado = [
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'cedula' => $request->input('cedula'),
            'correo' => $request->input('correo'),
            'telefono' => $request->input('telefono'),
            'parroquia'=>$direccion->parroquia ? $direccion->parroquia->nombre:'No disponible',
            'urbanizacion'=>$direccion->urbanizacion ? $direccion->urbanizacion->nombre:'No disponible',
             'sector' => $direccion->sector ? $direccion->sector->nombre : 'No disponible',
            'comunidad' => $direccion->comunidad ? $direccion->comunidad->nombre : 'No disponible',
            'calle' => $direccion->calle,
            'manzana' => $direccion->manzana,
            'numero_de_casa' => $direccion->numero_de_casa,
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
        return view('lideres.listalideres')->with('lider', null);
    }
    return view('lideres.listalideres')->with('lideres', [$lider]);
}
public function edit($slug)
    {
        $lideres=lider_comunitario::all();
        $direcciones= Direccion::all();
        $lider = lider_comunitario::where('slug', $slug)->firstOrFail();
        return view('lideres.modificarLider', compact('lider','direcciones','lideres'));
    }

    public function update(updateLiderRequest $request, $slug) 
    {
      
    
        try {
            $estado = 'sucre';
            $municipio = 'sucre';
    
            $parroquia = $request->input('parroquia');
            $urbanizacion = $request->input('urbanizacion');
            $sector = $request->input('sector');
            $comunidad = $request->input('comunidad');
            $calle = $request->input('calle');
            $manzana = $request->input('manzana');
            $num_casa = $request->input('num_casa');
    
            $direccion = Direccion::where('estado', $estado)
                ->where('municipio', $municipio)
                ->where('id_parroquia', $parroquia)
                ->where('id_urbanizacion', $urbanizacion)
                ->where('id_sector', $sector)
                ->where('id_comunidad', $comunidad)
                ->where('calle', $calle)
                ->where('manzana', $manzana)
                ->where('numero_de_casa', $num_casa)
                ->first();
    
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
                'parroquia' => $lider->direccion->parroquia->nombre ?? 'No disponible',
                'urbanizacion' => $lider->direccion->urbanizacion->nombre ?? 'No disponible',
                'sector' => $lider->direccion->sector->nombre ?? 'No disponible',
                'comunidad' => $lider->direccion->comunidad->nombre ?? 'No disponible',
                'calle' => $lider->direccion->calle,
                'manzana' => $lider->direccion->manzana,
                'numero_de_casa' => $lider->direccion->numero_de_casa,
            ];
    
            if (!$direccion) {
                $direccion = new Direccion();
                $direccion->estado = $estado;
                $direccion->municipio = $municipio;
                $direccion->id_comunidad = $comunidad;
                $direccion->id_sector = $sector;
                $direccion->calle = $calle;
                $direccion->manzana = $manzana;
                $direccion->numero_de_casa = $num_casa;
                $direccion->id_parroquia = $parroquia;
                $direccion->id_urbanizacion = $urbanizacion;
                $direccion->save();
            }
            else{
                return redirect()->back()->with('error', 'Ya existe un líder registrado para esta dirección.');
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
    
            if ($lider->direccion->parroquia->id_parroquia != $request->input('parroquia')) {
                $camposModificados['parroquia'] = $request->input('parroquia');
                $lider->direccion->parroquia = Parroquia::find($request->input('parroquia'));
            }
    
            if ($lider->direccion->urbanizacion->id_urbanizacion != $request->input('urbanizacion')) {
                $camposModificados['urbanizacion'] = $request->input('urbanizacion');
                $lider->direccion->urbanizacion = Urbanizacion::find($request->input('urbanizacion'));
            }
    
            if ($lider->direccion->sector->id_sector != $request->input('sector')) {
                $camposModificados['sector'] = $request->input('sector');
                $lider->direccion->sector = Sector::find($request->input('sector'));
            }
    
            if ($lider->direccion->comunidad->id_comunidad != $request->input('comunidad')) {
                $camposModificados['comunidad'] = $request->input('comunidad');
                $lider->direccion->comunidad = Comunidad::find($request->input('comunidad')); 
            }
    
            if ($lider->direccion->calle !== $request->input('calle')) {
                $camposModificados['calle'] = $request->input('calle');
                $lider->direccion->calle = $request->input('calle'); 
            }
    
            if ($lider->direccion->manzana !== $request->input('manzana')) {
                $camposModificados['manzana'] = $request->input('manzana');
                $lider->direccion->manzana = $request->input('manzana');
            }
    
            if ($lider->direccion->numero_de_casa != $request->input('num_casa')) {
                $camposModificados['numero_de_casa'] = $request->input('num_casa');
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
                $movimiento->valor_anterior = json_encode($camposAntiguos);
                $movimiento->save();
            }
    
            return redirect()->route('lideres.index')->with('success', 'Líder actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('lideres.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
    }
    

    
}