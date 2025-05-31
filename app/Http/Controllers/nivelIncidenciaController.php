<?php

namespace App\Http\Controllers;

use App\Models\nivelIncidencia;
use Illuminate\Http\Request;

class nivelIncidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $niveles = nivelIncidencia::orderBy('nivel')->get();
        return view('nivelesIncidencias.nivelesIncidencias', compact('niveles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('nivelesincidencias.agregarNivel');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required','string','max:30','regex:/^[^\s]+$/'],
            'descripcion' => ['required','string','max:200','regex:/^(\w+)( \w+)*$/u'],
            'horas_vencimiento' => 'required|integer|min:0',
            'dias' => 'nullable|integer|min:0',
            'color' => 'required|string|max:7',
        ], [
            'nombre.regex' => 'El nombre debe ser una sola palabra, sin espacios.',
            'descripcion.regex' => 'La descripción debe ser palabras separadas por un solo espacio.',
        ]);

        // Sumar días y horas para el campo horas_vencimiento
        $dias = (int) $request->input('dias', 0);
        $horas = (int) $request->input('horas_vencimiento', 0);
        $totalHoras = ($dias * 24) + $horas;
        if ($totalHoras < 1) {
            return back()->withErrors(['horas_vencimiento' => 'El tiempo total debe ser al menos 1 hora.'])->withInput();
        }

        // Validar que el color no esté repetido ni sea similar (por matiz)
        $color = strtolower($request->input('color'));
        $hueNuevo = $this->hexToHue($color);
        $umbral = 18; // grados de diferencia de matiz para considerar "similar"
        $coloresExistentes = nivelIncidencia::all()->pluck('color');
        foreach ($coloresExistentes as $colorExistente) {
            $hueExistente = $this->hexToHue(strtolower($colorExistente));
            if (abs($hueNuevo - $hueExistente) < $umbral || abs($hueNuevo - $hueExistente) > (360 - $umbral)) {
                return back()->withErrors(['color' => 'El color seleccionado es muy similar a uno ya asignado a otro nivel.'])->withInput();
            }
        }

        // Validar que el nombre no se parezca a otros (usando la función mejorada del modelo)
        if (nivelIncidencia::nombreEsSimilar($request->input('nombre'))) {
            return back()->withErrors(['nombre' => 'El nombre es igual o muy similar a otro nivel existente.'])->withInput();
        }

        // Asignar nivel autoincremental
        $ultimoNivel = nivelIncidencia::max('nivel');
        $nuevoNivel = $ultimoNivel ? $ultimoNivel + 1 : 1;
        if ($nuevoNivel > 10) {
            return back()->withErrors(['nivel' => 'No se pueden crear más de 10 niveles.'])->withInput();
        }
        $data = $request->except(['dias']);
        $data['nivel'] = $nuevoNivel;
        $data['horas_vencimiento'] = $totalHoras;
        nivelIncidencia::create($data);

        return redirect()->route('niveles-incidencia.index')
            ->with('success', 'Nivel de incidencia creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(nivelIncidencia $nivelIncidencia)
    {
        return view('niveles_incidencia.show', compact('nivelIncidencia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(nivelIncidencia $nivelIncidencia)
    {
        return view('nivelesIncidencias.editarNiveles', compact('nivelIncidencia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, nivelIncidencia $nivelIncidencia)
    {
        $request->validate([
            // 'nivel' no se puede editar
            'nombre' => ['required','string','max:30','regex:/^[^\s]+$/'],
            'descripcion' => ['required','string','max:200','regex:/^(\w+)( \w+)*$/u'],
            'horas_vencimiento' => 'required|integer|min:0',
            'dias' => 'nullable|integer|min:0',
            'color' => 'required|string|max:7',
        ], [
            'nombre.regex' => 'El nombre debe ser una sola palabra, sin espacios.',
            'descripcion.regex' => 'La descripción debe ser palabras separadas por un solo espacio.',
        ]);

        // Sumar días y horas para el campo horas_vencimiento
        $dias = (int) $request->input('dias', 0);
        $horas = (int) $request->input('horas_vencimiento', 0);
        $totalHoras = ($dias * 24) + $horas;
        if ($totalHoras < 1) {
            return back()->withErrors(['horas_vencimiento' => 'El tiempo total debe ser al menos 1 hora.'])->withInput();
        }

        // Validar que el color no esté repetido ni sea similar (ignorando el actual)
        $color = strtolower($request->input('color'));
        $hueNuevo = $this->hexToHue($color);
        $umbral = 18;
        $coloresExistentes = nivelIncidencia::where('id_nivel_incidencia', '!=', $nivelIncidencia->id_nivel_incidencia)->pluck('color');
        foreach ($coloresExistentes as $colorExistente) {
            $hueExistente = $this->hexToHue(strtolower($colorExistente));
            if (abs($hueNuevo - $hueExistente) < $umbral || abs($hueNuevo - $hueExistente) > (360 - $umbral)) {
                return back()->withErrors(['color' => 'El color seleccionado es muy similar a uno ya asignado a otro nivel.'])->withInput();
            }
        }

        // Validar que el nombre no se parezca a otros (usando la función mejorada del modelo, ignorando el actual)
        if (nivelIncidencia::nombreEsSimilar($request->input('nombre'), $nivelIncidencia->id_nivel_incidencia)) {
            return back()->withErrors(['nombre' => 'El nombre es igual o muy similar a otro nivel existente.'])->withInput();
        }

        $nivelIncidencia->update($request->except('nivel'));

        return redirect()->route('niveles-incidencia.index')
            ->with('success', 'Nivel de incidencia actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(nivelIncidencia $nivelIncidencia)
    {
        // Verificar si hay incidencias asociadas antes de eliminar
        if ($nivelIncidencia->incidencias()->count() > 0) {
            return redirect()->route('niveles-incidencia.index')
                ->with('error', 'No se puede eliminar el nivel porque tiene incidencias asociadas.');
        }

        $nivelIncidencia->delete();

        return redirect()->route('niveles-incidencia.index')
            ->with('success', 'Nivel de incidencia eliminado exitosamente.');
    }

    /**
     * Cambiar el estado activo/inactivo
     */
    public function toggleStatus(nivelIncidencia $nivelIncidencia)
    {
        $nivelIncidencia->activo = !$nivelIncidencia->activo;
        $nivelIncidencia->save();

        return redirect()->route('niveles-incidencia.index')
            ->with('success', 'Estado del nivel actualizado exitosamente.');
    }

    /**
     * Convierte un color HEX a matiz (hue) en HSL
     */
    private function hexToHue($hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex,0,1),2));
            $g = hexdec(str_repeat(substr($hex,1,1),2));
            $b = hexdec(str_repeat(substr($hex,2,1),2));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $r /= 255; $g /= 255; $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = 0;
        if ($max == $min) {
            $h = 0;
        } elseif ($max == $r) {
            $h = (60 * (($g - $b) / ($max - $min)) + 360) % 360;
        } elseif ($max == $g) {
            $h = (60 * (($b - $r) / ($max - $min)) + 120) % 360;
        } elseif ($max == $b) {
            $h = (60 * (($r - $g) / ($max - $min)) + 240) % 360;
        }
        return $h;
    }
}