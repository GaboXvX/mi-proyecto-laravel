@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2 class="mb-4">Editar Nivel de Incidencia</h2>

<form action="{{ route('niveles-incidencia.update', $nivelIncidencia->id_nivel_incidencia) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nivel">Nivel (1-5)</label>
            <input type="number" class="form-control" id="nivel" name="nivel"  value="{{ $nivelIncidencia->nivel }}" required>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="30" value="{{ $nivelIncidencia->nombre }}" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" maxlength="200" rows="3" required>{{ $nivelIncidencia->descripcion }}</textarea>
        </div>

        <div class="form-group">
            <label for="horas_vencimiento">Horas para vencimiento</label>
            <input type="number" class="form-control" id="horas_vencimiento" name="horas_vencimiento" min="1" value="{{ $nivelIncidencia->horas_vencimiento }}" required>
        </div>

        

        <div class="form-group">
            <label for="color">Color (hexadecimal)</label>
            <input type="color" class="form-control" id="color" name="color" value="{{ $nivelIncidencia->color }}" style="height: 38px; width: 100px;" required>
        </div>

        <div class="d-flex mt-3 justify-content-between">
            <a href="{{ route('niveles-incidencia.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>
@endsection