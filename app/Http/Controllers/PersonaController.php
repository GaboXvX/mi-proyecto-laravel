<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Models\Direccion;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PersonaController extends Controller
{
    public function create()
    {
        $lideres = Lider_Comunitario::all();

        $comunidades = [
            'Comunidad A' => ['Sector 1', 'Sector 2', 'Sector 3'],
            'Comunidad B' => ['Sector 4', 'Sector 5'],
            'Comunidad C' => ['Sector 6', 'Sector 7', 'Sector 8'],
        ];
        return view('personas.registrarPersonas', compact('lideres', 'comunidades'));
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

            // Ahora puedes trabajar con la dirección, ya sea encontrada o recién creada



            $persona = new Persona();
            $slug = Str::slug($request->input('nombre'));
            $originalSlug = $slug;
            $counter = 1;


            while (Persona::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }


            $usuario = Auth::user()->id_usuario;
            $persona->slug=$slug;
            $persona->nombre = $request->input('nombre');
            $persona->apellido = $request->input('apellido');
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->id_direccion = $direccion->id_direccion;
            $persona->id_usuario = $usuario;
            $persona->id_lider = $request->input('lider_comunitario');
            $persona->save();
            $movimiento = new Movimiento();
            $movimiento->id_usuario = Auth::user()->id_usuario;
            $movimiento->id_persona = $persona->id_persona;
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
        $lideres = lider_comunitario::all();
        $direcciones = Direccion::all();
        $persona = Persona::where('slug', $slug)->firstOrFail();
        return view('personas.modificarPersonas', compact('persona', 'direcciones', 'lideres'));
    }


    public function update(Request $request, $slug)
    {
        // Validación de datos
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

            // Buscar la dirección en la base de datos
            $direccion = Direccion::where('estado', $estado)
                ->where('municipio', $municipio)
                ->where('comunidad', $comunidad)
                ->where('sector', $sector)
                ->where('calle', $calle)
                ->where('manzana', $manzana)
                ->where('numero_de_casa', $num_casa)
                ->first();

            $originalSlug = $slug;

            $persona = Persona::where('slug', $originalSlug)->first();

            if (!$persona) {
                return redirect()->route('personas.index')->with('error', 'Persona no encontrada con el slug: ' . $originalSlug);
            }



            $camposModificados = [];
            $camposAntiguos = [
                'nombre' => $persona->nombre,
                'apellido' => $persona->apellido,
                'cedula' => $persona->cedula,
                'correo' => $persona->correo,
                'telefono' => $persona->telefono,
                'comunidad' => $persona->direccion->comunidad,
                'sector' => $persona->direccion->sector,
                'calle' => $persona->direccion->calle,
                'manzana' => $persona->direccion->manzana,
                'numero de casa' => $persona->direccion->numero_de_casa,
            ];


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
            if ($persona->nombre !== $request->input('nombre')) {
                $camposModificados['nombre'] = $request->input('nombre');
                $persona->nombre = $request->input('nombre');
            }

            if ($persona->apellido !== $request->input('apellido')) {
                $camposModificados['apellido'] = $request->input('apellido');
                $persona->apellido = $request->input('apellido');
            }

            if ($persona->cedula != $request->input('cedula')) {
                $camposModificados['cedula'] = $request->input('cedula');
                $persona->cedula = $request->input('cedula');
            }

            if ($persona->correo !== $request->input('correo')) {
                $camposModificados['correo'] = $request->input('correo');
                $persona->correo = $request->input('correo');
            }

            if ($persona->telefono != $request->input('telefono')) {
                $camposModificados['telefono'] = $request->input('telefono');
                $persona->telefono = $request->input('telefono');
            }
            if ($persona->direccion->comunidad !== $request->input('comunidad')) {
                $camposModificados['comunidad'] = $request->input('comunidad');
                $persona->direccion->comunidad = $request->input('comunidad');
            }

            // Comparar y actualizar el campo 'sector'
            if ($persona->direccion->sector !== $request->input('sector')) {
                $camposModificados['sector'] = $request->input('sector');
                $persona->direccion->sector = $request->input('sector');
            }

            // Comparar y actualizar el campo 'calle'
            if ($persona->direccion->calle !== $request->input('calle')) {
                $camposModificados['calle'] = $request->input('calle');
                $persona->direccion->calle = $request->input('calle');
            }

            // Comparar y actualizar el campo 'manzana'
            if ($persona->direccion->manzana !== $request->input('manzana')) {
                $camposModificados['manzana'] = $request->input('manzana');
                $persona->direccion->manzana = $request->input('manzana');
            }

            // Comparar y actualizar el campo 'numero de casa'
            if ($persona->direccion->numero_de_casa != $request->input('num_casa')) {
                $camposModificados['numero de casa'] = $request->input('num_casa');
                $persona->direccion->numero_de_casa = $request->input('num_casa');
            }
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->id_direccion = $direccion->id_direccion;
            $persona->save();
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
            return view('personas.busqueda')->with('persona', null);
        }
        return view('personas.busqueda')->with('persona', $persona);
    }
}
