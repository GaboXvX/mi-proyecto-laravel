<?php

namespace App\Http\Controllers;

use App\Models\movimiento;
use Illuminate\Http\Request;

class movimientoController extends Controller
{
    public function index(){
        $movimientos=movimiento::orderBy('id_movimiento','desc')->paginate(10);
      
        return view('movimientos.movimientos',compact('movimientos'));
    }
}
