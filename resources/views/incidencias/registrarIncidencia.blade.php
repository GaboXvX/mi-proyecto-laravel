@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Registrar Incidencia</h2>

    <div id="alert-container"></div>

    <form id="form-registrar-incidencia" action="{{ route('incidencias.store') }}" method="POST">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <input type="hidden" name="id_persona" value="{{ $persona->id_persona }}" />

        <!-- Paso 1: Dirección -->
        <div class="step" id="step-1">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Dirección del Incidente</h5>
                </div>
                <div class="card-body">
                    <livewire:dropdown-persona/>

                    <div class="row mt-3">
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
            </div>
            <button type="button" class="btn btn-primary" id="next-to-step-2">Siguiente</button>
        </div>

        <!-- Paso 2: Institución y Estación -->
        <div class="step d-none" id="step-2">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Institución Responsable</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="institucion" class="form-label">Institución:</label>
                        <select id="institucion" name="institucion" class="form-select" required>
                            <option value="" disabled selected>--Seleccione una institución--</option>
                            @foreach ($instituciones as $institucion)
                                <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estacion" class="form-label">Estación:</label>
                        <select id="estacion" name="estacion" class="form-select" required>
                            <option value="" disabled selected>--Seleccione una estación--</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" id="back-to-step-1">Atrás</button>
            <button type="button" class="btn btn-primary" id="next-to-step-3">Siguiente</button>
        </div>

        <!-- Paso 3: Detalles de la Incidencia -->
        <div class="step d-none" id="step-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Detalles de la Incidencia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tipo_incidencia" class="form-label">Tipo de Incidencia:</label>
                        <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                            <option value="" disabled selected>--Seleccione--</option>
                            <option value="agua potable">Agua Potable</option>
                            <option value="agua servida">Agua Servida</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="nivel_prioridad" class="form-label">Nivel de Prioridad:</label>
                        <select id="nivel_prioridad" name="nivel_prioridad" class="form-select" required>
                            <option value="" disabled selected>--Seleccione--</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" id="back-to-step-2">Atrás</button>
            <button type="submit" class="btn btn-primary">Registrar Incidencia</button>
        </div>
    </form>
</div>

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
        const form = document.getElementById('form-registrar-incidencia');

        form.addEventListener('submit', async function (event) {
            event.preventDefault(); // Evitar el envío tradicional del formulario

            const formData = new FormData(form);
            const actionUrl = form.action;

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Mostrar mensaje de éxito con SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Redirigir al comprobante
                        window.location.href = result.redirect_url;
                    });
                } else {
                    // Mostrar mensaje de error con SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Ocurrió un error al registrar la incidencia.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            } catch (error) {
                console.error('Error al enviar el formulario:', error);
                // Mostrar mensaje de error inesperado con SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'Ocurrió un error inesperado. Intente nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
</script>
@endsection