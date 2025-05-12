@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Nuevo Personal de Reparación</h1>
    
    <form id="personalReparacionForm" action="{{ route('personal-reparacion.store') }}" method="POST">
        @csrf
         
        <div class="row g-3">
            <!-- Institución y Estación -->
            <div class="col-md-6">
                <div class="form-floating">
                    <select name="id_institucion" id="id_institucion" class="form-select" required>
                        <option value="">Seleccione una institución</option>
                        @foreach($instituciones as $institucion)
                            <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                        @endforeach
                    </select>
                    <label for="id_institucion">Institución</label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-floating">
                    <select name="id_institucion_estacion" id="id_institucion_estacion" class="form-select" required disabled>
                        <option value="">Primero seleccione una institución</option>
                    </select>
                    <label for="id_institucion_estacion">Estación</label>
                </div>
            </div>
            
            <!-- Nombre y Apellido -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                    <label for="nombre">Nombre</label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="apellido" id="apellido" class="form-control" required>
                    <label for="apellido">Apellido</label>
                </div>
            </div>
            
            <!-- Nacionalidad y Cédula -->
            <div class="col-md-6">
                <div class="form-floating">
                    <select name="nacionalidad" id="nacionalidad" class="form-select" required>
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="V">Venezolano (V)</option>
                        <option value="E">Extranjero (E)</option>
                    </select>
                    <label for="nacionalidad">Nacionalidad</label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="cedula" id="cedula" class="form-control" required>
                    <label for="cedula">Cédula</label>
                    <div id="cedulaFeedback" class="invalid-feedback"></div>
                </div>
            </div>
            
            <!-- Teléfono -->
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" name="telefono" id="telefono" class="form-control" required>
                    <label for="telefono">Teléfono</label>
                </div>
            </div>
            
            <!-- Botones -->
            <div class="col-12 mt-4">
                <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                    <span id="submitText">Guardar</span>
                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
                <a href="{{ route('personal-reparacion.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
            </div>
        </div>
    </form>
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