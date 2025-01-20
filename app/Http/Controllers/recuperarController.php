<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class recuperarController extends Controller
{
    public function index(){
        return view('recuperar.recuperarClave');
    }
}
