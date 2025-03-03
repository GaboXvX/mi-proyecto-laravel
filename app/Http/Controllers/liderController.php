<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeLiderRequest;
use App\Http\Requests\updateLiderRequest;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class liderController extends Controller
{
    public function index(){
       
        $lideres = persona::where('es_lider',1)->get();


       
            return view('lideres.listalideres',compact('lideres'));
        
    }
public function create(){
    return view('lideres.registrarlideres');
}
public function show($slug){
    $lider= lider_comunitario::where('slug', $slug)->firstOrFail();
    if($lider){
    return view('lideres.lider', compact('lider'));
}
else{
    return redirect()->route('lideres.index');
}
}



public function buscar(Request $request)
{
    $cedula = $request->input('buscar');
    $lider= lider_comunitario::where('cedula', $cedula)->first();
    if (!$lider) {
        return view('lideres.listalideres')->with('lider', null);
    }
    return view('lideres.listalideres')->with('lideres', [$lider]);
}

}  
