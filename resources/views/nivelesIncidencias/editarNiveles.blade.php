@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2 class="mb-4">Editar Nivel de Incidencia</h2>

    <form action="{{ route('niveles-incidencia.update', $nivelIncidencia->id_nivel_incidencia) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" maxlength="30" value="{{ old('nombre', $nivelIncidencia->nombre) }}" required oninput="this.value = this.value.replace(/\s/g, '')">
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" maxlength="200" rows="3" required>{{ old('descripcion', $nivelIncidencia->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="dias">Días para vencimiento</label>
            <input type="number" class="form-control solo-numeros" id="dias" name="dias" min="0" value="{{ floor($nivelIncidencia->horas_vencimiento / 24) }}">
        </div>

        <div class="form-group">
            <label for="horas_vencimiento">Horas para vencimiento</label>
            <input type="number" class="form-control solo-numeros" id="horas_vencimiento" name="horas_vencimiento" min="0" value="{{ $nivelIncidencia->horas_vencimiento % 24 }}" required>
        </div>

        <div class="form-group">
            <label for="color">Color (hexadecimal)</label>
            <input type="color" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $nivelIncidencia->color) }}" style="height: 38px; width: 100px;" required>
            @error('color')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex mt-3 justify-content-between">
            <a href="{{ route('niveles-incidencia.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>

<script>
    // Al enviar el formulario, suma días*24 + horas y pone el resultado en horas_vencimiento
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if(form) {
            form.addEventListener('submit', function(e) {
                const dias = parseInt(document.getElementById('dias').value) || 0;
                const horas = parseInt(document.getElementById('horas_vencimiento').value) || 0;
                document.getElementById('horas_vencimiento').value = (dias * 24) + horas;
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const diasInput = document.getElementById('dias');
        const horasInput = document.getElementById('horas_vencimiento');

        diasInput.addEventListener('input', function () {
            if (parseInt(diasInput.value) > 10) {
                diasInput.value = 10;
            }
        });

        horasInput.addEventListener('input', function () {
            if (parseInt(horasInput.value) > 24) {
                horasInput.value = 24;
            }
        });
    });
</script>
@endsection