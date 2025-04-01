<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    // ...existing code...

    public function validarUsuario($nombre_usuario, Request $request)
    {
        $excluir = $request->query('excluir');
        $existe = \App\Models\User::where('nombre_usuario', $nombre_usuario)
            ->when($excluir, function ($query, $excluir) {
                return $query->where('id_usuario', '!=', $excluir);
            })
            ->exists();
        return response()->json(['disponible' => !$existe]);
    }

    public function validarCorreo($email, Request $request)
    {
        $excluir = $request->query('excluir');
        $existe = \App\Models\User::where('email', $email)
            ->when($excluir, function ($query, $excluir) {
                return $query->where('id_usuario', '!=', $excluir);
            })
            ->exists();
        return response()->json(['disponible' => !$existe]);
    }

    // ...existing code...
}