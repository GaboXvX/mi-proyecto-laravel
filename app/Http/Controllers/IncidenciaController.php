<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\Direccion;
use App\Models\incidencia;
use App\Models\lider_comunitario;
use App\Models\movimiento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        $incidencias = incidencia::orderBy('id_incidencia', 'desc')->get();;
        return view('incidencias.listaincidencias', compact('incidencias'));
    }
   
    public function crear($slug)
    {

        $persona = Persona::where('slug', $slug)->first();

       
            return view('incidencias.registrarIncidencia', compact('persona'));
    
    }
    public function create($slug)
    {

        $persona = Persona::where('slug', $slug)->first();

    $lider = lider_comunitario::where('slug', $slug)->first();
       
        if ($lider) {
          
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

       $codigo= Str::random(8);;
       while(Incidencia::where('cod_incidencia',$codigo)->exists()){
        $codigo= Str::random(8);
       }
       $incidencia->slug = $slug;

        $id_lider = $request->input('id_lider');
        $id_persona = $request->input('id_persona');
        if ($id_lider) {
            $incidencia->id_lider = $id_lider;
            $incidencia->id_persona = null; 
             $lider=lider_comunitario::where('id_lider',$id_lider)->first();
             $incidencia->cod_incidencia= $codigo;
        } elseif ($id_persona) {
            $incidencia->id_persona = $id_persona;
            $incidencia->id_lider = null; 
            $persona=Persona::where('id_persona',$id_persona)->first();
            $incidencia->cod_incidencia= $incidencia->cod_incidencia= $codigo;
            
        } else {
           
            return redirect()->route('incidencias.index')->with('error', 'No se pudo registrar la incidencia. Faltan datos de líder o persona.');
        }


        $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
        $incidencia->descripcion = $request->input('descripcion');
        $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
        $incidencia->estado = $request->input('estado');

       
        $incidencia->save();

        
        $camposCreado = [
            'tipo_de_incidencia' => $incidencia->tipo_incidencia,
            'descripcion' => $incidencia->descripcion,
            'nivel_de_prioridad' => $incidencia->nivel_prioridad,
            'estado' => $incidencia->estado,
        ];

        $movimiento = new Movimiento();
        $movimiento->id_incidencia = $incidencia->id_incidencia;
        $movimiento->id_usuario = Auth::user()->id_usuario;
        $movimiento->id_persona = $incidencia->id_persona;
        $movimiento->id_lider = $incidencia->id_lider;
        $movimiento->accion = 'se ha creado un registro';
        $movimiento->valor_anterior = json_encode($camposCreado);
        $movimiento->save();

       
        if ($id_lider) {
            $lider = Lider_Comunitario::findOrFail($id_lider);
            return redirect()->route('incidencias.show', [
                'slug' => $lider->slug,
                'incidencia_slug' => $incidencia->slug
            ])->with('success', 'Incidencia registrada correctamente.');
        }

        if ($id_persona) {
            $persona = Persona::findOrFail($id_persona);
            return redirect()->route('incidencias.show', [
                'slug' => $persona->slug,
                'incidencia_slug' => $incidencia->slug
            ])->with('success', 'Incidencia registrada correctamente.');
        }

        return redirect()->route('incidencias.index')->with('error', 'No se pudo registrar la incidencia. Faltan datos.');

    } catch (\Exception $e) {
        return redirect()->route('personas.index')->with('error', 'Error al enviar los datos: ' . $e->getMessage());
    }
}


    public function descargar($slug)
    {
        $incidencia = Incidencia::where('slug', $slug)->first();

        $pdf = FacadePdf::loadView('incidencias.incidencia', compact('incidencia'));

        return $pdf->download('incidencia-' . $incidencia->slug . '.pdf');
    }


    public function gestionar(Request $request)
    {
        $incidencias = incidencia::orderBy('id_incidencia', 'desc')->get();
        return view('incidencias.gestionincidencias', compact('incidencias'));
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
    
            
            $camposAntiguos = [
                'tipo_de_incidencia' => $incidencia->tipo_incidencia,
                'descripcion' => $incidencia->descripcion,
                'nivel_de_prioridad' => $incidencia->nivel_prioridad,
                'estado' => $incidencia->estado,
                'id_persona' => $incidencia->id_persona,
                'id_lider' => $incidencia->id_lider,
            ];
    
            
            $camposModificados = [];
            if ($incidencia->tipo_incidencia !== $request->input('tipo_incidencia')) {
                $camposModificados['tipo_de_incidencia'] = $request->input('tipo_incidencia');
                $incidencia->tipo_incidencia = $request->input('tipo_incidencia');
            }
    
            if ($incidencia->descripcion !== $request->input('descripcion')) {
                $camposModificados['descripcion'] = $request->input('descripcion');
                $incidencia->descripcion = $request->input('descripcion');
            }
    
            if ($incidencia->nivel_prioridad != $request->input('nivel_prioridad')) {
                $camposModificados['nivel_de_prioridad'] = $request->input('nivel_prioridad');
                $incidencia->nivel_prioridad = $request->input('nivel_prioridad');
            }
    
            if ($incidencia->estado !== $request->input('estado')) {
                $camposModificados['estado'] = $request->input('estado');
                $incidencia->estado = $request->input('estado');
            }
    
           
            $incidencia->save();
    
            
            if (!empty($camposModificados)) {
                $movimiento = new Movimiento();
    
                if (Auth::check()) {
                    $movimiento->id_usuario = Auth::user()->id_usuario;
                } else {
                    return redirect()->route('login')->with('error', 'Debe estar autenticado para realizar esta acción.');
                }
    
                
                $movimiento->id_lider = $incidencia->id_lider;
                $movimiento->id_incidencia = $incidencia->id_incidencia;
                $movimiento->accion = 'se ha actualizado un registro';
                $movimiento->valor_nuevo = json_encode($camposModificados);  // Los valores actuales después de la actualización
                $movimiento->valor_anterior = json_encode($camposAntiguos);  // Los valores antes de la actualización
                $movimiento->id_persona = $incidencia->id_persona;
                $movimiento->save();
    
                if ($movimiento->save()) {
                    if ($incidencia->id_lider) {
                        return redirect()->route('lideres.index')->with('success', 'Incidencia actualizada correctamente.');
                    } else {
                        return redirect()->route('personas.index')->with('success', 'Incidencia actualizada correctamente.');
                    }
                } else {
                    return redirect()->route('personas.index')->with('error', 'Error al registrar el movimiento.');
                }
            } else {
                
                if ($incidencia->id_lider) {
                    return redirect()->route('lideres.index')->with('success', 'Incidencia actualizada sin cambios.');
                } else {
                    return redirect()->route('personas.index')->with('success', 'Incidencia actualizada sin cambios.');
                }
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
        return redirect()->route('incidencias.gestionar')->with('success', 'marcado como atendido');
    }
    public function filtrar(Request $request)
{
    
    $validated = $request->validate([
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date',
    ]);

  
    $fechaInicio = $request->input('fecha_inicio');
    $fechaFin = $request->input('fecha_fin');

    
    $incidencias = Incidencia::whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->get();

 
    return response()->json([
        'incidencias' => $incidencias
    ]);
}


public function show($slug, $incidencia_slug)
{
    

    $incidencia = Incidencia::where('slug', $incidencia_slug)->first();


    if (!$incidencia) {
        abort(404, 'Incidencia no encontrada');
    }


    $lider = lider_comunitario::where('slug', $slug)->first();

    $persona = Persona::where('slug', $slug)->first();
    if($lider && $persona){
        return redirect()->route('personas.index')->with('error', 'algo salio mal');
    }else{
   
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
    }}
}



    public function download(Request $request)
{
   
    $validated = $request->validate([
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date',
    ]);

    
    $fechaInicio = $request->input('fecha_inicio');
    $fechaFin = $request->input('fecha_fin');

   
    $incidencias = Incidencia::whereBetween('created_at', [$fechaInicio, $fechaFin])->get();

    if ($incidencias->isEmpty()) {
        return response()->json(['message' => 'No se encontraron incidencias en este periodo.'], 404);
    }

    $pdf = FacadePdf::loadView('incidencias.listaincidencias', compact('incidencias', 'fechaInicio', 'fechaFin'));

    return $pdf->download('incidencias-' . $fechaInicio . '_a_' . $fechaFin . '.pdf');
}

   public function showChart(Request $request)
{
    $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfYear()));
    $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()));

    $tipoIncidencia = $request->input('tipo_incidencia', '');

    
    $queryAtendidas = Incidencia::where('estado', 'Atendido')
        ->whereBetween('created_at', [$startDate, $endDate]);

    if ($tipoIncidencia) {
        $queryAtendidas->where('tipo_incidencia', $tipoIncidencia);
    }

 
    $incidenciasAtendidas = $queryAtendidas->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

    
    $labels = [];
    $dataAtendidas = [];

    
    foreach ($incidenciasAtendidas as $incidencia) {
        $monthName = Carbon::createFromFormat('m', $incidencia->month)->format('F');
        $labels[] = $monthName . ' ' . $incidencia->year;  
        $dataAtendidas[] = $incidencia->total;  
    }

    
    return view('incidencias.grafica_incidencia_resueltas', compact('labels', 'dataAtendidas', 'startDate', 'endDate', 'tipoIncidencia'));
}
public function buscar(Request $request){
    $codigo=$request->input('buscar');
  $incidencia=incidencia::where('cod_incidencia',$codigo)->first();  
    if(url()->previous()==route('incidencias.gestionar')){
        return view('incidencias.gestionincidencias')->with('incidencias',[$incidencia]);
    }

return view('incidencias.listaincidencias')->with('incidencias',[$incidencia]);
}
}
