@extends('layouts.app')

@section('content')
<div class="table-container mt-5">
    <h2 class="mb-4">Registrar Incidencia General</h2>

    <div id="alert-container"></div>
    
    <form id="incidenciaGeneralForm" action="{{ route('incidencias.store') }}" method="POST">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" name="id_persona" value="{{ $persona->id_persona }}" />


        <!-- Paso visual -->
        <div>
            <ul class="nav nav-pills justify-content-center" id="stepIndicator">
                <li class="nav-item">
                    <a class="nav-link active" data-step="1">
                        <div class="step-circle">1</div>
                        <span>Dirección</span>
                        <div class="connector"></div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" data-step="2">
                        <div class="step-circle">2</div>
                        <span>Institución</span>
                        <div class="connector"></div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" data-step="3">
                        <div class="step-circle">3</div>
                        <span>Detalles</span>
                    </a>
                </li>
            </ul>
        </div>


        <!-- Paso 1: Dirección -->
        <div class="step" id="step-1">
            <div class="card border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Dirección del Incidente</h5>
                </div>
                <div class="card-body">
                    <livewire:dropdown-persona/>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" id="calle" name="calle" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="punto_de_referencia" class="form-label">Punto de Referencia:</label>
                            <input type="text" id="punto_de_referencia" name="punto_de_referencia" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end m-3">
                    <button type="button" class="btn btn-primary" id="next-to-step-2">
                        Siguiente <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Paso 2: Institución y Estación -->
       <!-- Paso 2: Institución y Estación -->
<div class="step d-none" id="step-2">
    <div class="card border-0 mb-3">
        <div class="card-header">
            <h5 class="mb-0">Institución y Estación</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="institucion" class="form-label">Institución Responsable</label>
                <select id="institucion" name="institucion" class="form-select form-select-sm" required>
                    <option value="" disabled selected>--Seleccione--</option>
                    @foreach($instituciones as $institucion)
                        <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="estacion" class="form-label">Estación</label>
                <select id="estacion" name="estacion" class="form-select form-select-sm" required>
                    <option value="" disabled selected>--Seleccione una estación--</option>
                </select>
            </div>

            <!-- Botón para agregar instituciones de apoyo -->
            <div class="mb-3">
                <button type="button" id="btn-agregar-apoyo" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo
                </button>
            </div>

            <!-- Contenedor para instituciones de apoyo (oculto inicialmente) -->
            <div id="contenedor-apoyo" class="d-none">
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Institución de Apoyo</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="institucion_apoyo" class="form-label">Institución</label>
                                <select id="institucion_apoyo" class="form-select form-select-sm">
                                    <option value="" selected>--Seleccione--</option>
                                    @foreach($instituciones as $institucion)
                                        <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="estacion_apoyo" class="form-label">Estación</label>
                                <select id="estacion_apoyo" class="form-select form-select-sm">
                                    <option value="" selected>--Seleccione una estación--</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" id="btn-agregar-item" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de instituciones de apoyo agregadas -->
                <div class="mb-3">
                    <h6>Instituciones de Apoyo Seleccionadas:</h6>
                    <div id="lista-apoyo" class="list-group">
                        <!-- Aquí se agregarán dinámicamente las instituciones de apoyo -->
                    </div>
                    <!-- Campos ocultos para enviar los datos -->
                    <div id="campos-ocultos-apoyo">
                        <!-- Se generarán dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between m-3">
            <button type="button" class="btn btn-secondary btn-sm" id="back-to-step-1">
                <i class="bi bi-chevron-left me-1"></i> Atrás
            </button>
            <button type="button" class="btn btn-primary btn-sm" id="next-to-step-3">
                Siguiente <i class="bi bi-chevron-right ms-1"></i>
            </button>
        </div>
    </div>
</div>

        <!-- Paso 3 -->
        <div class="step d-none" id="step-3">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Detalles de la Incidencia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tipo_incidencia" class="form-label">Tipo de Incidencia</label>
                        <select id="tipo_incidencia" name="tipo_incidencia" class="form-select form-select-sm" required>
                            <option value="" disabled selected>--Seleccione--</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_incidencia }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control form-control-sm" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="nivel_prioridad" class="form-label">Nivel de Prioridad</label>
                        <select id="nivel_prioridad" name="nivel_prioridad" class="form-select form-select-sm" required>
                            <option value="" disabled selected>--Seleccione--</option>
                           @foreach($prioridades as $prioridad)
                           @if($prioridad->activo == 1)
                                <option value="{{ $prioridad->id_nivel_incidencia }}">{{ $prioridad->nivel }}/{{$prioridad->nombre}}</option>
                            @endif
                            @endforeach
                          
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                    <button type="button" class="btn btn-secondary btn-sm" id="back-to-step-2">
                        <i class="bi bi-chevron-left me-1"></i> Atrás
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check-circle me-1"></i> Registrar Incidencia
                    </button>
                </div>
            </div>
    </form>
</div>

<script src="{{ asset('js/incidencias.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const steps = document.querySelectorAll('.step');
    const nextToStep2 = document.getElementById('next-to-step-2');
    const nextToStep3 = document.getElementById('next-to-step-3');
    const backToStep1 = document.getElementById('back-to-step-1');
    const backToStep2 = document.getElementById('back-to-step-2');

    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('d-none', index !== stepIndex);
        });
    }

    nextToStep2.addEventListener('click', () => showStep(1));
    nextToStep3.addEventListener('click', () => showStep(2));
    backToStep1.addEventListener('click', () => showStep(0));
    backToStep2.addEventListener('click', () => showStep(1));

    const estadoSelect = document.getElementById('estado');
    const institucionSelect = document.getElementById('institucion');
    const estacionSelect = document.getElementById('estacion');

    async function cargarEstaciones() {
        const estadoId = estadoSelect.value;
        const institucionId = institucionSelect.value;

        estacionSelect.innerHTML = '<option value="" disabled selected>--Seleccione una estación--</option>';

        if (!estadoId || !institucionId) return;

        try {
            const response = await fetch(`/instituciones-estaciones/estado/${estadoId}/institucion/${institucionId}`);
            const data = await response.json();

            if (data.success) {
                data.estaciones.forEach(estacion => {
                    const option = document.createElement('option');
                    option.value = estacion.id_institucion_estacion;
                    option.textContent = `${estacion.nombre} (Municipio: ${estacion.municipio.nombre})`;
                    estacionSelect.appendChild(option);
                });

                if (data.estaciones.length === 0) {
                    alert('No hay estaciones disponibles para esta combinación.');
                }
            } else {
                alert(data.message || 'Error al cargar las estaciones.');
            }
        } catch (error) {
            console.error('Error al cargar las estaciones:', error);
            alert('Ocurrió un error al cargar las estaciones. Intente nuevamente.');
        }
    }

    estadoSelect.addEventListener('change', cargarEstaciones);
    institucionSelect.addEventListener('change', cargarEstaciones);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('incidenciaGeneralForm');

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        Swal.fire({
            title: 'Procesando',
            html: 'Registrando la incidencia...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (!response.ok) {
                // Si es duplicada
                if (response.status === 422 && data.is_duplicate) {
                    let htmlContent = `
                        <div class="text-start">
                            <p>${data.message}</p>
                            <div class="mt-3">
                                <strong>Detalles de la incidencia existente:</strong>
                                <ul class="list-unstyled">
                                    <li><strong>Código:</strong> ${data.duplicate_data.codigo}</li>
                                    <li><strong>Descripción:</strong> ${data.duplicate_data.descripcion}</li>
                                    <li><strong>Fecha:</strong> ${data.duplicate_data.fecha_creacion}</li>
                                    <li><strong>Estado:</strong> ${data.duplicate_data.estado}</li>
                                    <li><strong>Prioridad:</strong> ${data.duplicate_data.prioridad}</li>
                                </ul>
                            </div>
                        </div>`;
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incidencia duplicada',
                        html: htmlContent,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Ver incidencia',
                        denyButtonText: 'Continuar registro',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            actions: 'swal2-duplicate-actions'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Ver incidencia existente
                            window.open(data.ver_url, '_blank');
                        } else if (result.isDenied) {
                            // Confirmación para continuar
                            Swal.fire({
                                title: 'Continuar registro',
                                text: '¿Está seguro que desea registrar esta incidencia aunque sea similar a una existente?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, registrar',
                                cancelButtonText: 'No, volver'
                            }).then((confirmResult) => {
                                if (confirmResult.isConfirmed) {
                                    // Reenviar con force_register=true
                                    const forcedFormData = new FormData(form);
                                    forcedFormData.append('force_register', 'true');

                                    fetch(form.action, {
                                        method: 'POST',
                                        body: forcedFormData,
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: '¡Éxito!',
                                                text: data.message,
                                                confirmButtonText: 'Aceptar'
                                            }).then(() => {
                                                window.location.href = data.redirect_url;
                                            });
                                        } else {
                                            throw new Error(data.message);
                                        }
                                    })
                                    .catch(error => {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: error.message || 'Ocurrió un error al registrar la incidencia',
                                            confirmButtonText: 'Aceptar'
                                        });
                                    });
                                }
                            });
                        }
                        // Si canceló, no se hace nada
                    });
                    return;
                }

                throw new Error(data.message || 'Error en la respuesta del servidor');
            }

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = data.redirect_url;
                    }
                });
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Ocurrió un error al registrar la incidencia',
                confirmButtonText: 'Aceptar'
            });
            console.error('Error:', error);
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ... código existente ...

    // Mostrar/ocultar sección de apoyo
    const btnAgregarApoyo = document.getElementById('btn-agregar-apoyo');
    const contenedorApoyo = document.getElementById('contenedor-apoyo');
    
    btnAgregarApoyo.addEventListener('click', function() {
        contenedorApoyo.classList.toggle('d-none');
        if (contenedorApoyo.classList.contains('d-none')) {
            btnAgregarApoyo.innerHTML = '<i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo';
        } else {
            btnAgregarApoyo.innerHTML = '<i class="bi bi-dash-circle"></i> Ocultar';
        }
    });

    // Select de institución de apoyo
    const institucionApoyoSelect = document.getElementById('institucion_apoyo');
    const estacionApoyoSelect = document.getElementById('estacion_apoyo');
    
    // Función para cargar estaciones de apoyo
    function cargarEstacionesApoyo(institucionId) {
        if (!institucionId) {
            estacionApoyoSelect.innerHTML = '<option value="">Primero seleccione una institución</option>';
            estacionApoyoSelect.disabled = true;
            return;
        }

        estacionApoyoSelect.disabled = true;
        estacionApoyoSelect.innerHTML = '<option value="">Cargando estaciones...</option>';

        const url = `/personal-reparacion/estaciones/${institucionId}`;
        console.log('Cargando estaciones de apoyo desde:', url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(({ success, data }) => {
                estacionApoyoSelect.innerHTML = '<option value="">Seleccione una estación</option>';

                if (success && data.length > 0) {
                    data.forEach(estacion => {
                        const nombre = estacion.codigo ? `${estacion.nombre} (${estacion.codigo})` : estacion.nombre;
                        const option = new Option(nombre, estacion.id);
                        estacionApoyoSelect.add(option);
                    });
                    estacionApoyoSelect.disabled = false;
                } else {
                    estacionApoyoSelect.innerHTML = '<option value="">No hay estaciones disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar estaciones de apoyo:', error);
                estacionApoyoSelect.innerHTML = '<option value="">Error al cargar estaciones</option>';
            });
    }

    // Cargar estaciones de apoyo cuando cambia la institución
    institucionApoyoSelect.addEventListener('change', function() {
        const institucionId = this.value;
        cargarEstacionesApoyo(institucionId);
    });

    // Agregar institución de apoyo a la lista
    const btnAgregarItem = document.getElementById('btn-agregar-item');
    const listaApoyo = document.getElementById('lista-apoyo');
    const camposOcultosApoyo = document.getElementById('campos-ocultos-apoyo');
    
    btnAgregarItem.addEventListener('click', function() {
        const institucionId = institucionApoyoSelect.value;
        const estacionId = estacionApoyoSelect.value;
        
        if (!institucionId) {
            alert('Seleccione una institución');
            return;
        }
        
        const institucionNombre = institucionApoyoSelect.options[institucionApoyoSelect.selectedIndex].text;
        const estacionNombre = estacionId ? 
            estacionApoyoSelect.options[estacionApoyoSelect.selectedIndex].text : 
            'Todas las estaciones';
        
        // Verificar si ya existe
        const existe = Array.from(listaApoyo.children).some(item => {
            return item.getAttribute('data-institucion-id') === institucionId && 
                   item.getAttribute('data-estacion-id') === estacionId;
        });
        
        if (existe) {
            alert('Esta combinación ya fue agregada');
            return;
        }
        
        // Crear elemento de lista
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.setAttribute('data-institucion-id', institucionId);
        listItem.setAttribute('data-estacion-id', estacionId);
        
        listItem.innerHTML = `
            <div>
                <strong>${institucionNombre}</strong> - ${estacionNombre}
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-apoyo">
                <i class="bi bi-trash"></i>
            </button>
        `;
        
        // Crear campos ocultos para el formulario
        const inputInstitucion = document.createElement('input');
        inputInstitucion.type = 'hidden';
        inputInstitucion.name = 'instituciones_apoyo[]';
        inputInstitucion.value = institucionId;
        
        const inputEstacion = document.createElement('input');
        inputEstacion.type = 'hidden';
        inputEstacion.name = 'estaciones_apoyo[]';
        inputEstacion.value = estacionId || '';
        
        // Agregar a la lista y a los campos ocultos
        listaApoyo.appendChild(listItem);
        camposOcultosApoyo.appendChild(inputInstitucion);
        camposOcultosApoyo.appendChild(inputEstacion);
        
        // Limpiar selects
        institucionApoyoSelect.value = '';
        estacionApoyoSelect.innerHTML = '<option value="" selected>--Seleccione una estación--</option>';
        
        // Evento para eliminar
        listItem.querySelector('.btn-eliminar-apoyo').addEventListener('click', function() {
            // Eliminar el item de la lista
            listItem.remove();
            
            // Eliminar los campos ocultos correspondientes
            const inputs = Array.from(camposOcultosApoyo.querySelectorAll('input'));
            const index = inputs.findIndex(input => 
                input.name === 'instituciones_apoyo[]' && input.value === institucionId
            );
            
            if (index !== -1) {
                camposOcultosApoyo.removeChild(inputs[index]); // Institución
                camposOcultosApoyo.removeChild(inputs[index + 1]); // Estación (siguiente)
            }
        });
    });

    // ... resto del código existente ...
});
</script>


@endsection