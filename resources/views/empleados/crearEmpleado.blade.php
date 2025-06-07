@extends('layouts.app')
@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="table-container" style="width: 100%; max-width: 600px;">
        <h2 class="text-center">Registrar Empleado Autorizado</h2>
        <form action="{{ route('empleados.store') }}" method="POST" id="empleadoForm">
            @csrf
            <div class="row g-2 mb-3">
                <div class="col-md-8">
                    <label for="cedula" class="form-label"><span style="color: red;" class="me-2">*</span>Cédula</label>
                    <input type="text" name="cedula" id="cedula" class="form-control solo-numeros" required value="{{ old('cedula') }}" maxlength="8">
                    <span id="cedulaStatus" class="text-muted" style="display:none;"></span>
                    <span id="cedulaError" class="text-danger" style="display:none;"></span>
                </div>
                <div class="col-md-4">
                    <label for="nacionalidad" class="form-label"><span style="color: red;" class="me-2">*</span>Nacionalidad</label>
                    <select name="nacionalidad" id="nacionalidad" class="form-control" required>
                        <option value="V" {{ old('nacionalidad', 'V') == 'V' ? 'selected' : '' }}>V</option>
                        <option value="E" {{ old('nacionalidad', 'V') == 'E' ? 'selected' : '' }}>E</option>
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label"><span style="color: red;" class="me-2">*</span>Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control solo-letras" maxlength="12" required value="{{ old('nombre') }}">
                </div>
                <div class="col-md-6">
                    <label for="apellido" class="form-label"><span style="color: red;" class="me-2">*</span>Apellido</label>
                    <input type="text" name="apellido" id="apellido" class="form-control solo-letras" maxlength="12" required value="{{ old('apellido') }}">
                </div>
            </div>
            
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="cargo_id" class="form-label"><span style="color: red;" class="me-2">*</span>Cargo</label>
                    <select name="cargo_id" id="cargo_id" class="form-control" required>
                        <option value="">Seleccione un cargo</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id_cargo }}" {{ old('cargo_id') == $cargo->id_cargo ? 'selected' : '' }}>{{ $cargo->nombre_cargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="genero" class="form-label"><span style="color: red;" class="me-2">*</span>Género</label>
                    <select name="genero" id="genero" class="form-control" required>
                        <option value="">Seleccione un género</option>
                        <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </div>
           
            <div class="mb-3">
                <label for="telefono" class="form-label"><span style="color: red;" class="me-2">*</span>Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control solo-numeros" maxlength="11" required value="{{ old('telefono') }}">
            </div>
           
            <div class="d-flex justify-content-between">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success" id="registrarBtn">Registrar</button>
            </div>
        </form>
        <span class="text-muted d-flex justify-content-center">Los campos señalados con * deben ser rellenados</span>
    </div>
</div>

<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
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
document.addEventListener('DOMContentLoaded', function() {
    const cedulaInput = document.getElementById('cedula');
    const nacionalidadInput = document.getElementById('nacionalidad');
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const generoInput = document.getElementById('genero');
    const telefonoInput = document.getElementById('telefono');
    const cargoInput = document.getElementById('cargo_id');
    const registrarBtn = document.getElementById('registrarBtn');
    const cedulaStatus = document.getElementById('cedulaStatus');
    const cedulaError = document.getElementById('cedulaError');
    const form = document.getElementById('empleadoForm');

    let empleadoEncontrado = false;
    let ultimaCedulaConsultada = null;
    let longitudAnteriorCedula = 0;

    // Función para verificar si todos los campos requeridos están llenos
    function verificarCamposVacios() {
        const camposRequeridos = [
            cedulaInput,
            nombreInput,
            apellidoInput,
            generoInput,
            telefonoInput,
            cargoInput
        ];
        
        return camposRequeridos.every(campo => campo.value.trim() !== '');
    }

    // Función para verificar si la cédula tiene longitud válida
    function verificarLongitudCedula() {
        return cedulaInput.value.length >= 7 && cedulaInput.value.length <= 8;
    }

    // Función para actualizar el estado del botón de registro
    function actualizarEstadoBoton() {
        const camposCompletos = verificarCamposVacios();
        const cedulaValida = verificarLongitudCedula();
        
        registrarBtn.disabled = empleadoEncontrado || !camposCompletos || !cedulaValida;
        
        if (registrarBtn.disabled) {
            registrarBtn.classList.remove('btn-success');
            registrarBtn.classList.add('btn-secondary');
        } else {
            registrarBtn.classList.remove('btn-secondary');
            registrarBtn.classList.add('btn-success');
        }
    }

    // Asegurar que la nacionalidad tenga un valor válido al cargar
    if (!nacionalidadInput.value) {
        nacionalidadInput.value = 'V';
    }

    // Validación para solo letras en nombre y apellido
    nombreInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        actualizarEstadoBoton();
    });

    apellidoInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        actualizarEstadoBoton();
    });

    // Escuchar cambios en todos los campos requeridos
    [cedulaInput, generoInput, telefonoInput, cargoInput].forEach(input => {
        input.addEventListener('input', actualizarEstadoBoton);
        input.addEventListener('change', actualizarEstadoBoton);
    });

    nacionalidadInput.addEventListener('change', function() {
        // Si cambia la nacionalidad, resetear la verificación de cédula
        ultimaCedulaConsultada = null;
        if (cedulaInput.value.length >= 7) {
            cedulaInput.dispatchEvent(new Event('input'));
        }
        actualizarEstadoBoton();
    });

    function bloquearCamposEmpleado(data) {
        nombreInput.value = data.nombre;
        apellidoInput.value = data.apellido;
        generoInput.value = data.genero;
        telefonoInput.value = data.telefono;
        cargoInput.value = data.id_cargo;
        nacionalidadInput.value = data.nacionalidad;
        
        nombreInput.disabled = true;
        apellidoInput.disabled = true;
        generoInput.disabled = true;
        telefonoInput.disabled = true;
        cargoInput.disabled = true;
        nacionalidadInput.disabled = true;
        
        empleadoEncontrado = true;
        actualizarEstadoBoton();
        
        cedulaError.style.display = 'inline';
        cedulaError.textContent = 'La cédula ya está registrada. No puedes registrar este empleado nuevamente.';
        cedulaStatus.style.display = 'none';
        cedulaInput.classList.remove('is-valid');
    }

    function desbloquearCamposEmpleado(limpiar = false) {
        if (limpiar) {
            nombreInput.value = '';
            apellidoInput.value = '';
            generoInput.value = '';
            telefonoInput.value = '';
            cargoInput.value = '';
        }
        
        nombreInput.disabled = false;
        apellidoInput.disabled = false;
        generoInput.disabled = false;
        telefonoInput.disabled = false;
        cargoInput.disabled = false;
        nacionalidadInput.disabled = false;
        
        empleadoEncontrado = false;
        actualizarEstadoBoton();
        
        cedulaError.style.display = 'none';
        cedulaStatus.style.display = 'inline';
        cedulaStatus.textContent = 'Cédula válida';
        cedulaStatus.classList.remove('text-muted');
        cedulaStatus.classList.add('text-success');
        cedulaInput.classList.add('is-valid');
    }

    function resetearEstadoCedula() {
        cedulaInput.classList.remove('is-valid');
        cedulaStatus.style.display = 'inline';
        cedulaStatus.textContent = 'Ingrese al menos 7 dígitos';
        cedulaStatus.classList.remove('text-success');
        cedulaStatus.classList.add('text-muted');
        cedulaError.style.display = 'none';
        actualizarEstadoBoton();
    }

    // Verificar cédula al cambiar el input
    cedulaInput.addEventListener('input', function() {
        const cedula = cedulaInput.value.trim();
        const longitudActual = cedula.length;
        
        // Si se está borrando un dígito y previamente se había encontrado un empleado
        if (empleadoEncontrado && longitudActual < longitudAnteriorCedula) {
            desbloquearCamposEmpleado(true);
        }
        
        longitudAnteriorCedula = longitudActual;
        
        if (cedula.length === 0) {
            resetearEstadoCedula();
            ultimaCedulaConsultada = null;
            return;
        }

        // Validar formato de cédula
        if (!/^\d+$/.test(cedula)) {
            cedulaError.style.display = 'inline';
            cedulaError.textContent = 'La cédula debe contener solo números';
            resetearEstadoCedula();
            return;
        } else {
            cedulaError.style.display = 'none';
        }

        // Resetear estado si tiene menos de 7 dígitos
        if (cedula.length < 7) {
            resetearEstadoCedula();
            return;
        }

        // Solo consultar si la cédula ha cambiado
        if (cedula === ultimaCedulaConsultada) {
            return;
        }
        
        ultimaCedulaConsultada = cedula;

        // Mostrar que se está verificando
        cedulaStatus.style.display = 'inline';
        cedulaStatus.textContent = 'Verificando cédula...';
        cedulaStatus.classList.remove('text-success', 'text-muted');
        cedulaStatus.classList.add('text-info');

        // Consultar si la cédula existe
        fetch("{{ url('/empleados/verificar-cedula') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                cedula: cedula,
                nacionalidad: nacionalidadInput.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                bloquearCamposEmpleado(data.empleado);
            } else {
                desbloquearCamposEmpleado();
                cedulaStatus.textContent = 'Cédula disponible';
                cedulaStatus.classList.remove('text-info');
                cedulaStatus.classList.add('text-success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            cedulaStatus.style.display = 'none';
            cedulaError.style.display = 'inline';
            cedulaError.textContent = 'Error al verificar la cédula';
        });
    });

    // Resto del código (envío del formulario) se mantiene igual
    form.addEventListener('submit', function(e) {
        if (empleadoEncontrado) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No puedes registrar un empleado que ya existe',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Validar longitud de cédula antes de enviar
        if (!verificarLongitudCedula()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La cédula debe tener entre 7 y 8 dígitos',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Validar campos vacíos antes de enviar
        if (!verificarCamposVacios()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Todos los campos requeridos deben estar completos',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrando...';
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    let errorMessages = '';
                    for (const [field, errors] of Object.entries(data.errors)) {
                        errorMessages += errors.join('\n') + '\n';
                    }
                    throw new Error(errorMessages.trim());
                }
                throw new Error(data.message || 'Error al procesar la solicitud');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                return Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    showConfirmButton: true,
                    confirmButtonText: 'Ir a la lista',
                    allowOutsideClick: false
                });
            }
            throw new Error(data.message || 'Error desconocido');
        })
        .then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('usuarios.index') }}";
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Ocurrió un error al registrar el empleado',
                confirmButtonText: 'Entendido'
            });
        })
        .finally(() => {
            submitBtn.disabled = empleadoEncontrado || !verificarCamposVacios() || !verificarLongitudCedula();
            submitBtn.innerHTML = 'Registrar';
        });
    });

    // Inicializar estado del botón
    actualizarEstadoBoton();
});
</script>
@endsection