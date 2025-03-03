@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modificar Direcciones</h1>

    <!-- Botón para añadir dirección -->
    <button type="button" class="btn btn-primary mb-4" id="addDireccionBtn">Añadir Dirección</button>

    <!-- Tabla con las direcciones existentes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Estado</th>
                <th>Municipio</th>
                <th>Parroquia</th>
                <th>Urbanización</th>
                <th>Sector</th>
                <th>Comunidad</th>
                <th>Calle</th>
                <th>Manzana</th>
                <th>Número de Casa</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($persona->direccion as $direccion)
                <tr id="direccion_{{ $direccion->id_direccion }}">
                    <td>{{ $direccion->estado }}</td>
                    <td>{{ $direccion->municipio }}</td>
                    <td>{{ $direccion->parroquia->nombre }}</td>
                    <td>{{ $direccion->urbanizacion->nombre }}</td>
                    <td>{{ $direccion->sector->nombre }}</td>
                    <td>{{ $direccion->comunidad->nombre }}</td>
                    <td>{{ $direccion->calle }}</td>
                    <td>{{ $direccion->manzana }}</td>
                    <td>{{ $direccion->numero_de_casa }}</td>
                    <td>
                        <!-- Botón de editar -->
                        <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="{{ $direccion->id_direccion }}" 
                                data-estado="{{ $direccion->estado }}" data-municipio="{{ $direccion->municipio }}"
                                data-parroquia="{{ $direccion->parroquia->nombre }}" data-urbanizacion="{{ $direccion->urbanizacion->nombre }}"
                                data-sector="{{ $direccion->sector->nombre }}" data-comunidad="{{ $direccion->comunidad->nombre }}"
                                data-calle="{{ $direccion->calle }}" data-manzana="{{ $direccion->manzana }}"
                                data-numero-de-casa="{{ $direccion->numero_de_casa }}" data-bs-toggle="modal" data-bs-target="#editDireccionModal">
                            Modificar
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal de edición de dirección -->
    <div class="modal fade" id="editDireccionModal" tabindex="-1" aria-labelledby="editDireccionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDireccionModalLabel">Editar Dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para editar dirección -->
                    <form method="POST" id="editDireccionForm" action="">
                        @csrf
                        @method('POST') <!-- Usamos POST para enviar los datos a la ruta configurada -->

                        <input type="hidden" id="direccion_id" name="direccion_id">

                        <!-- Componente Livewire -->
                        <livewire:dropdown-persona />

                        <div class="mb-3">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" id="calle" name="calle" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="manzana" class="form-label">Manzana:</label>
                            <input type="text" id="manzana" name="manzana" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="numero_de_casa" class="form-label">Número de Casa:</label>
                            <input type="text" id="numero_de_casa" name="numero_de_casa" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para llenar el modal con los datos de la dirección seleccionada
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const estado = this.getAttribute('data-estado');
            const municipio = this.getAttribute('data-municipio');
            const parroquia = this.getAttribute('data-parroquia');
            const urbanizacion = this.getAttribute('data-urbanizacion');
            const sector = this.getAttribute('data-sector');
            const comunidad = this.getAttribute('data-comunidad');
            const calle = this.getAttribute('data-calle');
            const manzana = this.getAttribute('data-manzana');
            const numeroDeCasa = this.getAttribute('data-numero-de-casa');

            // Asignar los valores al formulario del modal
            document.getElementById('direccion_id').value = id;
            document.getElementById('calle').value = calle;
            document.getElementById('manzana').value = manzana;
            document.getElementById('numero_de_casa').value = numeroDeCasa;

            // Establecer la acción del formulario con el id de la dirección
            document.getElementById('editDireccionForm').action = `/personas/actualizardireccion/${id}`;
        });
    });
</script>

@endsection