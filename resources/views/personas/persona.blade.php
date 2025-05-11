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
                Domicilios
                
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDomicilioModal">
                    Añadir Domicilio
                </button>
                
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
                                <th style="width: 10%;">Categoría</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($domicilios as $domicilio)
                                <tr id="domicilio_{{ $domicilio->id_domicilio }}">
                                    <td>{{ $domicilio->estado->nombre }}</td>
                                    <td>{{ $domicilio->municipio->nombre }}</td>
                                    <td>{{ $domicilio->parroquia->nombre }}</td>
                                    <td>{{ $domicilio->urbanizacion->nombre }}</td>
                                    <td>{{ $domicilio->sector->nombre }}</td>
                                    <td>{{ $domicilio->comunidad->nombre }}</td>
                                    <td>{{ $domicilio->calle }}</td>
                                    <td>{{ $domicilio->manzana }}</td>
                                    <td>{{ $domicilio->numero_de_vivienda }}</td>
                                    <td>{{ $domicilio->bloque }}</td>
                                    <td>
                                        <span class="{{ $domicilio->es_principal ? 'text-success' : 'text-danger' }}">
                                            {{ $domicilio->es_principal ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $categoriasExclusivas = $domicilio->persona->categoriasExclusivasPersonas
                                                ->where('id_comunidad', $domicilio->id_comunidad)
                                                ->where('es_activo', true);
                                        @endphp
                                        
                                        @if($categoriasExclusivas->count() > 0)
                                            @foreach($categoriasExclusivas as $categoriaExclusiva)
                                                <small class="text-muted">{{ $categoriaExclusiva->categoria->nombre_categoria }}</small><br>
                                            @endforeach
                                        @else
                                            {{$persona->categoria->nombre_categoria}}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <button type="button" class="btn btn-warning btn-sm mb-1 edit-btn" data-id="{{ $domicilio->id_domicilio }}" 
                                                    data-estado="{{ $domicilio->estado }}" data-municipio="{{ $domicilio->municipio }}"
                                                    data-parroquia="{{ $domicilio->parroquia->nombre }}" data-urbanizacion="{{ $domicilio->urbanizacion->nombre }}"
                                                    data-sector="{{ $domicilio->sector->nombre }}" data-comunidad="{{ $domicilio->comunidad->nombre }}"
                                                    data-calle="{{ $domicilio->calle }}" data-manzana="{{ $domicilio->manzana }}"
                                                    data-numero-de-vivienda="{{ $domicilio->numero_de_vivienda }}" data-bloque="{{ $domicilio->bloque }}"
                                                    data-id-persona="{{ $persona->id_persona }}"
                                                    data-bs-toggle="modal" data-bs-target="#editDomicilioModal">
                                                Modificar
                                            </button>
                                            @if(!$domicilio->es_principal)
                                                <form action="{{ route('personas.marcarPrincipal') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="id_domicilio" value="{{ $domicilio->id_domicilio }}">
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
                    {{ $domicilios->links() }}
                </div>
            </div>
        </div>

        <!-- Modal de edición de domicilio -->
        <div class="modal fade" id="editDomicilioModal" tabindex="-1" aria-labelledby="editDomicilioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDomicilioModalLabel">Editar Domicilio</h5>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="editDomicilioForm" action="">
                            @csrf
                            @method('POST')

                            <input type="hidden" id="domicilio_id" name="domicilio_id">

                            <livewire:dropdown-persona/>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="calle" class="form-label">Calle:</label>
                                    <input type="text" id="calle" name="calle" class="form-control" required maxlength="14">
                                </div>

                                <div class="col-md-6">
                                    <label for="manzana" class="form-label">Manzana:</label>
                                    <input type="text" id="manzana" name="manzana" class="form-control" maxlength="14">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="bloque" class="form-label">Bloque: <small>(Solo si vive en apartamento)</small></label>
                                <input type="text" id="bloque" name="bloque" class="form-control" maxlength="3">
                            </div>

                            <div class="mb-3">
                                <label for="numero_de_vivienda" class="form-label">Número de Vivienda:</label>
                                <input type="text" id="numero_de_vivienda" name="numero_de_vivienda" class="form-control" required maxlength="4">
                            </div>

                            <div class="mb-3" id="categoria-container">
                                <label for="categoria" class="form-label">Categoría:</label>
                                <select id="categoria" name="categoria" class="form-select" required>
                                    <option value="" disabled selected>--Seleccione--</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id_categoria_persona }}">{{ $categoria->nombre_categoria }}</option>
                                    @endforeach
                                </select>
                                
                            </div>

                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para añadir domicilio -->
        <div class="modal fade" id="addDomicilioModal" tabindex="-1" aria-labelledby="addDomicilioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDomicilioModalLabel">Agregar Domicilio</h5>
                    </div>
                    <div class="modal-body">
                        <div id="addDomicilioErrorContainer" class="alert alert-danger d-none"></div>
                        <form id="addDomicilioForm" method="POST" action="{{ route('guardarDireccion', $persona->id_persona) }}">
                            @csrf
                            <livewire:dropdown-persona />
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="calle" class="form-label">Calle:</label>
                                    <input type="text" id="calle" name="calle" class="form-control" required maxlength="14">
                                </div>

                                <div class="col-md-6">
                                    <label for="manzana" class="form-label">Manzana:</label>
                                    <input type="text" id="manzana" name="manzana" class="form-control" maxlength="14">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="bloque" class="form-label">Bloque: <small>(Solo si vive en apartamento)</small></label>
                                <input type="text" id="bloque" name="bloque" class="form-control" maxlength="3">
                            </div>

                            <div class="mb-3">
                                <label for="numero_de_vivienda" class="form-label">Número de Vivienda:</label>
                                <input type="number" id="numero_de_vivienda" name="numero_de_vivienda" class="form-control" required maxlength="4">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6" id="categoria-container">
                                    <label for="categoria" class="form-label">Categoría:</label>
                                    <select id="categoria" name="categoria" class="form-select" required>
                                        <option value="" disabled selected>--Seleccione--</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id_categoria_persona }}">{{ $categoria->nombre_categoria }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="es_principal" class="form-label">¿Es el domicilio principal?</label>
                                    <select name="es_principal" id="es_principal" class="form-select" required>
                                        <option value="1">Sí</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
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
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('personas.update', $persona->slug) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cédula:</label>
                                <input type="text" id="cedula" name="cedula" class="form-control" value="{{ old('cedula', $persona->cedula) }}" required maxlength="8">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre:</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $persona->nombre) }}" required maxlength="11">
                                </div>

                                <div class="col-md-6">
                                    <label for="apellido" class="form-label">Apellido:</label>
                                    <input type="text" id="apellido" name="apellido" class="form-control" value="{{ old('apellido', $persona->apellido) }}" required maxlength="11">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="correo" class="form-label">Correo Electrónico:</label>
                                    <input type="email" id="correo" name="correo" class="form-control" value="{{ old('correo', $persona->correo) }}" required maxlength="15">
                                </div>

                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono:</label>
                                    <input type="tel" id="telefono" name="telefono" class="form-control" value="{{ old('telefono', $persona->telefono) }}" required maxlength="11">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="genero" class="form-label">Género:</label>
                                <select name="genero" id="genero" class="form-select" required>
                                    <option value="M" {{ old('genero', $persona->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('genero', $persona->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                                </select>
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
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const idPersona = this.getAttribute('data-id-persona');

                // Campos que se van a llenar
                document.getElementById('domicilio_id').value = id;
                document.getElementById('calle').value = this.getAttribute('data-calle');
                document.getElementById('manzana').value = this.getAttribute('data-manzana');
                document.getElementById('numero_de_vivienda').value = this.getAttribute('data-numero-de-vivienda');
                document.getElementById('bloque').value = this.getAttribute('data-bloque');

                // Actualizamos el action del formulario
                document.getElementById('editDomicilioForm').action = `/personas/actualizardomicilio/${id}/${idPersona}`;

                // Confirmación dulce 🍬
                Swal.fire({
                    icon: 'info',
                    title: 'Editando Domicilio',
                    text: 'Los campos se han cargado correctamente. ¡Haz tus cambios!',
                    confirmButtonText: 'Entendido 💪',
                    timer: 2000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
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
       document.getElementById('addDireccionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    // Mostrar loader
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
    submitButton.disabled = true;

    try {
        const response = await fetch('{{ route('guardarDireccion', $persona->id_persona) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            throw data;
        }

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: data.title || '¡Éxito!',
                text: data.message,
                confirmButtonText: 'Aceptar'
            });
            
            // Redirigir si hay URL de redirección
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                location.reload();
            }
        }

    } catch (error) {
        let errorHtml = '<ul>';

        if (error.errors) {
            Object.values(error.errors).forEach(messages => {
                messages.forEach(message => {
                    errorHtml += `<li>${message}</li>`;
                });
            });
        } else if (error.message) {
            errorHtml += `<li>${error.message}</li>`;
        } else {
            errorHtml += '<li>Error desconocido al procesar la solicitud</li>';
        }

        errorHtml += '</ul>';

        await Swal.fire({
            icon: 'error',
            title: error.title || 'Error al guardar',
            html: errorHtml,
            confirmButtonText: 'Cerrar'
        });
    } finally {
        // Restaurar botón
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
});
    </script>
    <script>
        document.getElementById('editDireccionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Mostrar loader
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            submitButton.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: data.title || '¡Éxito!',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    });

                    // Redirigir si hay URL de redirección
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        location.reload();
                    }
                }

            } catch (error) {
                let errorHtml = '<ul>';

                if (error.errors) {
                    Object.values(error.errors).forEach(messages => {
                        messages.forEach(message => {
                            errorHtml += `<li>${message}</li>`;
                        });
                    });
                } else if (error.message) {
                    errorHtml += `<li>${error.message}</li>`;
                } else {
                    errorHtml += '<li>Error desconocido al procesar la solicitud</li>';
                }

                errorHtml += '</ul>';

                await Swal.fire({
                    icon: 'error',
                    title: error.title || 'Error al actualizar',
                    html: errorHtml,
                    confirmButtonText: 'Cerrar'
                });
            } finally {
                // Restaurar botón
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    </script>
    <script>
        document.getElementById('altura').addEventListener('input', function (e) {
            // Permitir solo números, una coma o punto, y limitar a un dígito antes y dos después
            this.value = this.value
                .replace(/[^0-9.,]/g, '') // Permitir solo números, coma o punto
                .replace(/,/g, '.') // Reemplazar coma por punto para consistencia
                .replace(/^(\d{2,})\./, '$1') // Limitar a un dígito antes del punto
                .replace(/(\.\d{2}).*/, '$1'); // Limitar a dos dígitos después del punto

            // Validar rango de altura (mínimo 0.50, máximo 2.72)
            const altura = parseFloat(this.value);
            if (altura < 0.5 || altura > 2.72) {
                this.value = ''; // Limpiar el campo si está fuera del rango
            }
        });

        document.getElementById('editPersonaModal').addEventListener('show.bs.modal', function () {
            const alturaInput = document.getElementById('altura');
            if (alturaInput) {
                // Eliminar cualquier texto adicional como "cm" y validar el formato
                alturaInput.value = alturaInput.value
                    .replace(/[^0-9.,]/g, '') // Permitir solo números, coma o punto
                    .replace(/,/g, '.') // Reemplazar coma por punto
                    .replace(/^(\d{2,})\./, '$1') // Limitar a un dígito antes del punto
                    .replace(/(\.\d{2}).*/, '$1'); // Limitar a dos dígitos después del punto
            }
        });
    </script>
    <script>
        document.getElementById('addDomicilioForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Mostrar loader
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            submitButton.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                // Mostrar alerta de éxito
                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: data.title || '¡Éxito!',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    });

                    // Redirigir si hay URL de redirección
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        location.reload();
                    }
                }
            } catch (error) {
                // Manejar errores
                let errorHtml = '<ul>';

                if (error.errors) {
                    Object.values(error.errors).forEach(messages => {
                        messages.forEach(message => {
                            errorHtml += `<li>${message}</li>`;
                        });
                    });
                } else if (error.message) {
                    errorHtml += `<li>${error.message}</li>`;
                } else {
                    errorHtml += '<li>Error desconocido al procesar la solicitud</li>';
                }

                errorHtml += '</ul>';

                // Mostrar alerta de error
                await Swal.fire({
                    icon: 'error',
                    title: error.title || 'Error al guardar',
                    html: errorHtml,
                    confirmButtonText: 'Cerrar'
                });
            } finally {
                // Restaurar botón
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    </script>
    <script>
        document.getElementById('editDomicilioForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Mostrar loader
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            submitButton.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                // Mostrar alerta de éxito
                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: data.title || '¡Éxito!',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    });

                    // Redirigir si hay URL de redirección
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        location.reload();
                    }
                }
            } catch (error) {
                // Manejar errores
                let errorHtml = '<ul>';

                if (error.errors) {
                    Object.values(error.errors).forEach(messages => {
                        messages.forEach(message => {
                            errorHtml += `<li>${message}</li>`;
                        });
                    });
                } else if (error.message) {
                    errorHtml += `<li>${error.message}</li>`;
                } else {
                    errorHtml += '<li>Error desconocido al procesar la solicitud</li>';
                }

                errorHtml += '</ul>';

                // Mostrar alerta de error
                await Swal.fire({
                    icon: 'error',
                    title: error.title || 'Error al actualizar',
                    html: errorHtml,
                    confirmButtonText: 'Cerrar'
                });
            } finally {
                // Restaurar botón
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    </script>
@endsection