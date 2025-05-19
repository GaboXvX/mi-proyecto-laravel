@extends('layouts.app')

@section('content')
<div class="table-container mt-5">
    <h2 class="mb-4">Registrar Incidencia General</h2>

    <div id="alert-container"></div>
    
    <form id="incidenciaGeneralForm" action="{{ route('incidencias.store') }}" method="POST">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
                <div class="d-flex justify-content-between m-3">
                    <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="button" class="btn btn-primary" id="next-to-step-2">
                        Siguiente <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>

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
                                <option value="{{ $prioridad->id_nivel_incidencia }}">{{ $prioridad->nivel }}/{{$prioridad->nombre}}</option>
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

    // Mostrar el paso actual y ocultar los demás
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('d-none', index !== stepIndex);
        });
    }

    // Navegación entre pasos
    nextToStep2.addEventListener('click', () => showStep(1));
    nextToStep3.addEventListener('click', () => showStep(2));
    backToStep1.addEventListener('click', () => showStep(0));
    backToStep2.addEventListener('click', () => showStep(1));

    const estadoSelect = document.getElementById('estado'); // Dropdown de estado
    const institucionSelect = document.getElementById('institucion'); // Dropdown de institución
    const estacionSelect = document.getElementById('estacion'); // Dropdown de estación

    // Función para cargar estaciones
    async function cargarEstaciones() {
        const estadoId = estadoSelect.value;
        const institucionId = institucionSelect.value;

        // Limpiar el selector de estaciones
        estacionSelect.innerHTML = '<option value="" disabled selected>--Seleccione una estación--</option>';

        if (!estadoId || !institucionId) return;

        try {
            const response = await fetch(`/instituciones-estaciones/estado/${estadoId}/institucion/${institucionId}`);
            const data = await response.json();

            if (data.success) {
                // Poblar el selector de estaciones
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

    // Escuchar cambios en los dropdowns de estado e institución
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
                        window.location.href = data.redirect_url; // Redirigir a la lista de incidencias
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
@endsection