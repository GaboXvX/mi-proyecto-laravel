@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modificar Direcciones</h1>
    @if (session('success'))
        <div class="alert alert-success mb-3" id="success-alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-3" id="error-alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-3" id="validation-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                <th>Número de Vivienda</th>
                <th>Bloque</th>
                <th>Principal</th>
                <th>¿Es líder Comunitario?</th>
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
                    <td>{{ $direccion->numero_de_vivienda }}</td>
                    <td>{{ $direccion->bloque }}</td>
                    <td>
                        <span class="{{ $direccion->es_principal ? 'text-success' : 'text-danger' }}">
                            {{ $direccion->es_principal ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="
                        @if($direccion->esLider)
                            text-success  <!-- Clase para color verde -->
                        @else
                            text-danger  <!-- Clase para color rojo -->
                        @endif
                        ">
                        {{ $direccion->esLider ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <!-- Botón de editar -->
                        <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="{{ $direccion->id_direccion }}" 
                                data-estado="{{ $direccion->estado }}" data-municipio="{{ $direccion->municipio }}"
                                data-parroquia="{{ $direccion->parroquia->nombre }}" data-urbanizacion="{{ $direccion->urbanizacion->nombre }}"
                                data-sector="{{ $direccion->sector->nombre }}" data-comunidad="{{ $direccion->comunidad->nombre }}"
                                data-calle="{{ $direccion->calle }}" data-manzana="{{ $direccion->manzana }}"
                                data-numero-de-vivienda="{{ $direccion->numero_de_vivienda }}" data-bloque="{{ $direccion->bloque }}"
                                data-id-persona="{{ $persona->id_persona }}"
                                data-bs-toggle="modal" data-bs-target="#editDireccionModal">
                            Modificar
                        </button>
                        @if(!$direccion->es_principal)
                            <form action="{{ route('personas.marcarPrincipal') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="id_direccion" value="{{ $direccion->id_direccion }}">
                                <button type="submit" class="btn btn-primary btn-sm" title="Marcar como principal">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </button>
                            </form>
                        @endif
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
                            <label for="bloque" class="form-label">Bloque: <small>(Solo si vive en apartamento)</small></label>
                            <input type="text" id="bloque" name="bloque" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="numero_de_vivienda" class="form-label">Número de Vivienda:</label>
                            <input type="text" id="numero_de_vivienda" name="numero_de_vivienda" class="form-control" required>
                        </div>

                        <div class="mb-3" id="categoria-container">
                            <label for="categoria" class="form-label">Categoría:</label>
                            <select id="categoria" name="categoria" class="form-select" required>
                                <option value="" disabled selected>--Seleccione--</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id_categoriaPersona }}">{{ $categoria->nombre_categoria }}</option>
                                @endforeach
                            </select>
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
            const idPersona = this.getAttribute('data-id-persona');
            const estado = this.getAttribute('data-estado');
            const municipio = this.getAttribute('data-municipio');
            const parroquia = this.getAttribute('data-parroquia');
            const urbanizacion = this.getAttribute('data-urbanizacion');
            const sector = this.getAttribute('data-sector');
            const comunidad = this.getAttribute('data-comunidad');
            const calle = this.getAttribute('data-calle');
            const manzana = this.getAttribute('data-manzana');
            const numeroDeVivienda = this.getAttribute('data-numero-de-vivienda');
            const bloque = this.getAttribute('data-bloque');

            // Asignar los valores al formulario del modal
            document.getElementById('direccion_id').value = id;
            document.getElementById('calle').value = calle;
            document.getElementById('manzana').value = manzana;
            document.getElementById('numero_de_vivienda').value = numeroDeVivienda;
            document.getElementById('bloque').value = bloque;

            // Establecer la acción del formulario con el id de la dirección y el id de la persona
            document.getElementById('editDireccionForm').action = `/personas/actualizardireccion/${id}/${idPersona}`;

            // Verificar si la persona ya es líder en la comunidad seleccionada
            fetch(`/check-lider-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ comunidad_id: comunidad, persona_id: idPersona })
            })
            .then(response => response.json())
            .then(data => {
                if (data.esLider) {
                    document.getElementById('categoria-container').style.display = 'none';
                } else {
                    document.getElementById('categoria-container').style.display = 'block';
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 2000);
    });
</script>
@endsection