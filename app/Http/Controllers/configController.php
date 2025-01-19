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
    public function restaurar( $id_usuario){
       try{
        $usuario=User::where('id_usuario',$id_usuario)->first();
        $usuario->password=bcrypt('12345678');
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'usuario restaurado correctamente');
    }catch (\Exception $e) {
        return redirect()->route('usuarios.index')->with('error', 'Error al restaurar el usuario: ' . $e->getMessage());
    }
    }
}
