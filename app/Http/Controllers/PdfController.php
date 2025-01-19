<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade as PDF;  
use App\Models\Incidencia;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function generar(Request $request)
    {
        
        $fecha = $request->input('fecha');
    
        
        $incidencias = Incidencia::whereDate('created_at', $fecha)->get();
    
        
        if ($incidencias->isEmpty()) {
            $incidencias=Incidencia::all();
        }
    
       
        $pdf = FacadePdf::loadView('incidencias.listaincidencias', compact('incidencias'));
    
      
        return $pdf->download('listaIncidencias.pdf');
    }
    
}
