@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="table-container" style="width: 100%; max-width: 600px;">
        <h2 class="mb-4">Crear Nuevo Personal de Reparación</h2>
        
        <form id="personalReparacionForm" action="{{ route('personal-reparacion.store') }}" method="POST">
            @csrf
            
            <div class="row g-3 justify-content-center">
                <!-- Institución y Estación -->
                <div class="col-md-6">
                    <label for="nacionalidad">Nacionalidad</label>
                    <select name="nacionalidad" id="nacionalidad" class="form-select" required>
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="V">Venezolano (V)</option>
                        <option value="E">Extranjero (E)</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="cedula">Cédula</label>
                    <input type="text" name="cedula" id="cedula" class="form-control solo-numeros" maxlength="8" required>
                    <div id="cedulaFeedback" class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="id_institucion">Institución</label>
                    <select name="id_institucion" id="id_institucion" class="form-select" required>
                        <option value="">Seleccione una institución</option>
                        @foreach($instituciones as $institucion)
                            <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="id_institucion_estacion">Estación</label>
                    <select name="id_institucion_estacion" id="id_institucion_estacion" class="form-select" required disabled>
                        <option value="">Primero seleccione una institución</option>
                    </select>
                </div>
                
                <!-- Nombre y Apellido -->
                <div class="col-md-6">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control solo-letras" maxlength="12" required> 
                </div>
                
                <div class="col-md-6">
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" id="apellido" class="form-control solo-letras" maxlength="12" required>
                    
                </div>

                <!-- Teléfono -->
                <div class="col-md-12"> 
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control solo-numeros" maxlength="11" required>
                </div>
                
                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('personal-reparacion.index') }}" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                        <span id="submitText">Guardar</span>
                        <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('personalReparacionForm');
    const institucionSelect = document.getElementById('id_institucion');
    const estacionSelect = document.getElementById('id_institucion_estacion');
    const nacionalidadSelect = document.getElementById('nacionalidad');
    const cedulaInput = document.getElementById('cedula');
    const cedulaFeedback = document.getElementById('cedulaFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');

    function resetCedulaValidation() {
        cedulaInput.classList.remove('is-invalid', 'is-valid');
        cedulaFeedback.textContent = '';
        cedulaFeedback.classList.remove('text-warning', 'text-danger', 'text-success');
        submitBtn.disabled = false;
    }

    function showValidationLoading() {
        cedulaFeedback.textContent = 'Validando cédula...';
        cedulaFeedback.classList.add('text-warning');
    }

    function handleValidationResult(exists, message) {
        if (exists) {
            cedulaInput.classList.add('is-invalid');
            cedulaFeedback.textContent = message;
            cedulaFeedback.classList.remove('text-warning');
            cedulaFeedback.classList.add('text-danger');
            submitBtn.disabled = true;
        } else {
            cedulaInput.classList.add('is-valid');
            cedulaFeedback.textContent = message;
            cedulaFeedback.classList.remove('text-warning');
            cedulaFeedback.classList.add('text-success');
        }
    }

    function showValidationError(message) {
        cedulaFeedback.textContent = message;
        cedulaFeedback.classList.remove('text-warning');
        cedulaFeedback.classList.add('text-danger');
        submitBtn.disabled = false;
    }

    function validarCedula() {
        const cedula = cedulaInput.value.trim();
        if (!cedula) return;

        resetCedulaValidation();
        showValidationLoading();

const url = `/validar-cedula/${encodeURIComponent(cedula)}`;
        console.log('Validando cédula con URL:', url);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta de validación:', data);
            if (!data.success) {
                throw new Error(data.message || 'Error inesperado');
            }
            handleValidationResult(data.exists, data.message);
        })
        .catch(error => {
            console.error('Error al validar cédula:', error);
            showValidationError('No se pudo verificar la cédula');
        });
    }

    function cargarEstaciones(institucionId) {
        if (!institucionId) {
            estacionSelect.innerHTML = '<option value="">Primero seleccione una institución</option>';
            estacionSelect.disabled = true;
            return;
        }

        estacionSelect.disabled = true;
        estacionSelect.innerHTML = '<option value="">Cargando estaciones...</option>';

        const url = `/personal-reparacion/estaciones/${institucionId}`;
        console.log('Cargando estaciones desde:', url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(({ success, data }) => {
                estacionSelect.innerHTML = '<option value="">Seleccione una estación</option>';

                if (success && data.length > 0) {
                    data.forEach(estacion => {
                        const nombre = estacion.codigo ? `${estacion.nombre} (${estacion.codigo})` : estacion.nombre;
                        const option = new Option(nombre, estacion.id);
                        estacionSelect.add(option);
                    });
                    estacionSelect.disabled = false;
                } else {
                    estacionSelect.innerHTML = '<option value="">No hay estaciones disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar estaciones:', error);
                estacionSelect.innerHTML = '<option value="">Error al cargar estaciones</option>';
            });
    }

    // Eventos
    institucionSelect.addEventListener('change', () => cargarEstaciones(institucionSelect.value));
    nacionalidadSelect.addEventListener('change', validarCedula);
    cedulaInput.addEventListener('input', function () {
        if (this.value.length >= 3) {
            validarCedula();
        }
    });
    cedulaInput.addEventListener('blur', validarCedula);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        validarCedula();

        setTimeout(() => {
            if (cedulaInput.classList.contains('is-invalid')) {
                cedulaInput.focus();
                return;
            }

            submitText.textContent = 'Procesando...';
            submitSpinner.classList.remove('d-none');
            submitBtn.disabled = true;

            form.submit();
        }, 300); // Esperar validación mínima
    });
});
</script>


<style>
    .form-floating {
        margin-bottom: 1rem;
    }
    .is-invalid {
        border-color: #dc3545;
    }
    #submitSpinner {
        margin-left: 8px;
    }
</style>
@endsection