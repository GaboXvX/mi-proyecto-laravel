<?php

namespace App\Http\Controllers;

use App\Models\empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados=empleado::orderBy('id_empleado','desc')->get();
        return view('empleados.listaEmpleados',compact('empleados'));
    }

    public function create()
    {
        return view('empleados.registrarempleados');
    }

    public function store(Request $request)
    {
        try{
       empleado::create($request->all());
       return redirect()->route('empleados.create')->with('success', 'Datos enviados correctamente');
    } catch (\Exception $e) {
        return redirect()->route('personas.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
    }
    }

    public function edit($slug)
    {
        $empleado=empleado::where('slug',$slug)->firstOrfail();
        return view('empleados.modificarEmpleados',compact('empleado'));
    }

    public function update(Request $request, $slug)
    {
        try{$empleado=empleado::where('slug',$slug)->firstOrfail();
            $empleado->nombre=$request->input('nombre');
            $empleado->apellido=$request->input('apellido');
            $empleado->cedula=$request->input('cedula');
            $empleado->correo=$request->input('correo');
            $empleado->save(); 
            return redirect()->route('empleados.index')->with('success', 'Datos actualizados correctamente');
        }
        catch(\Exception $e){
            return redirect()->route('empleados.index')->with('error', 'Error al actualizar los datos: ' . $e->getMessage());
        }
        

    
    }

    public function destroy($id)
    {
        
    }
}


