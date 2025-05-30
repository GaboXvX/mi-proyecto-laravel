<?php

namespace App\Http\Controllers;

use App\Models\EmpleadoAutorizado;
use App\Models\User;
use App\Models\Cargo;
use Illuminate\Http\Request;

class EmpleadoAutorizadoController extends Controller
{
    public function index()
    {
        // Traer todos los empleados autorizados con su usuario (si existe) y su cargo
        $empleados = EmpleadoAutorizado::with(['usuario', 'cargo'])->get();
        return view('empleados.listaEmpleados', compact('empleados'));
    }

    public function create()
    {
        $cargos = Cargo::all();
        return view('empleados.crearEmpleado', compact('cargos'));
    }

    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:empleados_autorizados,cedula',
            'cargo_id' => 'required|exists:cargos_empleados_autorizados,id_cargo',
            'genero' => 'required|in:M,F',
            'telefono' => 'required|string|max:20' // Asegúrate que el teléfono sea string
        ]);

        $empleado = new EmpleadoAutorizado();
        $empleado->nombre = $request->nombre;
        $empleado->apellido = $request->apellido;
        $empleado->cedula = $request->cedula;
        $empleado->id_cargo = $request->cargo_id;
        $empleado->genero = $request->genero;
        $empleado->telefono = $request->telefono;
        $empleado->save();

        return response()->json([
            'success' => true,
            'message' => 'Empleado registrado correctamente',
            'redirect_url' => route('usuarios.index') // Cambié usuarios.index por empleados.index para consistencia
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error inesperado: ' . $e->getMessage()
        ], 500);
    }
}

    public function show($id)
    {
        $empleado = EmpleadoAutorizado::with(['usuario', 'cargo'])->findOrFail($id);
        return view('empleados.verEmpleado', compact('empleado'));
    }

    public function edit($id)
    {
        $empleado = EmpleadoAutorizado::findOrFail($id);
        $cargos = Cargo::all();
        return view('empleados.editarEmpleado', compact('empleado', 'cargos'));
    }

    public function update(Request $request, $id)
    {
        $empleado = EmpleadoAutorizado::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|max:20|unique:empleados_autorizados,cedula,' . $empleado->id_empleado_autorizado . ',id_empleado_autorizado',
            'cargo_id' => 'required|exists:cargos,id_cargo',
        ]);
        $empleado->nombre = $request->nombre;
        $empleado->apellido = $request->apellido;
        $empleado->cedula = $request->cedula;
        $empleado->id_cargo = $request->cargo_id;
        $empleado->save();
        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente');
    }

    public function destroy($id)
    {
        $empleado = EmpleadoAutorizado::findOrFail($id);
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente');
    }

    public function verificarCedula(Request $request)
    {
        $cedula = $request->input('cedula');
        $empleado = EmpleadoAutorizado::where('cedula', $cedula)->first();
        if ($empleado) {
            return response()->json([
                'existe' => true,
                'empleado' => [
                    'nombre' => $empleado->nombre,
                    'apellido' => $empleado->apellido,
                    'genero' => $empleado->genero,
                    'telefono' => $empleado->telefono,
                    'id_cargo' => $empleado->id_cargo
                ]
            ]);
        } else {
            return response()->json(['existe' => false]);
        }
    }
}
