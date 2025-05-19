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
            'nivel' => 'required|integer|unique:niveles_incidencias,nivel',
            'nombre' => 'required|string|max:30',
            'descripcion' => 'required|string|max:200',
            'horas_vencimiento' => 'required|integer|min:1',
            'frecuencia_recordatorio' => 'required|integer|min:1',
            'color' => 'required|string|max:7',
        ]);

        nivelIncidencia::create($request->all());

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
            'nivel' => 'required|integer|unique:niveles_incidencias,nivel,'.$nivelIncidencia->id_nivel_incidencia.',id_nivel_incidencia',
            'nombre' => 'required|string|max:30',
            'descripcion' => 'required|string|max:200',
            'horas_vencimiento' => 'required|integer|min:1',
            'color' => 'required|string|max:7',
        ]);

        $nivelIncidencia->update($request->all());

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
}