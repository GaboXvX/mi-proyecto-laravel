@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="table-container" style="width: 100%; max-width: 600px;">
        <h2 class="mb-4">Crear Nuevo Personal de Reparación</h2>
        
        <form id="personalReparacionForm" action="{{ route('personal-reparacion.store') }}" method="POST">
            @csrf
            
            <div class="row g-3 justify-content-center">
                <!-- Nacionalidad y Cédula -->
                
                
                <div class="col-md-6">
                    <label for="cedula"><span style="color: red;" class="me-2">*</span>Cédula</label>
                    <input type="text" name="cedula" id="cedula" class="form-control solo-numeros" maxlength="8" required>
                    <div id="cedulaFeedback" class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="nacionalidad">Nacionalidad</label>
                    <select name="nacionalidad" id="nacionalidad" class="form-select" required>
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="V">Venezolano (V)</option>
                        <option value="E">Extranjero (E)</option>
                    </select>
                </div>
                <!-- Institución y Estación -->
                <div class="col-md-6">
                    <label for="id_institucion"><span style="color: red;" class="me-2">*</span>Institución</label>
                    <select name="id_institucion" id="id_institucion" class="form-select" required>
                        <option value="">Seleccione una institución</option>
                        @foreach($instituciones as $institucion)
                            <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="id_institucion_estacion"><span style="color: red;" class="me-2">*</span>Estación</label>
                    <select name="id_institucion_estacion" id="id_institucion_estacion" class="form-select" required disabled>
                        <option value="">Primero seleccione una institución</option>
                    </select>
                </div>
                
                <!-- Nombre y Apellido -->
                <div class="col-md-6">
                    <label for="nombre"><span style="color: red;" class="me-2">*</span>Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control solo-letras" maxlength="12" required> 
                </div>
                
                <div class="col-md-6">
                    <label for="apellido"><span style="color: red;" class="me-2">*</span>Apellido</label>
                    <input type="text" name="apellido" id="apellido" class="form-control solo-letras" maxlength="12" required>
                </div>

                <!-- Teléfono y Género -->
                <div class="col-md-6"> 
                    <label for="telefono"><span style="color: red;" class="me-2">*</span>Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control solo-numeros" maxlength="11" required>
                </div>

                <div class="col-md-6">
                    <label for="genero">Género</label>
                    <select name="genero" id="genero" class="form-select" required>
                        <option value="" selected disabled>Seleccione...</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                
                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('personal-reparacion.index') }}" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary px-4" disabled>
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
    // Letras solamente (nombre, apellido, etc.)
    const letraInputs = document.querySelectorAll('.solo-letras');
    letraInputs.forEach(input => {
        input.addEventListener('input', function () {
            // Reemplaza todo lo que no sea letra o espacio
            this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');

            // Respeta el maxlength
            const maxLength = this.getAttribute('maxlength');
            if (maxLength && this.value.length > maxLength) {
                this.value = this.value.slice(0, maxLength);
            }
        });
    });

    // Números solamente (cédula, teléfono, etc.)
    const numeroInputs = document.querySelectorAll('.solo-numeros');
    numeroInputs.forEach(input => {
        input.addEventListener('input', function () {
            // Reemplaza todo lo que no sea número
            this.value = this.value.replace(/[^0-9]/g, '');

            // Respeta el maxlength
            const maxLength = this.getAttribute('maxlength');
            if (maxLength && this.value.length > maxLength) {
                this.value = this.value.slice(0, maxLength);
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('personalReparacionForm');
    const institucionSelect = document.getElementById('id_institucion');
    const estacionSelect = document.getElementById('id_institucion_estacion');
    const nacionalidadSelect = document.getElementById('nacionalidad');
    const cedulaInput = document.getElementById('cedula');
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const telefonoInput = document.getElementById('telefono');
    const generoSelect = document.getElementById('genero');
    const cedulaFeedback = document.getElementById('cedulaFeedback');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');

    let personalEncontrado = false;
    let cedulaAnteriorRegistrada = false;

    // Función para bloquear campos
    function bloquearCampos() {
        nombreInput.readOnly = true;
        apellidoInput.readOnly = true;
        telefonoInput.readOnly = true;
        generoSelect.disabled = true;
        institucionSelect.disabled = true;
        estacionSelect.disabled = true;
        nacionalidadSelect.disabled = true;
        submitBtn.disabled = true;
    }

    // Función para desbloquear campos
    function desbloquearCampos() {
        nombreInput.readOnly = false;
        apellidoInput.readOnly = false;
        telefonoInput.readOnly = false;
        generoSelect.disabled = false;
        institucionSelect.disabled = false;
        nacionalidadSelect.disabled = false;
        estacionSelect.disabled = institucionSelect.value === '';
        validarFormulario();
    }

    // Función para limpiar campos solo si se encontraba un empleado registrado previamente
    function limpiarCamposSiEsNecesario() {
        if (cedulaAnteriorRegistrada) {
            nombreInput.value = '';
            apellidoInput.value = '';
            telefonoInput.value = '';
            generoSelect.value = '';
            institucionSelect.value = '';
            estacionSelect.innerHTML = '<option value="">Primero seleccione una institución</option>';
            estacionSelect.disabled = true;
            nacionalidadSelect.value = '';
            personalEncontrado = false;
            desbloquearCampos();
        }
        cedulaAnteriorRegistrada = false;
    }

    // Función para validar si todos los campos están llenos
    function validarFormulario() {
        const camposRequeridos = [
            nacionalidadSelect,
            cedulaInput,
            institucionSelect,
            estacionSelect,
            nombreInput,
            apellidoInput,
            telefonoInput,
            generoSelect
        ];

        const todosLlenos = camposRequeridos.every(campo => {
            if (campo.tagName === 'SELECT') {
                return campo.value !== '' && campo.value !== null;
            } else {
                return campo.value.trim() !== '';
            }
        });

        // Solo habilitar el botón si todos los campos están llenos y la cédula es válida
        submitBtn.disabled = !todosLlenos || cedulaInput.classList.contains('is-invalid');
    }

    // Función para buscar personal por cédula (solo número)
    function buscarPersonalPorCedula() {
        const cedula = cedulaInput.value.trim();
        
        if (!cedula || cedula.length < 7) {
            return;
        }

        // Mostrar loading
        cedulaFeedback.textContent = 'Buscando personal...';
        cedulaFeedback.classList.add('text-warning');
        
        fetch(`/personal-reparacion/buscar/${cedula}`, {
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
            console.log('Respuesta de búsqueda:', data);
            
            if (data && data.empleado) {
                // Personal encontrado, llenar los campos
                personalEncontrado = true;
                cedulaAnteriorRegistrada = true;
                
                nacionalidadSelect.value = data.empleado.nacionalidad || '';
                nombreInput.value = data.empleado.nombre || '';
                apellidoInput.value = data.empleado.apellido || '';
                telefonoInput.value = data.empleado.telefono || '';
                generoSelect.value = data.empleado.genero || '';
                
                if (data.empleado.id_institucion) {
                    institucionSelect.value = data.empleado.id_institucion;
                    cargarEstaciones(data.empleado.id_institucion, () => {
                        if (data.empleado.id_institucion_estacion) {
                            estacionSelect.value = data.empleado.id_institucion_estacion;
                        }
                        bloquearCampos();
                    });
                } else {
                    bloquearCampos();
                }
                
                cedulaFeedback.textContent = 'Personal encontrado. Campos bloqueados para edición.';
                cedulaFeedback.classList.remove('text-warning');
                cedulaFeedback.classList.add('text-success');
            } else {
                personalEncontrado = false;
                limpiarCamposSiEsNecesario();
                cedulaFeedback.textContent = data.message || 'No se encontró personal con esta cédula';
                cedulaFeedback.classList.remove('text-warning');
                cedulaFeedback.classList.add('text-info');
                validarFormulario();
            }
        })
        .catch(error => {
            console.error('Error al buscar personal:', error);
            personalEncontrado = false;
            limpiarCamposSiEsNecesario();
            cedulaFeedback.textContent = 'Error al buscar personal';
            cedulaFeedback.classList.remove('text-warning');
            cedulaFeedback.classList.add('text-danger');
            validarFormulario();
        });
    }

    // Función para cargar estaciones con callback
    function cargarEstaciones(institucionId, callback = null) {
        if (!institucionId) {
            estacionSelect.innerHTML = '<option value="">Primero seleccione una institución</option>';
            estacionSelect.disabled = true;
            if (callback) callback();
            validarFormulario();
            return;
        }

        estacionSelect.disabled = true;
        estacionSelect.innerHTML = '<option value="">Cargando estaciones...</option>';

        fetch(`/personal-reparacion/estaciones/${institucionId}`)
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
                    estacionSelect.disabled = personalEncontrado;
                } else {
                    estacionSelect.innerHTML = '<option value="">No hay estaciones disponibles</option>';
                }
                
                if (callback) callback();
                validarFormulario();
            })
            .catch(error => {
                console.error('Error al cargar estaciones:', error);
                estacionSelect.innerHTML = '<option value="">Error al cargar estaciones</option>';
                if (callback) callback();
                validarFormulario();
            });
    }

    // Función para validar cédula (solo número)
    function validarCedula() {
        const cedula = cedulaInput.value.trim();
        const cedulaLength = cedula.length;
        
        // Resetear estado
        cedulaInput.classList.remove('is-invalid', 'is-valid');
        cedulaFeedback.textContent = '';
        cedulaFeedback.classList.remove('text-warning', 'text-danger', 'text-success');
        
        // Si la cédula tiene entre 7-8 dígitos, validar
        if (cedulaLength >= 7 && cedulaLength <= 8) {
            fetch(`/validar-cedula/${cedula}`, {
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
                if (data.exists) {
                    // Cédula existe
                    cedulaAnteriorRegistrada = true;
                    cedulaInput.classList.add('is-invalid');
                    cedulaFeedback.textContent = data.message || 'Esta cédula ya está registrada';
                    cedulaFeedback.classList.add('text-danger');
                    submitBtn.disabled = true;
                    
                    // Buscar los datos del personal
                    buscarPersonalPorCedula();
                } else {
                    // Cédula no existe - disponible
                    limpiarCamposSiEsNecesario();
                    cedulaInput.classList.add('is-valid');
                    cedulaFeedback.innerHTML = '<i class="fas fa-check"></i> Cédula disponible para registro';
                    cedulaFeedback.classList.add('text-success');
                    validarFormulario();
                }
            })
            .catch(error => {
                console.error('Error al validar cédula:', error);
                cedulaFeedback.textContent = 'Error al validar cédula';
                cedulaFeedback.classList.add('text-danger');
                validarFormulario();
            });
        } else if (cedulaLength < 7 && cedulaLength > 0) {
            // Cédula muy corta
            limpiarCamposSiEsNecesario();
            cedulaFeedback.textContent = 'La cédula debe tener al menos 7 dígitos';
            cedulaFeedback.classList.add('text-warning');
            validarFormulario();
        } else if (cedulaLength === 0) {
            // Cédula vacía
            limpiarCamposSiEsNecesario();
            validarFormulario();
        }
    }

    // Event listeners para validar el formulario cuando cambian los campos
    [nacionalidadSelect, cedulaInput, institucionSelect, estacionSelect, 
     nombreInput, apellidoInput, telefonoInput, generoSelect].forEach(element => {
        element.addEventListener('change', validarFormulario);
        element.addEventListener('input', validarFormulario);
    });

    // Event listeners
    institucionSelect.addEventListener('change', () => {
        cargarEstaciones(institucionSelect.value);
        // Solo deshabilitar estación si no hay institución seleccionada
        estacionSelect.disabled = institucionSelect.value === '';
    });
    
    cedulaInput.addEventListener('input', function() {
        // Solo permitir números
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Validar en cada cambio cuando tenga entre 7-8 dígitos
        if (this.value.length >= 7 && this.value.length <= 8) {
            validarCedula();
        } else {
            // Si se borró un dígito y quedó con 6 o menos, limpiar validación
            if (this.value.length < 7) {
                limpiarCamposSiEsNecesario();
                personalEncontrado = false;
                cedulaInput.classList.remove('is-invalid', 'is-valid');
                cedulaFeedback.textContent = this.value.length > 0 ? 
                    'La cédula debe tener al menos 7 dígitos' : '';
                cedulaFeedback.classList.remove('text-warning', 'text-danger', 'text-success');
                validarFormulario();
                // Asegurar que los selects se desbloqueen
                institucionSelect.disabled = false;
                estacionSelect.disabled = institucionSelect.value === '';
            }
        }
    });
    
    cedulaInput.addEventListener('blur', validarCedula);

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (personalEncontrado) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se puede registrar un personal que ya existe'
            });
            return;
        }
        
        submitText.textContent = 'Procesando...';
        submitSpinner.classList.remove('d-none');
        submitBtn.disabled = true;

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonText: 'Ir al listado',
                }).then(() => {
                    window.location.href = data.redirect || '{{ route("personal-reparacion.index") }}';
                });
            } else {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error', 
                    text: data.message || 'Error al registrar el personal' 
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ 
                icon: 'error', 
                title: 'Error', 
                text: 'No se pudo registrar el personal.' 
            });
        })
        .finally(() => {
            submitText.textContent = 'Guardar';
            submitSpinner.classList.add('d-none');
            submitBtn.disabled = false;
        });
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
    .is-valid {
        border-color: #28a745;
    }
    #submitSpinner {
        margin-left: 8px;
    }
    .form-control:read-only {
        background-color: #e9ecef;
        opacity: 1;
    }
    .form-select:disabled {
        background-color: #e9ecef;
        opacity: 1;
    }
    .fa-check {
        color: #28a745;
        margin-right: 5px;
    }
    .is-valid {
        border-color: #28a745;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
</style>
@endsection