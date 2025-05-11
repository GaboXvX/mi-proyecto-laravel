@extends('layouts.app')

@section('content')
<div class="table-container">
    <h1>Editar Categoría</h1>

    <form action="{{ route('categorias-personas.update', $categoria->slug) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre_categoria" class="form-label">Nombre</label>
            <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" value="{{ old('nombre_categoria', $categoria->nombre_categoria) }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion', $categoria->descripcion) }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="requiere_comunidad" id="requiere_comunidad" class="form-check-input" {{ old('requiere_comunidad', $categoria->reglasConfiguradas->requiere_comunidad ?? false) ? 'checked' : '' }}>
            <label for="requiere_comunidad" class="form-check-label">Requiere comunidad</label>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="unico_en_comunidad" id="unico_en_comunidad" class="form-check-input" {{ old('unico_en_comunidad', $categoria->reglasConfiguradas->unico_en_comunidad ?? false) ? 'checked' : '' }}>
            <label for="unico_en_comunidad" class="form-check-label">Único en comunidad</label>
        </div>

        <div class="mb-3">
            <label for="mensaje_error" class="form-label">Mensaje de error</label>
            <input type="text" name="mensaje_error" id="mensaje_error" class="form-control" value="{{ old('mensaje_error', $categoria->reglasConfiguradas->mensaje_error ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('categorias-personas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
