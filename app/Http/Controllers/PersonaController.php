<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
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

class PersonaController extends Controller
{
    public function create()
    {
        $lideres = Lider_Comunitario::all();

      
        return view('personas.registrarPersonas', compact('lideres'));
    }

    public function store(StorePersonaRequest $request)
{
    try {
        // Obtener los datos de la dirección del request
        $estado = 'sucre';
        $municipio = 'sucre';
        
        // Obtener los valores de las tablas relacionadas (parroquia, urbanizacion, sector, comunidad)
        $parroquia = $request->input('parroquia');
        $urbanizacion = $request->input('urbanizacion');
        $sector = $request->input('sector');
        $comunidad = $request->input('comunidad');
        $calle = $request->input('calle');
        $manzana = $request->input('manzana');
        $num_casa = $request->input('num_casa');
        
        // Buscar la dirección en la base de datos utilizando los IDs de las llaves foráneas
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

        // Si la dirección no existe, crearla
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

        // Buscar al líder comunitario de la misma comunidad
        $lider = Lider_Comunitario::where('id_comunidad',$comunidad
        )->first();

        if ($lider) {
            // Crear la nueva persona asociada al líder encontrado
            $persona = new Persona();
            $slug = Str::slug($request->input('nombre'));
            $count = Persona::where('slug', $slug)->count() + Lider_Comunitario::where('slug', $slug)->count();

    if ($count > 0) {
        // Si el slug ya existe, agrega un sufijo para hacerlo único
        $originalSlug = $slug;
        $counter = 1;

        // Mientras el slug exista en alguna de las tablas, incrementar el contador
        while (Persona::where('slug', $slug)->exists() || Lider_Comunitario::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    }

            $persona->slug = $slug;
            $persona->nombre = $request->input('nombre');
            $persona->apellido = $request->input('apellido');
            $persona->cedula = $request->input('cedula');
            $persona->correo = $request->input('correo');
            $persona->telefono = $request->input('telefono');
            $persona->id_direccion = $direccion->id_direccion;
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->id_lider = $lider->id_lider; // Asociando al líder
            $persona->save();

            // Registrar el movimiento
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
                'lider' => $lider->nombre ." ".$lider->apellido." ".$lider->cedula ? : 'No diponible',
                'calle' => $direccion->calle,
                'manzana' => $direccion->manzana,
                'numero_de_casa' => $direccion->numero_de_casa,
            ];
            
            $movimiento->accion = 'se ha creado un registro';
            $movimiento->valor_anterior = json_encode($camposCreado);
            $movimiento->save();

            return redirect()->route('personas.index')->with('success', 'Datos enviados correctamente');
        } else {
            return redirect()->route('personas.index')->with('error', 'No se encontró un líder para esta comunidad');
        }
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
            'lider_comunitario' => 'nullable|exists:lider_comunitario,id_lider', // Valida que el ID del líder exista
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
            'lider_comunitario.exists' => 'El líder comunitario seleccionado no existe.', // Mensaje de error para líder
        ];
    
        $request->validate($rules, $messages);
    
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
    
            $persona = Persona::where('slug', $slug)->first();
            $persona = Persona::with('lider_comunitario')->where('slug', $slug)->first();

            if (!$persona) {
                return redirect()->route('personas.index')->with('error', 'Persona no encontrada con el slug: ' . $slug);
            }
    
            $camposModificados = [];
            $camposAntiguos = [
                'nombre' => $persona->nombre,
                'apellido' => $persona->apellido,
                'cedula' => $persona->cedula,
                'correo' => $persona->correo,
                'telefono' => $persona->telefono,
                'parroquia' => $persona->direccion->parroquia->nombre ?? 'No disponible',
                'urbanizacion' => $persona->direccion->urbanizacion->nombre ?? 'No disponible',
                'sector' => $persona->direccion->sector->nombre ?? 'No disponible',
                'comunidad' => $persona->direccion->comunidad->nombre ?? 'No disponible',
                // Acceso seguro al líder, se verifica si está presente antes de acceder a sus propiedades
                'lider' => $persona->lider_comunitario ? $persona->lider_comunitario->nombre . " " . $persona->lider_comunitario->apellido . " " . $persona->lider_comunitario->cedula : 'No disponible',
                'calle' => $persona->direccion->calle,
                'manzana' => $persona->direccion->manzana,
                'numero_de_casa' => $persona->direccion->numero_de_casa,
            ];
            
            
    
            // Si la dirección no existe, crearla
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
    
            // Actualización de los datos
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
            
            // Obtención de los nombres de las entidades relacionadas (parroquia, urbanización, sector, comunidad)
            $parroquiaNombre = Parroquia::find($request->input('parroquia'))->nombre ?? 'No disponible';
            $urbanizacionNombre = Urbanizacion::find($request->input('urbanizacion'))->nombre ?? 'No disponible';
            $sectorNombre = Sector::find($request->input('sector'))->nombre ?? 'No disponible';
            $comunidadNombre = Comunidad::find($request->input('comunidad'))->nombre ?? 'No disponible';
            
            // Actualización de los campos relacionados con la dirección, guardando los nombres
            if ($persona->direccion->parroquia->nombre !== $parroquiaNombre) {
                $camposModificados['parroquia'] = $parroquiaNombre;
                $persona->direccion->parroquia = Parroquia::find($request->input('parroquia'));
            }
            
            if ($persona->direccion->urbanizacion->nombre !== $urbanizacionNombre) {
                $camposModificados['urbanizacion'] = $urbanizacionNombre;
                $persona->direccion->urbanizacion = Urbanizacion::find($request->input('urbanizacion'));
            }
            
            if ($persona->direccion->sector->nombre !== $sectorNombre) {
                $camposModificados['sector'] = $sectorNombre;
                $persona->direccion->sector = Sector::find($request->input('sector'));
            }
            
            if ($persona->direccion->comunidad->nombre !== $comunidadNombre) {
                $camposModificados['comunidad'] = $comunidadNombre;
                $persona->direccion->comunidad = Comunidad::find($request->input('comunidad'));
            }
            
            if ($persona->direccion->calle !== $request->input('calle')) {
                $camposModificados['calle'] = $request->input('calle');
                $persona->direccion->calle = $request->input('calle');
            }
            
            if ($persona->direccion->manzana !== $request->input('manzana')) {
                $camposModificados['manzana'] = $request->input('manzana');
                $persona->direccion->manzana = $request->input('manzana');
            }
            
            if ($persona->direccion->numero_de_casa != $request->input('num_casa')) {
                $camposModificados['numero_de_casa'] = $request->input('num_casa');
                $persona->direccion->numero_de_casa = $request->input('num_casa');
            }
            
            // Si el líder comunitario cambió, actualizarlo
            $lider_comunitario_id = $request->input('lider_comunitario');
            if ($persona->id_lider != $lider_comunitario_id) {
                $camposModificados['lider'] = $lider_comunitario_id;
                $lider = Lider_Comunitario::find($lider_comunitario_id);
                if ($lider) {
                    // Ahora almacenamos el nombre, apellido y cédula del líder, no su id
                    $camposModificados['lider'] = $lider->nombre . " " . $lider->apellido . " " . $lider->cedula;
                    $persona->id_lider = $lider->id_lider;
                } else {
                    // Si no se encuentra el líder, asignamos null
                    $persona->id_lider = null;
                }
            }
            
            $persona->id_usuario = Auth::user()->id_usuario;
            $persona->id_direccion = $direccion->id_direccion;
            $persona->save();
            
            // Registrar movimiento
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
