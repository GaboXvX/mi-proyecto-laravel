<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecuperarGetController extends Controller
{
    public function redirigirRecuperarClave(Request $request)
    {
        return redirect()->route('recuperar.recuperarClave', [
            'usuarioId' => $request->usuario_id,
            'preguntaId' => $request->pregunta_id
        ]);
    }
}
