@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Configura tus preguntas de seguridad</h2>
    <p>Por favor, selecciona y responde las siguientes preguntas de seguridad para proteger tu cuenta.</p>

    <!-- Mostrar mensajes de error o éxito -->
    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <form action="{{ route('seguridad.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="pregunta_1">¿Cuál es el nombre de tu primera mascota?</label>
            <input type="text" name="pregunta_1" id="pregunta_1" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="pregunta_2">¿En qué ciudad naciste?</label>
            <input type="text" name="pregunta_2" id="pregunta_2" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="pregunta_3">¿Cuál es el nombre de tu mejor amigo de la infancia?</label>
            <input type="text" name="pregunta_3" id="pregunta_3" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Preguntas de Seguridad</button>
    </form>
</div>
@endsection
