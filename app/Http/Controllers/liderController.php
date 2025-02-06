<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeLiderRequest;
use App\Http\Requests\updateLiderRequest;
use App\Models\Comunidad;
use App\Models\Direccion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\Parroquia;
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
    if($lider){
    return view('lideres.lider', compact('lider'));
}
else{
    return redirect()->route('lideres.index');
}
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

        
        $liderExistente = Lider_Comunitario::where('id_comunidad', $comunidad)->where('estado','activo')->first();
        if ($liderExistente) {
            return redirect()->route('lideres.index')->with('error', 'Ya existe un líder asignado a esta comunidad.');
        }

       
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

        
        $lider = new Lider_Comunitario();
        $lider->slug = $slug;
        $lider->estado = $request->input('estado');
        $lider->nombre = $request->input('nombre');
        $lider->apellido = $request->input('apellido');
        $lider->cedula = $request->input('cedula');
        $lider->correo = $request->input('correo');
        $lider->telefono = $request->input('telefono');
        $lider->id_direccion = $direccion->id_direccion;
        $lider->id_comunidad = $comunidad;
        $lider->id_usuario = Auth::user()->id_usuario;
        $lider->save();

       
        $movimiento = new Movimiento();
        $movimiento->id_usuario = Auth::user()->id_usuario;
        $movimiento->id_lider = $lider->id_lider;
        $camposCreado = [
            'estado' => $request->input('estado'),
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

public function update(updateLiderRequest $request, $slug)
{
    try {
        $estado = 'sucre';
        $municipio = 'sucre';

        // Obtener datos del formulario
        $parroquia = $request->input('parroquia');
        $urbanizacion = $request->input('urbanizacion');
        $sector = $request->input('sector');
        $comunidad = $request->input('comunidad');
        $calle = $request->input('calle');
        $manzana = $request->input('manzana');
        $num_casa = $request->input('num_casa');

        // Buscar el líder por slug
        $lider = Lider_Comunitario::where('slug', $slug)->first();
        if (!$lider) {
            return redirect()->route('lideres.index')->with('error', 'Líder no encontrado con el slug: ' . $slug);
        }

        // Verificar si la comunidad ha cambiado
        if ($lider->id_comunidad != $comunidad) {
            // Verificar si ya existe otro líder activo en la nueva comunidad
            $liderExistente = Lider_Comunitario::where('id_comunidad', $comunidad)
                ->where('id_lider', '!=', $lider->id_lider) // Comparar id_lider, no estado
                ->where('estado', 'activo')
                ->first();

            if ($liderExistente) {
                return redirect()->route('lideres.index')->with('error', 'Ya existe un líder activo en esta comunidad.');
            }
        }

        // Guardar valores antiguos para el movimiento
        $camposAntiguos = [
            'nombre' => $lider->nombre,
            'apellido' => $lider->apellido,
            'cedula' => $lider->cedula,
            'correo' => $lider->correo,
            'telefono' => $lider->telefono,
            'estado' => $lider->estado, // Incluir el estado antiguo
            'parroquia' => $lider->direccion->parroquia->nombre ?? 'No disponible',
            'urbanizacion' => $lider->direccion->urbanizacion->nombre ?? 'No disponible',
            'sector' => $lider->direccion->sector->nombre ?? 'No disponible',
            'comunidad' => $lider->direccion->comunidad->nombre ?? 'No disponible',
            'calle' => $lider->direccion->calle,
            'manzana' => $lider->direccion->manzana,
            'numero_de_casa' => $lider->direccion->numero_de_casa,
        ];

        // Buscar o crear la dirección
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
            $direccion->id_parroquia = $parroquia;
            $direccion->id_urbanizacion = $urbanizacion;
            $direccion->id_sector = $sector;
            $direccion->id_comunidad = $comunidad;
            $direccion->calle = $calle;
            $direccion->manzana = $manzana;
            $direccion->numero_de_casa = $num_casa;
            $direccion->save();
        }

        // Inicializar array de campos modificados
        $camposModificados = [];

        // Actualizar campos del líder
        if ($lider->nombre !== $request->input('nombre')) {
            $camposModificados['nombre'] = $request->input('nombre');
            $lider->nombre = $request->input('nombre');
        }

        if ($lider->apellido !== $request->input('apellido')) {
            $camposModificados['apellido'] = $request->input('apellido');
            $lider->apellido = $request->input('apellido');

            // Generar nuevo slug si el nombre o apellido cambian
            $nuevoSlug = Str::slug($lider->nombre . ' ' . $lider->apellido);
            while (Lider_Comunitario::where('slug', $nuevoSlug)->where('id_lider', '!=', $lider->id_lider)->exists()) {
                $nuevoSlug = Str::slug($lider->nombre . ' ' . $lider->apellido) . '-' . Str::random(5);
            }
            $lider->slug = $nuevoSlug;
        }

        if ($lider->cedula !== $request->input('cedula')) {
            $camposModificados['cedula'] = $request->input('cedula');
            $lider->cedula = $request->input('cedula');
        }

        if ($lider->correo !== $request->input('correo')) {
            $camposModificados['correo'] = $request->input('correo');
            $lider->correo = $request->input('correo');
        }

        if ($lider->telefono !== $request->input('telefono')) {
            $camposModificados['telefono'] = $request->input('telefono');
            $lider->telefono = $request->input('telefono');
        }

        // Actualizar el estado si ha cambiado
        if ($lider->estado !== $request->input('estado')) {
            $camposModificados['estado'] = $request->input('estado');
            $lider->estado = $request->input('estado');
        }

        // Actualizar dirección del líder
        $lider->id_direccion = $direccion->id_direccion;
        $lider->id_usuario = Auth::user()->id_usuario;
        $lider->save();
        // Buscar todas las personas que tienen el mismo id_lider
$personas = Persona::where('id_lider', $lider->id_lider)->get();

foreach ($personas as $persona) {
    // Obtener la comunidad asociada a la dirección de la persona
    $comunidad = $persona->direccion->comunidad;

    if ($comunidad) {
        // Buscar al líder activo dentro de esa comunidad a través de la tabla 'lider_comunitario'
        $liderActivo = $comunidad->lideres_comunitarios()
            ->where('estado', 'activo')
            ->first();  // Obtener el primer líder activo de esa comunidad
        
        if ($liderActivo) {
            // Si encontramos un líder activo, asignamos el id_lider de la persona
            $persona->id_lider = $liderActivo->id_lider;  // Asumimos que el campo 'id_lider' está en 'lider_comunitario'
            $persona->save();  // Guardar los cambios en la persona
        } else {
            // Si no hay líder activo, podrías manejar el caso aquí
            // Como asignar un líder por defecto o dejarlo vacío
        }
    }
}

        
        

        // Registrar movimiento si hay cambios
        if (!empty($camposModificados)) {
            $movimiento = new Movimiento();
            $movimiento->id_usuario = Auth::user()->id_usuario;
            $movimiento->id_lider = $lider->id_lider;
            $movimiento->accion = 'se ha actualizado un registro';
            $movimiento->valor_nuevo = json_encode($camposModificados);
            $movimiento->valor_anterior = json_encode($camposAntiguos);
            $movimiento->save();
        }

        return redirect()->route('lideres.index')->with('success', 'Datos actualizados correctamente');
    } catch (\Exception $e) {
        return redirect()->route('lideres.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
    }
}


public function edit($slug)
{
    $lider = Lider_Comunitario::where('slug', $slug)->first();
    if ($lider) {
        return view('lideres.modificarLider', compact('lider'));    }

    else  {
       
        return redirect()->route('lideres.index');
    }

  
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
