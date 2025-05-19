@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Nuevo Nivel de Incidencia</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('niveles-incidencia.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nivel">Nivel (1-5)</label>
            <input type="number" class="form-control" id="nivel" name="nivel" min="1" max="6" required>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="30" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" maxlength="200" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="horas_vencimiento">Horas para vencimiento</label>
            <input type="number" class="form-control" id="horas_vencimiento" name="horas_vencimiento" min="1" required>
        </div>


        <div class="form-group">
            <label for="color">Color (hexadecimal)</label>
            <input type="color" class="form-control" id="color" name="color" value="#FF0000" style="height: 38px; width: 100px;" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('niveles-incidencia.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection