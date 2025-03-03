<?php

namespace App\Http\Controllers;

use App\Http\Requests\storePeticionRequest;
use Illuminate\Support\Str;
use App\Models\peticion;
use App\Models\pregunta;
use App\Models\User;

class peticionController extends Controller
{
    public function index()
    {
        $peticiones = peticion::orderBy('id_peticion', 'desc')->get();
        return view('peticiones.listapeticiones', compact('peticiones'));
    }
    public function store(storePeticionRequest $request)
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
                $preguntaSeguridad = $peticion->preguntas_de_seguridad()->first();


                if ($preguntaSeguridad) {

                    $preguntaSeguridad->primera_mascota = $request->input('mascota');
                    $preguntaSeguridad->ciudad_de_nacimiento = $request->input('ciudad');
                    $preguntaSeguridad->nombre_de_mejor_amigo = $request->input('amigo');


                    $preguntaSeguridad->save();
                }
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

            $pregunta = new pregunta();
            $peticion = new Peticion();
            $peticion->id_rol = $request->input('rol');
            $peticion->estado_peticion = 'No verificado';
            $peticion->nombre = $request->input('nombre');
            $peticion->apellido = $request->input('apellido');
            $peticion->cedula = $request->input('cedula');
            $peticion->email = $request->input('email');
            $peticion->nombre_usuario = $request->input('nombre_usuario');
            $peticion->password = bcrypt($request->input('password'));
            $pregunta->primera_mascota = $request->input('mascota');
            $pregunta->ciudad_de_nacimiento = $request->input('ciudad');
            $pregunta->nombre_de_mejor_amigo = $request->input('amigo');


            $slug = Str::slug($request->input('nombre'));
            $counter = 1;
            $originalSlug = $slug;
            while (Peticion::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $peticion->slug = $slug;
            $pregunta->save();
            $peticion->id_pregunta = $pregunta->id_pregunta;
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
    public function aceptar($id)
    {


        try {
            $peticion = Peticion::where('id_peticion', $id)->first();

            if (!$peticion) {
                return redirect()->route('usuarios.create')->with('error', 'Petición no encontrada');
            }
            $peticion->estado_peticion = 'aceptado';
            $peticion->save();
            $usuario = new User();
            $usuario->id_peticion = $peticion->id_peticion;
            $usuario->id_rol = $peticion->id_rol;
            $usuario->id_pregunta = $peticion->id_pregunta;
            $usuario->slug = $peticion->slug;
            $usuario->estado_peticion = $peticion->estado_peticion;
            $usuario->nombre = $peticion->nombre;
            $usuario->cedula = $peticion->cedula;
            $usuario->apellido = $peticion->apellido;
            $usuario->email = $peticion->email;
            $usuario->nombre_usuario = $peticion->nombre_usuario;
            $usuario->password = $peticion->password;
            $usuario->created_at = now();
            $usuario->updated_at = now();
            $usuario->save();

            return redirect()->route('peticiones.index')->with('success', 'Datos enviados correctamente');
        } catch (\Exception $e) {
            return redirect()->route('peticiones.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
        }
    }
}
