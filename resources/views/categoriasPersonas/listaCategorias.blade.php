@extends('layouts.app')

@section('content')
<div class="table-container">
    <h1 class="mb-4">Categorías de Personas</h1>
    <a href="{{ route('categorias-personas.create') }}" class="btn btn-primary mb-3">Nueva Categoría</a>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Descripción</th>
                <th>Requiere Comunidad</th>
                <th>Único en Comunidad</th>
                <th>Mensaje de Error</th>
                <th>Personas Asociadas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categorias as $categoria)
                <tr id="categoria-{{ $categoria->id_categoria_persona }}">
                    <td>{{ $categoria->nombre_categoria }}</td>
                    <td>{{ $categoria->slug }}</td>
                    <td>{{ $categoria->descripcion }}</td>
                    <td>{{ $categoria->reglasConfiguradas?->requiere_comunidad ? 'Sí' : 'No' }}</td>
                    <td>{{ $categoria->reglasConfiguradas?->unico_en_comunidad ? 'Sí' : 'No' }}</td>
                    <td>{{ $categoria->reglasConfiguradas?->mensaje_error ?? '-' }}</td>
                    <td class="personas-count" data-id="{{ $categoria->id_categoria_persona }}">
                        {{ $categoria->personas->count() }}
                    </td>
                    <td>
                        <a href="{{ route('categorias-personas.edit', $categoria->slug) }}" class="btn btn-sm btn-warning">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay categorías registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Función para obtener el número de personas y actualizar la tabla
   function updatePersonasCount(categoriaId) {
    // Añade un timestamp como parámetro para evitar caché
    const timestamp = new Date().getTime();
    fetch(`/categorias-personas/${categoriaId}/personas-count?t=${timestamp}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta');
            }
            return response.json();
        })
        .then(data => {
            if (data.personas_count !== undefined) {
                const row = document.querySelector(`#categoria-${categoriaId}`);
                const personasCountCell = row.querySelector('.personas-count');
                personasCountCell.textContent = data.personas_count;
            }
        })
        .catch(error => {
            console.error('Error al obtener el número de personas:', error);
        });
}
    // Actualización periódica del número de personas cada 30 segundos
    setInterval(() => {
        document.querySelectorAll('.personas-count').forEach(cell => {
            const categoriaId = cell.dataset.id;
            updatePersonasCount(categoriaId);  // Actualiza el contador de personas para cada categoría
        });
    }, 30000);  // 30 segundos
});
</script>
@endsection
