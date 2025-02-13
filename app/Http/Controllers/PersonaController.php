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
           

            $parroquia = $request->input('parroquia');
            $urbanizacion = $request->input('urbanizacion');
            $sector = $request->input('sector');
            $comunidad = $request->input('comunidad');
            $calle = $request->input('calle');
            $manzana = $request->input('manzana');
            $num_casa = $request->input('num_casa');

            $direccion = Direccion::
                where('id_parroquia', $parroquia)
                ->where('id_urbanizacion', $urbanizacion)
                ->where('id_sector', $sector)
                ->where('id_comunidad', $comunidad)
                ->where('calle', $calle)
                ->where('manzana', $manzana)
                ->where('numero_de_casa', $num_casa)
                ->first();

            if (!$direccion) {
                $direccion = new Direccion();
                $direccion->id_comunidad = $comunidad;
                $direccion->id_sector = $sector;
                $direccion->calle = $calle;
                $direccion->manzana = $manzana;
                $direccion->numero_de_casa = $num_casa;
                $direccion->id_parroquia = $parroquia;
                $direccion->id_urbanizacion = $urbanizacion;
                $direccion->save();
            }

            $persona = new Persona();
            $slug = Str::slug($request->input('nombre'));
            $count = Persona::where('slug', $slug)->count();

            if ($count > 0) {
                $originalSlug = $slug;
                $counter = 1;

                while (Persona::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            $lider = Persona::whereHas('direccion', function ($query) use ($comunidad) {
                $query->where('id_comunidad', $comunidad);
            })
                ->where('es_lider', 1)
                ->first();
            if ($lider && $request->input('lider_comunitario') == 1) {
                return redirect()->route('personas.index')->with('error', 'ya existe un lider para esa comunidad ');
            }
            $persona->slug = $slug;
            $persona->nombre = $request->input('nombre');
            $persona->apellido = $request->input('apellido');
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->id_direccion = $direccion->id_direccion;
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->es_lider = $request->input('lider_comunitario');
            $persona->save();

            if ($persona->es_lider == 1) {
                $lider = new Lider_Comunitario();
                $lider->id_persona = $persona->id_persona;
                $lider->id_comunidad = $comunidad;
                $lider->estado = 1;
                $lider->save();
            }

            $movimiento = new Movimiento();
            $movimiento->id_usuario = Auth::user()->id_usuario;
            $movimiento->id_persona = $persona->id_persona;
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
                'calle' => $direccion->calle,
                'manzana' => $direccion->manzana,
                'numero_de_casa' => $direccion->numero_de_casa,
                'es lider' => $request->input('lider_comunitario'),
            ];

            $movimiento->accion = 'se ha creado un registro';
            $movimiento->valor_anterior = json_encode($camposCreado);
            $movimiento->save();

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

        if (!$persona) {
            return redirect()->route('personas.index')->with('error', 'Persona no encontrada con el slug: ' . $slug);
        }

        $direccion = $persona->direccion;

        $camposAntiguos = [
            'nombre' => $persona->nombre,
            'apellido' => $persona->apellido,
            'cedula' => $persona->cedula,
            'correo' => $persona->correo,
            'telefono' => $persona->telefono,
            'parroquia' => $direccion->parroquia->nombre ?? 'No disponible',
            'urbanizacion' => $direccion->urbanizacion->nombre ?? 'No disponible',
            'sector' => $direccion->sector->nombre ?? 'No disponible',
            'comunidad' => $direccion->comunidad->nombre ?? 'No disponible',
            'calle' => $direccion->calle,
            'manzana' => $direccion->manzana,
            'numero_de_casa' => $direccion->numero_de_casa,
            'es_lider' => $persona->es_lider,
        ];

        $persona->nombre = $request->input('nombre');
        $persona->apellido = $request->input('apellido');
        $persona->cedula = $request->input('cedula');
        $persona->correo = $request->input('correo');
        $persona->telefono = $request->input('telefono');
        $persona->id_usuario = Auth::user()->id_usuario;

        $direccionModificada = false;
        $camposModificados = [];

        if ($direccion->id_parroquia != $request->input('parroquia')) {
            $direccion->id_parroquia = $request->input('parroquia');
            $camposModificados['parroquia'] = $request->input('parroquia');
            $direccionModificada = true;
        }
        if ($direccion->id_urbanizacion != $request->input('urbanizacion')) {
            $direccion->id_urbanizacion = $request->input('urbanizacion');
            $camposModificados['urbanizacion'] = $request->input('urbanizacion');
            $direccionModificada = true;
        }
        if ($direccion->id_sector != $request->input('sector')) {
            $direccion->id_sector = $request->input('sector');
            $camposModificados['sector'] = $request->input('sector');
            $direccionModificada = true;
        }
        if ($direccion->id_comunidad != $request->input('comunidad')) {
            $direccion->id_comunidad = $request->input('comunidad');
            $camposModificados['comunidad'] = $request->input('comunidad');
            $direccionModificada = true;
        }
        if ($direccion->calle != $request->input('calle')) {
            $direccion->calle = $request->input('calle');
            $camposModificados['calle'] = $request->input('calle');
            $direccionModificada = true;
        }
        if ($direccion->manzana != $request->input('manzana')) {
            $direccion->manzana = $request->input('manzana');
            $camposModificados['manzana'] = $request->input('manzana');
            $direccionModificada = true;
        }
        if ($direccion->numero_de_casa != $request->input('num_casa')) {
            $direccion->numero_de_casa = $request->input('num_casa');
            $camposModificados['numero_de_casa'] = $request->input('num_casa');
            $direccionModificada = true;
        }

        if ($direccionModificada) {
            $direccion->save();
            $persona->id_direccion = $direccion->id_direccion;
        }

        $esLiderNuevo = $request->input('lider_comunitario');
        if ($persona->es_lider != $esLiderNuevo) {
            $persona->es_lider = $esLiderNuevo;

            if ($esLiderNuevo == 1) {
                $otroLider = Persona::whereHas('direccion', function ($query) use ($direccion) {
                    $query->where('id_comunidad', $direccion->id_comunidad);
                })->where('es_lider', 1)->first();

                if ($otroLider) {
                    return redirect()->route('personas.index')->with('error', 'Ya existe un líder activo para esta comunidad.');
                }

                $persona->lider_Comunitario()->updateOrCreate(
                    ['id_persona' => $persona->id_persona],
                    ['estado' => true, 'id_comunidad' => $direccion->id_comunidad]
                );
            } else {
                $persona->lider_Comunitario()->update(['estado' => false]);
            }
        }

        $persona->save();

        foreach ($camposAntiguos as $campo => $valorAntiguo) {
            $valorNuevo = $persona->$campo;

            if ($valorNuevo != $valorAntiguo && !isset($camposModificados[$campo])) {
                $camposModificados[$campo] = $valorNuevo;
            }
        }

        if (!empty($camposModificados)) {
            $movimiento = new Movimiento();
            $movimiento->id_usuario = Auth::user()->id_usuario;
            $movimiento->id_persona = $persona->id_persona;
            $movimiento->accion = 'se ha actualizado un registro';
            $movimiento->valor_nuevo = json_encode($camposModificados);
            $movimiento->valor_anterior = json_encode($camposAntiguos);
            $movimiento->save();
        }

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
            return redirect()->route('personas.index')->with('error', 'Persona no encontrada');
        }

        return view('personas.listapersonas')->with('personas', [$persona]);
    }
}
