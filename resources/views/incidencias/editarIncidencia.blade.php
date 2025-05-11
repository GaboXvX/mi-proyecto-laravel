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
                <livewire:dropdown 
                    :parroquia-id="$parroquiaActual->id_parroquia ?? null"
                    :urbanizacion-id="$urbanizacionActual->id_urbanizacion ?? null"
                    :sector-id="$sectorActual->id_sector ?? null"
                    :comunidad-id="$comunidadActual->id_comunidad ?? null"/>
                    
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" id="calle" name="calle" class="form-control" value="{{ $incidencia->direccion->calle }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="punto_de_referencia" class="form-label">Punto de Referencia:</label>
                            <input type="text" id="punto_de_referencia" name="punto_de_referencia" class="form-control" value="{{ $incidencia->direccion->punto_de_referencia }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end m-3">
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
                               
                                <option value="{{ $tipo->id_tipo_incidencia }}">{{ $tipo->nombre }} </option>
                                  
                               
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
                                <option value="{{ $prioridad->id_nivel_incidencia }}"
                                    {{ $incidencia->id_nivel_incidencia == $prioridad->id_nivel_incidencia ? 'selected' : '' }}>
                                    {{ $prioridad->nivel }}/{{$prioridad->nombre}}
                                </option>
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
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

            // Cerrar el loader inmediatamente al recibir respuesta
            await swalInstance.close();

            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La respuesta no es JSON válido');
            }

            const result = await response.json();

            if (!response.ok || !result.success) {
                // Si el servidor devuelve un error (400, 500, etc.) o success=false
                throw new Error(result.message || `Error en la operación: ${response.statusText}`);
            }

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
                // Recargar como fallback
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