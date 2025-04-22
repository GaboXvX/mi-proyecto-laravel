@extends('layouts.app')
@section('content')
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Lista de Personas</h2>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registroPersonaModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-plus" viewBox="0 0 16 16">
                            <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                            <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <input type="search" id="buscar" placeholder="Buscar por cédula" class="form-control d-inline-block" style="width: auto;">
            </div>

            <div id="personas-lista">
                @if (!empty($personas) && count($personas) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Cédula</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="personas-tbody">
                                @foreach ($personas as $persona)
                                    <tr>
                                        <td>{{ $persona->nombre }}</td>
                                        <td>{{ $persona->apellido }}</td>
                                        <td>{{ $persona->cedula }}</td>
                                        <td>{{ $persona->correo }}</td>
                                        <td>{{ $persona->telefono }}</td>
                                        <td>
                                            <div class="btn-group gap-1">
                                                <a href="{{ route('personas.show', $persona->slug) }}" class="btn btn-info btn-sm rounded" title="Ver persona">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                                    </svg>
                                                </a>
                                                <button type="button" class="btn btn-success btn-sm rounded" data-bs-toggle="modal" data-bs-target="#registroIncidenciaModal" data-persona-id="{{ $persona->id_persona }}" data-persona-nombre="{{ $persona->nombre }}" title="Añadir incidencia">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                                                        <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0"/>
                                                    </svg>
                                                </button>
                                                <a href="{{ route('personas.incidencias', $persona->slug) }}" class="btn btn-warning btn-sm rounded">Ver Incidencias</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="alert alert-warning">No se encontró ninguna persona con esa cédula.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="registroPersonaModal" tabindex="-1" aria-labelledby="registroPersonaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="registroPersonaForm" action="{{ route('personas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="registroPersonaModalLabel">Registrar Persona</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="global-alerts" class="alert d-none"></div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="categoria" class="form-label">Categoría:</label>
                                <select name="categoria" id="categoria" class="form-select" required>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id_categoriaPersona }}">{{ $categoria->nombre_categoria }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="genero" class="form-label">Género:</label>
                                <select name="genero" id="genero" class="form-select" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="11">
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">Apellido:</label>
                                <input type="text" id="apellido" name="apellido" class="form-control" required maxlength="11">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cedula" class="form-label">Cédula:</label>
                                <input type="text" id="cedula" name="cedula" class="form-control" required maxlength="8">
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" id="correo" name="correo" class="form-control" required>
                                <div class="invalid-feedback"></div>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono" class="form-control" pattern="[0-9]{10,11}" placeholder="Ej: 1234567890" required>
                            </div>
                            <div class="col-md-6">
                                <label for="altura" class="form-label">Altura (cm):</label>
                                <input type="number" id="altura" name="altura" class="form-control" required step="0.01" min="0.1" placeholder="Ej: 1.75">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>

                        <livewire:dropdown-persona/>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="calle" class="form-label">Calle:</label>
                                <input type="text" id="calle" name="calle" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="manzana" class="form-label">Manzana:</label>
                                <input type="text" id="manzana" name="manzana" class="form-control" >
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bloque" class="form-label">Bloque: <small>(Solo si vive en apartamento)</small></label>
                                <input type="text" id="bloque" name="bloque" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="num_vivienda" class="form-label">Número de Vivienda:</label>
                                <input type="number" id="num_vivienda" name="num_vivienda" class="form-control" required min="1" step="1">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="es_principal" class="form-label">¿Es la dirección principal?</label>
                            <select name="es_principal" id="es_principal" class="form-select" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitRegistroPersona">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registroIncidenciaModal" tabindex="-1" aria-labelledby="registroIncidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="registroIncidenciaForm" action="{{ route('incidencias.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="registroIncidenciaModalLabel">Registrar Incidencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_persona" name="id_persona" value="{{ isset($persona) ? $persona->id_persona : '' }}">

                        <div class="mb-3">
                            <label for="tipo_incidencia" class="form-label">Tipo de incidencia:</label>
                            <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                                <option value="" disabled selected>--Seleccione--</option>
                                <option value="agua potable">Agua Potable</option>
                                <option value="agua servida">Agua Servida</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <input type="text" id="descripcion" name="descripcion" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="nivel_prioridad" class="form-label">Nivel Incidencia:</label>
                            <select name="nivel_prioridad" id="nivel_prioridad" class="form-select" required>
                                <option value="" disabled selected>--Seleccione--</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección:</label>
                            <select id="direccion" name="direccion" class="form-select" required>
                                <option value="" disabled selected>--Seleccione--</option>
                                {{-- Opciones dinámicas cargadas con JavaScript --}}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración del buscador de personas
            const originalData = @json($personas->items());
            new BuscadorPersonas('buscar', 'personas-tbody', '{{ route('personas.buscar') }}', originalData);

            // Mostrar mensaje de éxito si está presente en la sesión
            const successMessage = "{{ session('success') }}";
            if (successMessage) {
                const alertContainer = document.createElement('div');
                alertContainer.className = 'alert alert-success';
                alertContainer.textContent = successMessage;
                document.querySelector('.table-container').prepend(alertContainer);
                setTimeout(() => alertContainer.remove(), 5000);
            }

            // Validación en tiempo real para cédula
            document.getElementById('cedula').addEventListener('blur', async function() {
                const cedula = this.value.trim();
                const errorElement = this.nextElementSibling;
                
                if (cedula.length < 6) return;
                
                try {
                    const response = await fetch('{{ route('personas.validar-cedula') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ cedula })
                    });
                    
                    const data = await response.json();
                    
                    if (data.exists) {
                        this.classList.add('is-invalid');
                        errorElement.textContent = 'Esta cédula ya está registrada';
                    } else {
                        this.classList.remove('is-invalid');
                        errorElement.textContent = '';
                    }
                } catch (error) {
                    console.error('Error al validar cédula:', error);
                }
            });

            // Validación en tiempo real para correo
            document.getElementById('correo').addEventListener('blur', async function() {
                const correo = this.value.trim();
                const errorElement = this.nextElementSibling;
                
                if (correo.length === 0 || !correo.includes('@')) return;
                
                try {
                    const response = await fetch('{{ route('personas.validar-correo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ correo })
                    });
                    
                    const data = await response.json();
                    
                    if (data.exists) {
                        this.classList.add('is-invalid');
                        errorElement.textContent = 'Este correo ya está registrado';
                    } else {
                        this.classList.remove('is-invalid');
                        errorElement.textContent = '';
                    }
                } catch (error) {
                    console.error('Error al validar correo:', error);
                }
            });

            // Manejo del submit del formulario
            document.getElementById('registroPersonaForm').addEventListener('submit', async function(event) {
                event.preventDefault();
                
                const form = event.target;
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const globalAlerts = document.getElementById('global-alerts');
                
                // Limpiar errores previos
                globalAlerts.classList.add('d-none');
                globalAlerts.innerHTML = '';
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                    const feedback = el.nextElementSibling;
                    if (feedback) feedback.textContent = '';
                });

                // Mostrar estado de carga
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Procesando...
                `;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        // Mostrar errores de campo específico
                        if (data.errors) {
                            Object.entries(data.errors).forEach(([field, messages]) => {
                                const input = form.querySelector(`[name="${field}"]`);
                                const errorElement = input?.nextElementSibling;
                                
                                if (input && errorElement) {
                                    input.classList.add('is-invalid');
                                    errorElement.textContent = messages[0];
                                }
                            });
                        }
                        
                        // Mostrar mensaje global si existe
                        if (data.message) {
                            globalAlerts.classList.remove('d-none');
                            globalAlerts.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                            globalAlerts.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        // Éxito - cerrar modal y recargar
                        const modal = bootstrap.Modal.getInstance(document.getElementById('registroPersonaModal'));
                        modal.hide();
                        
                        // Mostrar mensaje de éxito
                        const successAlert = document.createElement('div');
                        successAlert.className = 'alert alert-success';
                        successAlert.textContent = data.message || 'Registro exitoso';
                        document.querySelector('.table-container').prepend(successAlert);
                        setTimeout(() => successAlert.remove(), 5000);
                        
                        // Recargar la página
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    globalAlerts.classList.remove('d-none');
                    globalAlerts.innerHTML = `
                        <div class="alert alert-danger">
                            Error de conexión. Por favor intente nuevamente.
                        </div>
                    `;
                    globalAlerts.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Registrar';
                }
            });

            const registroIncidenciaModal = document.getElementById('registroIncidenciaModal');
            const registroIncidenciaForm = document.getElementById('registroIncidenciaForm');
            const direccionSelect = document.getElementById('direccion');

            registroIncidenciaModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const personaId = button.getAttribute('data-persona-id');

                // Limpiar el select de direcciones
                direccionSelect.innerHTML = '<option value="" disabled selected>--Cargando direcciones--</option>';

                // Cargar las direcciones de la persona
                fetch(`/api/personas/${personaId}/direcciones`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al cargar las direcciones');
                        }
                        return response.json();
                    })
                    .then(data => {
                        direccionSelect.innerHTML = '<option value="" disabled selected>--Seleccione--</option>';
                        data.forEach(direccion => {
                            direccionSelect.innerHTML += `<option value="${direccion.id_direccion}">
                                ${direccion.estado?.nombre || 'N/A'} - 
                                 ${direccion.municipio?.nombre || 'N/A'} - 
                                 ${direccion.parroquia?.nombre || 'N/A'} - 
                                 ${direccion.urbanizacion?.nombre || 'N/A'} - 
                                 ${direccion.sector?.nombre || 'N/A'} - 
                                 ${direccion.comunidad?.nombre || 'N/A'} - 
                                 ${direccion.calle || 'N/A'} 
                                 ${direccion.manzana || 'N/A'} 
                                 ${direccion.numero_de_vivienda || 'N/A'}
                            </option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar direcciones:', error);
                        direccionSelect.innerHTML = '<option value="" disabled>--Error al cargar direcciones--</option>';
                    });
            });

            registroIncidenciaForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(registroIncidenciaForm);
                fetch(registroIncidenciaForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            // Redirigir a la URL proporcionada en la respuesta
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message || 'Error al registrar la incidencia.');
                        }
                    })
                    .catch(error => {
                        console.error('Error al enviar el formulario:', error);
                        alert('Error de conexión. Por favor intente nuevamente.');
                    });
            });
        });

        class BuscadorPersonas {
            constructor(inputId, tbodyId, url, originalData) {
                this.input = document.getElementById(inputId);
                this.tbody = document.getElementById(tbodyId);
                this.url = url;
                this.originalData = originalData;
                this.input.addEventListener('input', () => this.buscarPersonas());
            }

            async buscarPersonas() {
                const query = this.input.value.trim();
                if (!query) {
                    this.mostrarResultados(this.originalData);
                    return;
                }

                try {
                    const response = await fetch(this.url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ query })
                    });
                    const personas = await response.json();
                    this.mostrarResultados(personas);
                } catch (error) {
                    console.error('Error al buscar personas:', error);
                }
            }

            mostrarResultados(personas) {
                this.tbody.innerHTML = personas.map(persona => `
                    <tr>
                        <td>${persona.nombre}</td>
                        <td>${persona.apellido}</td>
                        <td>${persona.cedula}</td>
                        <td>${persona.correo}</td>
                        <td>${persona.telefono}</td>
                        <td>
                            <div class="btn-group">
                                <a href="/persona/${persona.slug}" class="btn btn-info btn-sm">Ver</a>
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#registroIncidenciaModal" data-persona-id="${persona.id_persona}" data-persona-nombre="${persona.nombre}">
                                    Añadir Incidencia
                                </button>
                                <a href="/persona/${persona.slug}/incidencias" class="btn btn-warning btn-sm">Ver Incidencias</a>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }
        }
    </script>
@endsection
