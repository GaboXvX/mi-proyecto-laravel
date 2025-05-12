@extends('layouts.registrar')
<style>
    .alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 10px;
    border-radius: 5px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 10px;
    border-radius: 5px;
}

</style>
@section('content')

    <div class="container">
        <div class="content">
          
        
        
            <h2>Recuperar Contraseña</h2>
            
            <p>Por favor, ingrese su cédula para continuar con la recuperación de su contraseña.</p>
        </div>
        <hr>
        <div class="form-content">
            <!-- Mensajes de error -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario para ingresar la cédula -->
            <form action="{{ route('recuperar.preguntas') }}" method="POST">
                @csrf

                <!-- Cédula -->
                <div class="row">
                    <input type="text" name="cedula" placeholder="Cédula" value="{{ old('cedula') }}" required>
                </div>

                <button type="submit" style="margin-top: 5px;">Continuar</button>
            </form>

            <a href="{{ route('login') }}">Volver al inicio de sesión</a>
        </div>
    </div>
@endsection