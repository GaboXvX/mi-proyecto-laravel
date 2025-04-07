@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        @if (session('success'))
            <div class="alert alert-success mb-3">
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

        <h2 class="text-center">Datos de la Persona</h2>

        <div class="card mt-4">
            <div class="card-header">
                Información Personal                                            
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPersonaModal">
                    Editar
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $persona->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Apellido:</th>
                            <td>{{ $persona->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Cédula:</th>
                            <td>{{ $persona->cedula }}</td>
                        </tr>
                        <tr>
                            <th>Correo Electrónico:</th>
                            <td>{{ $persona->correo }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $persona->telefono }}</td>
                        </tr>
                        <tr>
                            <th>Género:</th>
                            <td>{{ $persona->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                        </tr>
                        <tr>
                            <th>Altura:</th>
                            <td>{{ $persona->altura }}</td>
                            <tr>
                                <th>Responsable:</th>
                                <td>
                                    @if($persona->user && $persona->user->empleadoAutorizado)
                                        {{ $persona->user->empleadoAutorizado->nombre }} {{ $persona->user->empleadoAutorizado->apellido }}
                                    @else
                                        No asignado
                                    @endif
                                </td>
                            </tr>
                        <tr>
                            <th>Creado en:</th>
                            <td>{{ $persona->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                Direcciones
                <button type="button" class="btn btn-secondary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addDireccionModal">Añadir dirección</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Municipio</th>
                                <th style="width: 10%;">Parroquia</th>
                                <th style="width: 10%;">Urbanización</th>
                                <th style="width: 10%;">Sector</th>
                                <th style="width: 10%;">Comunidad</th>
                                <th style="width: 10%;">Calle</th>
                                <th style="width: 5%;">Manzana</th>
                                <th style="width: 5%;">N° vivienda</th>
                                <th style="width: 5%;">Bloque</th>
                                <th style="width: 5%;">Principal</th>
                                <th style="width: 5%;">Líder</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($direcciones as $direccion)
                                <tr id="direccion_{{ $direccion->id_direccion }}">
                                    <td>{{ $direccion->estado->nombre }}</td>
                                    <td>{{ $direccion->municipio->nombre }}</td>
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
                                        @php
                                            $lider = \App\Models\Lider_Comunitario::where('id_persona', $persona->id_persona)
                                                ->where('id_comunidad', $direccion->id_comunidad)
                                                ->first();
                                        @endphp
                                        <span class="{{ $lider && $lider->estado == 1 ? 'text-success' : 'text-danger' }}">
                                            {{ $lider && $lider->estado == 1 ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <button type="button" class="btn btn-warning btn-sm mb-1 edit-btn" data-id="{{ $direccion->id_direccion }}" 
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
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $direcciones->links() }}
                </div>
            </div>
        </div>

        <!-- Modal de edición de dirección -->
        <div class="modal fade" id="editDireccionModal" tabindex="-1" aria-labelledby="editDireccionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDireccionModalLabel">Editar Dirección</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="editDireccionForm" action="">
                            @csrf
                            @method('POST')

                            <input type="hidden" id="direccion_id" name="direccion_id">

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

        <!-- Modal para añadir dirección -->
        <div class="modal fade" id="addDireccionModal" tabindex="-1" aria-labelledby="addDireccionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDireccionModalLabel">Agregar Dirección</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="addDireccionErrorContainer" class="alert alert-danger d-none"></div>
                        <form id="addDireccionForm">
                            @csrf
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
                                <input type="number" id="numero_de_vivienda" name="numero_de_vivienda" class="form-control" required min="1" step="1">
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

                            <div class="mb-3">
                                <label for="es_principal" class="form-label">¿Es la dirección principal?</label>
                                <select name="es_principal" id="es_principal" class="form-select" required>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar persona -->
        <div class="modal fade" id="editPersonaModal" tabindex="-1" aria-labelledby="editPersonaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPersonaModalLabel">Editar Persona</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('personas.update', $persona->slug) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" id="nombre" name="nombre" class="form-control"
                                    value="{{ old('nombre', $persona->nombre) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido:</label>
                                <input type="text" id="apellido" name="apellido" class="form-control"
                                    value="{{ old('apellido', $persona->apellido) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cédula:</label>
                                <input type="number" id="cedula" name="cedula" class="form-control"
                                    value="{{ old('cedula', $persona->cedula) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" id="correo" name="correo" class="form-control"
                                    value="{{ old('correo', $persona->correo) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono" class="form-control"
                                    value="{{ old('telefono', $persona->telefono) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="genero" class="form-label">Género:</label>
                                <select name="genero" id="genero" class="form-select" required>
                                    <option value="M" {{ old('genero', $persona->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('genero', $persona->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="altura" class="form-label">Altura (cm):</label>
                                <input type="number" id="altura" name="altura" class="form-control"
                                    value="{{ old('altura', $persona->altura) }}" required min="0" step="0.01">
                            </div>

                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
                                    value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento) }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
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

                document.getElementById('direccion_id').value = id;
                document.getElementById('calle').value = calle;
                document.getElementById('manzana').value = manzana;
                document.getElementById('numero_de_vivienda').value = numeroDeVivienda;
                document.getElementById('bloque').value = bloque;

                document.getElementById('editDireccionForm').action = `/personas/actualizardireccion/${id}/${idPersona}`;

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

        const editPersonaModal = document.getElementById('editPersonaModal');
        
        editPersonaModal.addEventListener('show.bs.modal', function (event) {
            const modalContent = document.getElementById('modalEditPersonaContent');
            modalContent.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
            
            fetch(`{{ route('personas.edit', $persona->slug) }}`)
                .then(response => response.text())
                .then(html => {
                    modalContent.innerHTML = html;
                })
                .catch(error => {
                    modalContent.innerHTML = '<div class="alert alert-danger">Error al cargar el formulario de edición</div>';
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

    <script>
        document.getElementById('addDireccionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorContainer = document.getElementById('addDireccionErrorContainer');
            errorContainer.classList.add('d-none');
            errorContainer.innerHTML = '';

            fetch('{{ route('guardarDireccion', $persona->id_persona) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload();
                }
            })
            .catch(error => {
                let errorHtml = '<ul>';
                if (error.errors) {
                    Object.values(error.errors).forEach(messages => {
                        messages.forEach(message => {
                            errorHtml += `<li>${message}</li>`;
                        });
                    });
                } else if (error.message) {
                    errorHtml += `<li>${error.message}</li>`;
                    if (error.error) {
                        errorHtml += `<li>${error.error}</li>`;
                    }
                } else {
                    errorHtml += '<li>Error desconocido al procesar la solicitud</li>';
                }
                errorHtml += '</ul>';
                errorContainer.innerHTML = errorHtml;
                errorContainer.classList.remove('d-none');
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        document.getElementById('comunidad').addEventListener('change', function() {
            const comunidadId = this.value;
            const personaId = {{ $persona->id_persona }};
            
            fetch(`/check-lider-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ comunidad_id: comunidadId, persona_id: personaId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.esLider) {
                    document.getElementById('categoria-container').style.display = 'block';
                } else {
                    document.getElementById('categoria-container').style.display = 'none';
                }
            });
        });
    </script>

    <hr>

    <h3>Reportes de Incidencias</h3>
    @if($persona->incidencias->isEmpty())
        <p>No hay incidencias registradas para esta persona.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Tipo de Incidencia</th>
                    <th>Descripción</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($persona->incidencias as $incidencia)
                    <tr>
                        <td>{{ $incidencia->tipo_incidencia }}</td>
                        <td>{{ $incidencia->descripcion }}</td>
                        <td>{{ $incidencia->nivel_prioridad }}</td>
                        <td>{{ $incidencia->estado }}</td>
                        <td>{{ $incidencia->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($incidencia->estado !== 'atendido')
                                <a href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $persona->slug]) }}" class="btn btn-primary btn-sm">Modificar incidencia</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>
    setTimeout(function() {
        document.getElementById('error-alert')?.style.display = 'none';
        document.getElementById('validation-errors')?.style.display = 'none';
    }, 2000);
</script>
@endsection