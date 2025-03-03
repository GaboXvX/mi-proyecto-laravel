<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        return view('login');
    }
    public function showLoginForm()
    {
        if (Auth::check()) {
                return redirect()->route('home')->with('success', 'Inicio de sesión exitoso');
        }
        return view('login');
    }

    public function authenticate(Request $request)
{
    $estado = User::where('email', $request->input('email'))->first();
    if ($estado && $estado->estado == 'desactivado') {
        return redirect()->route('login')->with('error', 'Este usuario se encuentra desactivado');
    }

    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('home')->with('success', 'Inicio de sesión exitoso');
    }

    return redirect()->back()->withErrors([
        'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    ]);
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente');
    }
}
