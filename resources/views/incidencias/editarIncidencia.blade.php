@extends('layouts.app')

@section('content')
<div class="table-container mt-5">
    <h2>Editar Incidencia</h2>

    <div id="alert-container"></div>

    <form id="form-editar-incidencia" action="{{ route('incidencias.update', $incidencia->slug) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id_persona" id="id_persona" value="{{ $incidencia->id_persona }}">
        
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
                    <livewire:dropdown-persona 
                        :estadoId="$incidencia->direccionIncidencia->id_estado"
                        :municipioId="$incidencia->direccionIncidencia->id_municipio"
                        :parroquiaId="$incidencia->direccionIncidencia->id_parroquia"
                        :urbanizacionId="$incidencia->direccionIncidencia->id_urbanizacion ?? null"
                        :sectorId="$incidencia->direccionIncidencia->id_sector ?? null"
                        :comunidadId="$incidencia->direccionIncidencia->id_comunidad ?? null"
                    />
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" id="calle" name="calle" class="form-control" value="{{ $incidencia->direccionIncidencia->calle }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="punto_de_referencia" class="form-label">Punto de Referencia:</label>
                            <input type="text" id="punto_de_referencia" name="punto_de_referencia" class="form-control" value="{{ $incidencia->direccionIncidencia->punto_de_referencia }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                    <a href="{{ route('incidencias.index') }}" class="btn btn-secondary btn-sm py-2 px-3">
                        Cancelar
                    </a>
                    <button type="button" class="btn btn-primary" id="next-to-step-2">Siguiente</button>
                </div>
            </div>
        </div>

        <!-- Paso 2: Institución y Estación -->
        <div class="step d-none" id="step-2">
            <div class="card border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Institución Responsable</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="institucion" class="form-label">Institución:</label>
                        <select id="institucion" name="institucion" class="form-select" required>
                            <option value="" disabled>--Seleccione una institución--</option>
                            @foreach ($instituciones as $institucion)
                                <option value="{{ $institucion->id_institucion }}" 
                                    {{ $incidencia->id_institucion == $institucion->id_institucion ? 'selected' : '' }}>
                                    {{ $institucion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estacion" class="form-label">Estación:</label>
                        <select id="estacion" name="estacion" class="form-select" required>
                            <option value="" disabled>--Seleccione una estación--</option>
                            @foreach ($estaciones as $estacion)
                                <option value="{{ $estacion->id_institucion_estacion }}" 
                                    {{ $incidencia->id_institucion_estacion == $estacion->id_institucion_estacion ? 'selected' : '' }}>
                                    {{ $estacion->nombre }} (Municipio: {{ $estacion->municipio->nombre }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sección de instituciones de apoyo -->
                    <div class="mb-3">
                        <button type="button" id="btn-agregar-apoyo" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo
                        </button>
                    </div>

                    <div id="contenedor-apoyo" class="{{ $incidencia->institucionesApoyo->isEmpty() ? 'd-none' : '' }}">
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
                            <h6>Instituciones de Apoyo:</h6>
                            <div id="lista-apoyo" class="list-group">
                                <!-- Instituciones existentes (no se pueden eliminar) -->
                                @foreach($incidencia->institucionesApoyo as $apoyo)
                                    <div class="list-group-item d-flex justify-content-between align-items-center" 
                                         data-institucion-id="{{ $apoyo->id_institucion }}" 
                                         data-estacion-id="{{ $apoyo->id_institucion_estacion }}"
                                         data-existente="true">
                                        <div>
                                            <strong>{{ $apoyo->institucion->nombre }}</strong> - 
                                            {{ $apoyo->estacion ? $apoyo->estacion->nombre.' ('.$apoyo->estacion->municipio->nombre.')' : 'Todas las estaciones' }}
                                        </div>
                                        <span class="badge bg-secondary">Existente</span>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Campos ocultos para enviar los datos -->
                            <div id="campos-ocultos-apoyo">
                                @foreach($incidencia->institucionesApoyo as $apoyo)
                                    <input type="hidden" name="instituciones_apoyo[]" value="{{ $apoyo->id_institucion }}">
                                    <input type="hidden" name="estaciones_apoyo[]" value="{{ $apoyo->id_institucion_estacion ?? '' }}">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                    <button type="button" class="btn btn-secondary" id="back-to-step-1">Atrás</button>
                    <button type="button" class="btn btn-primary" id="next-to-step-3">Siguiente</button>
                </div>
            </div>
        </div>

        <!-- Paso 3: Detalles de la Incidencia -->
        <div class="step d-none" id="step-3">
            <div class="card border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Detalles de la Incidencia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tipo_incidencia" class="form-label">Tipo de Incidencia:</label>
                        <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                            <option value="" disabled>--Seleccione--</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_incidencia }}"
                                    {{ $incidencia->id_tipo_incidencia == $tipo->id_tipo_incidencia ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required>{{ $incidencia->descripcion }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="nivel_prioridad" class="form-label">Nivel de Prioridad:</label>
                        <select id="nivel_prioridad" name="nivel_prioridad" class="form-select" required>
                            @foreach($prioridades as $prioridad)
                             @if($prioridad->activo == 1)
                                <option value="{{ $prioridad->id_nivel_incidencia }}"

                                    {{ $incidencia->id_nivel_incidencia == $prioridad->id_nivel_incidencia ? 'selected' : '' }}>
                                    {{ $prioridad->nivel }}/{{$prioridad->nombre}}
                                </option>
                                @endif
                
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                    <button type="button" class="btn btn-secondary btn-sm" id="back-to-step-2">Atrás</button>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('js/incidencias.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Navegación entre pasos
    const steps = document.querySelectorAll('.step');
    const nextToStep2 = document.getElementById('next-to-step-2');
    const nextToStep3 = document.getElementById('next-to-step-3');
    const backToStep1 = document.getElementById('back-to-step-1');
    const backToStep2 = document.getElementById('back-to-step-2');

    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('d-none', index !== stepIndex);
        });
        
        // Actualizar indicador de pasos
        document.querySelectorAll('#stepIndicator .nav-link').forEach((link, index) => {
            if (index < stepIndex + 1) {
                link.classList.remove('disabled');
                link.classList.add('active');
            } else {
                link.classList.add('disabled');
                link.classList.remove('active');
            }
        });
    }

    nextToStep2.addEventListener('click', () => showStep(1));
    nextToStep3.addEventListener('click', () => showStep(2));
    backToStep1.addEventListener('click', () => showStep(0));
    backToStep2.addEventListener('click', () => showStep(1));

    // Mostrar/ocultar sección de apoyo
    const btnAgregarApoyo = document.getElementById('btn-agregar-apoyo');
    const contenedorApoyo = document.getElementById('contenedor-apoyo');
    
    // Mostrar sección si ya hay instituciones de apoyo
    if (document.getElementById('lista-apoyo').children.length > 0) {
        btnAgregarApoyo.innerHTML = '<i class="bi bi-dash-circle"></i> Ocultar';
    }
    
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
            Swal.fire({
                icon: 'warning',
                title: 'Seleccione una institución',
                text: 'Debe seleccionar una institución antes de agregar',
                confirmButtonText: 'Entendido'
            });
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
            Swal.fire({
                icon: 'warning',
                title: 'Institución ya agregada',
                text: 'Esta combinación de institución y estación ya fue agregada',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        // Crear elemento de lista
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.setAttribute('data-institucion-id', institucionId);
        listItem.setAttribute('data-estacion-id', estacionId);
        listItem.setAttribute('data-nueva', 'true');
        
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
        
        // Mostrar contenedor si estaba oculto
        if (contenedorApoyo.classList.contains('d-none')) {
            contenedorApoyo.classList.remove('d-none');
            btnAgregarApoyo.innerHTML = '<i class="bi bi-dash-circle"></i> Ocultar';
        }
        
        // Evento para eliminar (solo para nuevas instituciones)
        listItem.querySelector('.btn-eliminar-apoyo').addEventListener('click', function() {
            // Verificar si es una institución existente
            if (listItem.getAttribute('data-existente') === 'true') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Acción no permitida',
                    text: 'No se pueden eliminar instituciones de apoyo existentes',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
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
            
            // Ocultar contenedor si no hay más elementos
            if (listaApoyo.children.length === 0) {
                contenedorApoyo.classList.add('d-none');
                btnAgregarApoyo.innerHTML = '<i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo';
            }
        });
    });

    // Configurar eventos de eliminación para las instituciones de apoyo existentes
    document.querySelectorAll('.btn-eliminar-apoyo').forEach(btn => {
        btn.addEventListener('click', function() {
            const listItem = this.closest('.list-group-item');
            
            // Verificar si es una institución existente
            if (listItem.getAttribute('data-existente') === 'true') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Acción no permitida',
                    text: 'No se pueden eliminar instituciones de apoyo existentes',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            const institucionId = listItem.getAttribute('data-institucion-id');
            
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
            
            // Ocultar contenedor si no hay más elementos
            if (listaApoyo.children.length === 0) {
                contenedorApoyo.classList.add('d-none');
                btnAgregarApoyo.innerHTML = '<i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo';
            }
        });
    });

    // Envío del formulario con manejo de duplicados
    const form = document.getElementById('form-editar-incidencia');

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        // Mostrar loader
        const swalInstance = Swal.fire({
            title: 'Procesando',
            html: 'Actualizando la incidencia...',
            allowOutsideClick: false,
            showConfirmButton: false,
            allowEscapeKey: false,
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                // Si es un error de duplicado
                if (result.is_duplicate) {
                    await swalInstance.close();
                    
                    // Mostrar modal con detalles del duplicado
                    const { value: accept } = await Swal.fire({
                        title: 'Incidencia Duplicada',
                        html: `
                            <div class="text-start">
                                <p>${result.message}</p>
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Detalles de la incidencia existente:</h6>
                                        <p><strong>Código:</strong> ${result.duplicate_data.codigo}</p>
                                        <p><strong>Descripción:</strong> ${result.duplicate_data.descripcion}</p>
                                        <p><strong>Fecha:</strong> ${result.duplicate_data.fecha_creacion}</p>
                                        <p><strong>Estado:</strong> ${result.duplicate_data.estado}</p>
                                        <p><strong>Prioridad:</strong> ${result.duplicate_data.prioridad}</p>
                                        <a href="${result.ver_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            Ver incidencia existente
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Forzar actualización',
                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        allowOutsideClick: false
                    });

                    if (accept) {
                        // Agregar campo hidden para forzar el registro
                        const forceInput = document.createElement('input');
                        forceInput.type = 'hidden';
                        forceInput.name = 'force_register';
                        forceInput.value = '1';
                        form.appendChild(forceInput);
                        
                        // Reenviar el formulario
                        form.submit();
                    }
                    return;
                }
                
                throw new Error(result.message || `Error en la operación: ${response.statusText}`);
            }

            // Cerrar el loader
            await swalInstance.close();

            // Mostrar mensaje de éxito y redirigir
            await Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: result.message,
                showConfirmButton: true,
                confirmButtonText: 'Aceptar',
                timer: 3000,
                timerProgressBar: true
            });

            // Redirigir después del mensaje
            if (result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                window.location.reload();
            }

        } catch (error) {
            // Cerrar el loader si está abierto
            if (swalInstance.isActive()) {
                swalInstance.close();
            }
            
            console.error('Error completo:', error);
            
            let errorMessage = 'Ocurrió un error al procesar la solicitud';
            
            if (error instanceof SyntaxError) {
                errorMessage = 'Error al interpretar la respuesta del servidor';
            } else if (error.message) {
                errorMessage = error.message;
            }

            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Entendido',
                allowOutsideClick: false
            });
        }
    });
});
</script>
@endsection