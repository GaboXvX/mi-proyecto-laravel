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
        <div class="mt-3">
            <!-- Botón Volver utilizando ruta de Laravel -->
            <a href="{{ route('personas.index') }}" class="btn btn-primary fw-bold">Volver</a>
        </div>

        <!-- Card para mostrar la información personal -->
        <div class="card mt-4">
            <div class="card-header">
                Información Personal                                            
     <a href="{{ route('personas.edit', $persona->slug) }}" class="btn btn-warning btn-sm">Editar</a>

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
                            <th>Responsable:</th>
                            <td>{{ $persona->user->nombre }} {{ $persona->user->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Creado en:</th>
                            <td>{{ $persona->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card para mostrar las direcciones -->
       <div class="card mt-4">
    <div class="card-header">
        Direcciones
        <a href="{{ route('personas.agregarDireccion', $persona->slug) }}" class="btn btn-secondary btn-sm float-end">Añadir dirección</a>
    </div>
    <div class="card-body">
        <!-- Tabla con las direcciones existentes -->
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
                                <span class="{{ $direccion->esLider ? 'text-success' : 'text-danger' }}">
                                    {{ $direccion->esLider ? 'Sí' : 'No' }}
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
        </div>

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
                                <a href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $persona->slug]) }}">Modificar incidencia</a>
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
