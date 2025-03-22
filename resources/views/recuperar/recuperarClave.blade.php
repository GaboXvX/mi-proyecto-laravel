<!-- RecuperarClave.blade.php -->
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

            <!-- Mensajes de éxito -->
            @if (session('success'))
                <div class="alert alert-success" style="color: green;">
                    {{ session('success') }}
                </div>
            @endif

            <h2>Recuperar Contraseña</h2>
            <p>Por favor, responda la siguiente pregunta de seguridad:</p>

            <!-- Mostrar la pregunta -->
            <label for="respuesta" class="form-label" style="color: #000000;">{{ $pregunta->pregunta }}</label>

            <!-- Formulario de respuesta -->
            <form action="{{ route('recuperar.validarRespuesta') }}" method="POST">
                @csrf
                <!-- Asegúrate de pasar el id_usuario y el id_pregunta -->
                <input type="hidden" name="usuario_id" value="{{ $usuario->id_usuario }}">
                <input type="hidden" name="pregunta_id" value="{{ $pregunta->id_pregunta }}">
                <input type="text" name="respuesta" id="respuesta" class="form-control" required>

                <!-- Botón de enviar con estilos personalizados -->
                <button type="submit" class="btn btn-primary btn-block" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; font-size: 16px; border-radius: 5px;">Validar Respuesta</button>
            </form>
        </div>
    </div>
@endsection
