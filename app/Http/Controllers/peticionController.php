<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\peticion;
use Illuminate\Http\Request;

class peticionController extends Controller
{
    public function index()
    {
        $peticiones = peticion::orderBy('id_peticion', 'desc')->get();
        return view('peticiones.listapeticiones', compact('peticiones'));
    }
    public function store(Request $request)
    {
        try {
         
            $peticion = Peticion::where('cedula', $request->input('cedula'))->first();
    
         
            if ($peticion && $peticion->estado_peticion == 'No verificado') {
                return redirect()->route('login')->with('error', 'Ya existe una petición con esa cédula');
            }
    
            
            if ($peticion && $peticion->estado_peticion == 'aceptado') {
                return redirect()->route('login')->with('error', 'este solicitante ya tiene una peticion aceptada');
            }
            if ($peticion && $peticion->estado_peticion == 'rechazada') {
                $peticion->id_rol = $request->input('rol');
                $peticion->estado_peticion = 'No verificado';
                $peticion->nombre = $request->input('nombre');
                $peticion->apellido = $request->input('apellido');
                $peticion->email = $request->input('email');
                $peticion->nombre_usuario = $request->input('nombre_usuario');
                $peticion->password = bcrypt($request->input('password'));
    
                $slug = Str::slug($request->input('nombre'));
                $counter = 1;
                $originalSlug = $slug;
                while (Peticion::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $peticion->slug = $slug;
    
                $peticion->save();
    
                return redirect()->route('login')->with('success', 'Petición actualizada correctamente');
            }
    
            
            $peticion = new Peticion();
            $peticion->id_rol = $request->input('rol');
            $peticion->estado_peticion = 'No verificado';
            $peticion->nombre = $request->input('nombre');
            $peticion->apellido = $request->input('apellido');
            $peticion->cedula = $request->input('cedula');
            $peticion->email = $request->input('email');
            $peticion->nombre_usuario = $request->input('nombre_usuario');
            $peticion->password = bcrypt($request->input('password'));
            $peticion->estado = 'inactivo';
    
            $slug = Str::slug($request->input('nombre'));
            $counter = 1;
            $originalSlug = $slug;
            while (Peticion::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $peticion->slug = $slug;
    
            $peticion->save();
    
            return redirect()->route('login')->with('success', 'Petición realizada correctamente');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al procesar la petición: ' . $e->getMessage());
        }
    }
    public function rechazar($id)
    {
        $peticion = Peticion::where('id_peticion', $id)->first();

        if (!$peticion) {
            return redirect()->route('peticiones.index')->with('error', 'Petición no encontrada');;
        }

        $peticion->estado_peticion = 'rechazada';
        $peticion->save();
        return redirect()->route('peticiones.index')->with('success', 'Petición rechazada con éxito');
    }
}
