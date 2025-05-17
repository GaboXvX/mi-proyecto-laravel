@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2>Nueva Categoría de Persona</h2>

    <form method="POST" action="{{ route('categorias-personas.store') }}">
        @csrf

        <div class="mb-3">
            <label for="nombre_categoria" class="form-label">Nombre</label>
            <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" value="{{ old('nombre_categoria') }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion') }}</textarea>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" value="1" id="requiere_comunidad" name="requiere_comunidad" {{ old('requiere_comunidad') ? 'checked' : '' }}>
            <label class="form-check-label" for="requiere_comunidad">
                Requiere comunidad
            </label>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="unico_en_comunidad" name="unico_en_comunidad" {{ old('unico_en_comunidad') ? 'checked' : '' }}>
            <label class="form-check-label" for="unico_en_comunidad">
                Único en comunidad
            </label>
        </div>

        <div class="mb-3" id="mensaje_error_container" style="display: none;">
            <label for="mensaje_error" class="form-label">Mensaje de Error</label>
            <input type="text" name="mensaje_error" id="mensaje_error" class="form-control" value="{{ old('mensaje_error') }}">
        </div>

        <button type="submit" class="btn btn-primary">Crear</button>
        <a href="{{ route('categorias-personas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const unicoCheckbox = document.getElementById('unico_en_comunidad');
        const mensajeContainer = document.getElementById('mensaje_error_container');

        function toggleMensaje() {
            mensajeContainer.style.display = unicoCheckbox.checked ? 'block' : 'none';
        }

        unicoCheckbox.addEventListener('change', toggleMensaje);
        toggleMensaje();
    });
</script>
@endsection
