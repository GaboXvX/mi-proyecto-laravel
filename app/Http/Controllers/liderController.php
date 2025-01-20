<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeLiderRequest;
use App\Http\Requests\updateLiderRequest;
use App\Models\Comunidad;
use App\Models\Direccion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\Parroquia;
use App\Models\Lider;
use App\Models\Persona;
use App\Models\Sector;
use App\Models\Urbanizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class liderController extends Controller
{
    public function index(){
        $lideres=lider_comunitario::all();
        return view('lideres.listalideres',compact('lideres'));
    }
public function create(){
    return view('lideres.registrarlideres');
}
public function show($slug){
    $lider= lider_comunitario::where('slug', $slug)->firstOrFail();
    
    return view('lideres.lider', compact('lider'));
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

        // Verificar si ya hay un líder en la comunidad
        $liderExistente = Lider_Comunitario::where('id_comunidad', $comunidad)->first();
        if ($liderExistente) {
            return redirect()->route('lideres.index')->with('error', 'Ya existe un líder asignado a esta comunidad.');
        }

        // Comprobar si la dirección ya existe
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

        // Verificar si el slug ya existe en las tablas correspondientes
        $slug = Str::slug($request->input('nombre'));
        $slugCount = Lider_Comunitario::where('slug', $slug)->count() + Persona::where('slug', $slug)->count();

        if ($slugCount > 0) {
            $originalSlug = $slug;
            $counter = 1;

            while (Lider_Comunitario::where('slug', $slug)->exists() || Persona::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Crear el líder
        $lider = new Lider_Comunitario();
        $lider->slug = $slug;
        $lider->nombre = $request->input('nombre');
        $lider->apellido = $request->input('apellido');
        $lider->cedula = $request->input('cedula');
        $lider->correo = $request->input('correo');
        $lider->telefono = $request->input('telefono');
        $lider->id_direccion = $direccion->id_direccion;
        $lider->id_comunidad = $comunidad;
        $lider->id_usuario = Auth::user()->id_usuario;
        $lider->save();

        // Crear el movimiento
        $movimiento = new Movimiento();
        $movimiento->id_usuario = Auth::user()->id_usuario;
        $movimiento->id_lider = $lider->id_lider;
        $camposCreado = [
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'cedula' => $request->input('cedula'),
            'correo' => $request->input('correo'),
            'telefono' => $request->input('telefono'),
            'parroquia' => $direccion->parroquia ? $direccion->parroquia->nombre : 'No disponible',
            'urbanizacion' => $direccion->urbanizacion ? $direccion->urbanizacion->nombre : 'No disponible',
            'sector' => $direccion->sector ? $direccion->sector->nombre : 'No disponible',
            'comunidad' => $direccion->comunidad ? $direccion->comunidad->nombre : 'No disponible',
            'lider' => $lider->nombre . " " . $lider->apellido . " " . $lider->cedula ? : 'No disponible',
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

public function actualizar(updateLiderRequest $request, $slug)
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

        // Buscar el líder por su slug
        $lider = Lider_Comunitario::where('slug', $slug)->first();

        if (!$lider) {
            return redirect()->route('lider.index')->with('error', 'Líder no encontrado con el slug: ' . $slug);
        }

        // Verificar si ya existe un líder en la comunidad
        $liderExistente = Lider_Comunitario::where('id_comunidad', $comunidad)->where('id_lider', '!=', $lider->id_lider)->first();
        if ($liderExistente) {
            return redirect()->route('lider.index')->with('error', 'Ya existe un líder asignado a esta comunidad.');
        }

        // Comprobar si la dirección ya existe o crearla si no existe
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

        // Validar y actualizar los campos del líder
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

        // Actualizar los datos de los campos
        $lider->nombre = $request->input('nombre');
        $lider->apellido = $request->input('apellido');
        $lider->cedula = $request->input('cedula');
        $lider->correo = $request->input('correo');
        $lider->telefono = $request->input('telefono');

        // Asignar nueva dirección si ha cambiado
        if ($lider->direccion->parroquia->nombre !== $request->input('parroquia')) {
            $lider->direccion->parroquia = Parroquia::find($request->input('parroquia'));
            $camposModificados['parroquia'] = $lider->direccion->parroquia->nombre;
        }

        if ($lider->direccion->urbanizacion->nombre !== $request->input('urbanizacion')) {
            $lider->direccion->urbanizacion = Urbanizacion::find($request->input('urbanizacion'));
            $camposModificados['urbanizacion'] = $lider->direccion->urbanizacion->nombre;
        }

        if ($lider->direccion->sector->nombre !== $request->input('sector')) {
            $lider->direccion->sector = Sector::find($request->input('sector'));
            $camposModificados['sector'] = $lider->direccion->sector->nombre;
        }

        if ($lider->direccion->comunidad->nombre !== $request->input('comunidad')) {
            $lider->direccion->comunidad = Comunidad::find($request->input('comunidad'));
            $camposModificados['comunidad'] = $lider->direccion->comunidad->nombre;
        }

        // Actualizar la dirección
        $lider->direccion->calle = $request->input('calle');
        $lider->direccion->manzana = $request->input('manzana');
        $lider->direccion->numero_de_casa = $request->input('num_casa');
        $lider->direccion->save();

        // Guardar cambios en el líder
        $lider->id_usuario = Auth::user()->id_usuario;
        $lider->id_direccion = $direccion->id_direccion;
        $lider->save();

        // Registro del movimiento
        if (!empty($camposModificados)) {
            $movimiento = new Movimiento();
            $movimiento->id_usuario = Auth::user()->id_usuario;
            $movimiento->id_lider = $lider->id_lider;
            $movimiento->accion = 'se ha actualizado un registro';
            $movimiento->valor_nuevo = json_encode($camposModificados);
            $movimiento->valor_anterior = json_encode($camposAntiguos);
            $movimiento->save();
        }

        return redirect()->route('lider.index')->with('success', 'Datos actualizados correctamente');
    } catch (\Exception $e) {
        return redirect()->route('lider.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
    }
}

public function edit($slug){


    $lider= lider_comunitario::where('slug', $slug)->firstOrFail();
    return view('lideres.modificarLider', compact('lider'));
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

}  
