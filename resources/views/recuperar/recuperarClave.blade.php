<!-- RecuperarClave.blade.php -->
@extends('layouts.registrar')

@section('content')
    <div class="container">
        <div class="content">
            <h2>Recuperar Contraseña</h2>
            <p>Por favor, responda la siguiente pregunta de seguridad:</p>

            <!-- Mostrar la pregunta -->
           

            <!-- Formulario de respuesta -->
            <form action="{{ route('recuperar.validarRespuesta') }}" method="POST">
                @csrf
                <!-- Asegúrate de pasar el id_usuario y el id_pregunta -->
                <input type="hidden" name="usuario_id" value="{{ $usuario->id_usuario }}">
                <input type="hidden" name="pregunta_id" value="{{ $pregunta->id_pregunta }}">
                <label for="respuesta" class="form-label" style="color: #000000;">{{ $pregunta->pregunta }}</label>

                <input type="text" name="respuesta" id="respuesta" class="form-control" required>

                <!-- Botón de enviar con estilos personalizados -->
                <button type="submit" class="btn btn-primary btn-block" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; font-size: 16px; border-radius: 5px;">Validar Respuesta</button>
            </form>
        </div>
    </div>
@endsection
