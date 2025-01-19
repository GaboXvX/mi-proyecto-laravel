<?php

namespace App\Http\Controllers;


use App\Models\lider_comunitario;

use Illuminate\Http\Request;
class liderController extends Controller
{
    public function index(){
        $lideres=lider_comunitario::all();
        return view('lideres.listalideres',compact('lideres'));
    }

public function show($slug){
    $lider= lider_comunitario::where('slug', $slug)->firstOrFail();
    
    return view('lideres.lider', compact('lider'));
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