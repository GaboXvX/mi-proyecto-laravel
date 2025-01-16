<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class configController extends Controller
{
    public function index(){
        $id_usuario=Auth::user()->id_usuario;
        $usuario=User::where('id_usuario',$id_usuario)->first();
        return view('usuarios.configuracion',compact('usuario'));
    }
}
