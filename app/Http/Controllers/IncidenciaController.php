<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\incidencia;
use App\Models\lider_comunitario;
use App\Models\Persona;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        $incidencias = incidencia::orderBy('id_incidencia', 'desc')->get();;
        return view('incidencias.listaincidencias', compact('incidencias'));
    }
    public function create($slug)
    {

        $persona = Persona::where('slug', $slug)->first();


        $lider = null;
        if (!$persona) {
            $lider = lider_comunitario::where('slug', $slug)->first();
            return view('incidencias.registrarIncidencialider', compact('lider'));
        }

        if ($persona) {
            return view('incidencias.registrarIncidencia', compact('persona'));
        }
    }



    public function store(StoreIncidenciaRequest $request)
    {
        try {
            $incidencia = new Incidencia;


            $slug = Str::slug($request->input('descripcion'));
            $originalSlug = $slug;
            $counter = 1;
            while (Incidencia::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $incidencia->slug = $slug;

            $id_lider = $request->input('id_lider');
            $id_persona = $request->input('id_persona');
            $incidencia->id_persona = $id_persona;
            $incidencia->id_lider = $id_lider;
            $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
            $incidencia->descripcion = $request->input('descripcion');
            $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
            $incidencia->estado = $request->input('estado');

            $incidencia->save();


            if ($id_lider) {
                $lider = lider_comunitario::findOrFail($id_lider);
                return redirect()->route('incidencias.show', [
                    'persona_slug' => $lider->slug,
                    'incidencia_slug' => $incidencia->slug
                ])->with('success', 'Incidencia registrada correctamente.');
            }


            if ($id_persona) {
                $persona = Persona::findOrFail($id_persona);
                return redirect()->route('incidencias.show', [
                    'persona_slug' => $persona->slug,
                    'incidencia_slug' => $incidencia->slug
                ])->with('success', 'Incidencia registrada correctamente.');
            }

            return redirect()->route('incidencias.index')->with('error', 'No se pudo registrar la incidencia. Faltan datos.');
        } catch (\Exception $e) {
            return redirect()->route('personas.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
        }
    }





    public function edit($slug, $persona_slug = null)
    {

        $incidencia = Incidencia::where('slug', $slug)->firstOrFail();


        $persona = null;


        if ($persona_slug) {

            $persona = Persona::where('slug', $persona_slug)->first();
        }


        if (!$persona) {
            return view('incidencias.modificarincidencialider', compact('incidencia'));
        }


        return view('incidencias.modificarincidencia', compact('incidencia', 'persona'));
    }

    public function update(Request $request, $id)
    {
        try {

            $incidencia = Incidencia::findOrFail($id);

            $validated = $request->validate([
                'tipo_incidencia' => 'required|string|max:255',
                'descripcion' => 'required|string|max:1000',
                'nivel_prioridad' => 'required|string|max:255',
                'estado' => 'required|string|max:255',
                'id_persona' => 'nullable|exists:personas,id_persona',
                'id_lider' => 'nullable|exists:lider_comunitario,id_lider',
            ]);

            if ($incidencia->descripcion !== $request->input('descripcion')) {
                $slug = Str::slug($request->input('descripcion'));
                $originalSlug = $slug;
                $counter = 1;
                while (Incidencia::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $incidencia->slug = $slug;
            }


            $incidencia->id_persona = $request->input('id_persona');
            $incidencia->id_lider = $request->input('id_lider');
            $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
            $incidencia->descripcion = $request->input('descripcion');
            $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
            $incidencia->estado = $request->input('estado');

            $incidencia->save();

            if ($incidencia->id_lider) {
                return redirect()->route('lideres.index')->with('success', 'Incidencia registrada correctamente.');
            } else {
                return redirect()->route('personas.index')->with('success', 'Incidencia registrada correctamente.');
            }
        } catch (\Exception $e) {

            return redirect()->route('personas.index')->with('error', 'Error al actualizar la incidencia: ' . $e->getMessage());
        }
    }


    public function destroy() {}
    public function atender($slug)
    {
        $incidencia = incidencia::where('slug', $slug)->first();
        $incidencia->estado = 'atendido';
        $incidencia->save();
        return redirect()->route('incidencias.index')->with('success', 'marcado como atendido');
    }
    public function filtrar(Request $request)
    {

        $validated = $request->validate([
            'fecha' => 'required|date',
        ]);


        $fecha = $request->input('fecha');


        $incidencias = Incidencia::whereDate('created_at', '=', $fecha)->get();


        return response()->json([
            'incidencias' => $incidencias
        ]);
    }
    public function mostrar($slug)
    {
        $persona = Persona::where('slug', $slug)->first();

        if ($persona) {
            $incidencia = Incidencia::where('id_persona', $persona->id)->get();

            return view('incidencias.modificarincidencia', compact('incidencia', 'persona'));
        }

        $lider = Lider_Comunitario::where('slug', $slug)->first();

        if ($lider) {

            $incidencia = Incidencia::where('id_lider', $lider->id)->get();

            return view('incidencias.modificarincidencialider', compact('incidencia', 'lider'));
        }

        return redirect()->route('home')->with('error', 'No se encontraron incidencias para este usuario o líder.');
    }


    public function show($persona_slug, $incidencia_slug)
    {

        $incidencia = Incidencia::where('slug', $incidencia_slug)->firstOrFail();


        $persona = Persona::where('slug', $persona_slug)->first();
        $lider = lider_comunitario::where('slug', $persona_slug)->first();


        if ($persona) {

            if ($incidencia->id_persona !== $persona->id_persona) {
                abort(404, 'Incidencia no encontrada para esta persona.');
            }

            return view('incidencias.incidencia', compact('incidencia', 'persona'));
        } elseif ($lider) {

            if ($incidencia->id_lider !== $lider->id_lider) {
                abort(404, 'Incidencia no encontrada para este líder.');
            }

            return view('incidencias.incidencia', compact('incidencia', 'lider'));
        } else {
            abort(404, 'Persona o líder no encontrado.');
        }
    }


    public function download($slug)
    {
        $incidencia = Incidencia::where('slug', $slug)->first();

        $pdf = FacadePdf::loadView('incidencias.incidencia', compact('incidencia'));

        return $pdf->download('incidencia-' . $incidencia->slug . '.pdf');
    }
    public function gestionar()
    {
        $incidencias = incidencia::orderBy('id_incidencia', 'desc')->get();;
        return view('incidencias.gestionincidencias', compact('incidencias'));
    }

    public function showChart(Request $request)
{
    $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfYear())); 
    $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth())); 

    $tipoIncidencia = $request->input('tipo_incidencia', ''); 

    // Consultas para incidencias atendidas y por atender
    $queryAtendidas = Incidencia::where('estado', 'Atendido')
                                ->whereBetween('created_at', [$startDate, $endDate]);
    $queryPorAtender = Incidencia::where('estado', 'por atender')
                                 ->whereBetween('created_at', [$startDate, $endDate]);

    if ($tipoIncidencia) {
        $queryAtendidas->where('tipo_incidencia', $tipoIncidencia);
        $queryPorAtender->where('tipo_incidencia', $tipoIncidencia);
    }

    // Obtener datos de incidencias atendidas
    $incidenciasAtendidas = $queryAtendidas->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
                                          ->groupBy('year', 'month')
                                          ->orderBy('year')
                                          ->orderBy('month')
                                          ->get();
    
    // Obtener datos de incidencias por atender
    $incidenciasPorAtender = $queryPorAtender->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
                                              ->groupBy('year', 'month')
                                              ->orderBy('year')
                                              ->orderBy('month')
                                              ->get();

    // Preparar datos para la vista
    $labels = [];
    $dataAtendidas = [];
    $dataPorAtender = [];

    // Recorremos las incidencias atendidas
    foreach ($incidenciasAtendidas as $incidencia) {
        $monthName = Carbon::createFromFormat('m', $incidencia->month)->format('F');
        $labels[] = $monthName . ' ' . $incidencia->year;
        $dataAtendidas[] = $incidencia->total;
    }

    // Recorremos las incidencias por atender
    foreach ($incidenciasPorAtender as $incidencia) {
        $monthName = Carbon::createFromFormat('m', $incidencia->month)->format('F');
        if (!in_array($monthName . ' ' . $incidencia->year, $labels)) {
            $labels[] = $monthName . ' ' . $incidencia->year;
        }
        $dataPorAtender[] = $incidencia->total;
    }

    return view('incidencias.grafica_incidencia_resueltas', compact('labels', 'dataAtendidas', 'dataPorAtender', 'startDate', 'endDate', 'tipoIncidencia'));
}

    
    
}    

