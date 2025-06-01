@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Registrar Empleado Autorizado</h2>
    <form action="{{ route('empleados.store') }}" method="POST" id="empleadoForm">
        @csrf
        <div class="mb-3">
            <label for="cedula" class="form-label">Cédula</label>
            <input type="text" name="cedula" id="cedula" class="form-control" required value="{{ old('cedula') }}">
            <span id="cedulaStatus" class="text-success" style="display:none;"></span>
            <span id="cedulaError" class="text-danger" style="display:none;"></span>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre') }}">
        </div>
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" name="apellido" id="apellido" class="form-control" required value="{{ old('apellido') }}">
        </div>
        <div class="mb-3">
            <label for="cargo_id" class="form-label">Cargo</label>
            <select name="cargo_id" id="cargo_id" class="form-control" required>
                <option value="">Seleccione un cargo</option>
                @foreach($cargos as $cargo)
                    <option value="{{ $cargo->id_cargo }}" {{ old('cargo_id') == $cargo->id_cargo ? 'selected' : '' }}>{{ $cargo->nombre_cargo }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="genero" class="form-label">Género</label>
            <select name="genero" id="genero" class="form-control" required>
                <option value="">Seleccione un género</option>
                <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control" required value="{{ old('telefono') }}">
        </div>
        <button type="submit" class="btn btn-success" id="registrarBtn">Registrar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cedulaInput = document.getElementById('cedula');
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const generoInput = document.getElementById('genero');
    const telefonoInput = document.getElementById('telefono');
    const cargoInput = document.getElementById('cargo_id');
    const registrarBtn = document.getElementById('registrarBtn');
    const cedulaStatus = document.getElementById('cedulaStatus');
    const cedulaError = document.getElementById('cedulaError');

    let empleadoEncontrado = false;
    let ultimoEmpleadoBloqueado = null;

    function bloquearCamposEmpleado(data) {
        nombreInput.value = data.nombre;
        apellidoInput.value = data.apellido;
        generoInput.value = data.genero;
        telefonoInput.value = data.telefono;
        cargoInput.value = data.id_cargo;
        nombreInput.disabled = true;
        apellidoInput.disabled = true;
        generoInput.disabled = true;
        telefonoInput.disabled = true;
        cargoInput.disabled = true;
        registrarBtn.disabled = true; // Bloquea el botón de registrar
        registrarBtn.classList.remove('btn-success');
        registrarBtn.classList.add('btn-secondary');
        cedulaError.style.display = 'inline';
        cedulaError.textContent = 'La cédula ya está registrada. No puedes registrar este empleado nuevamente.';
        cedulaStatus.style.display = 'none';
        cedulaInput.classList.remove('is-valid');
        empleadoEncontrado = true;
        ultimoEmpleadoBloqueado = data.cedula;
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
        registrarBtn.disabled = false;
        registrarBtn.classList.remove('btn-secondary');
        registrarBtn.classList.add('btn-success');
        cedulaError.style.display = 'none';
        cedulaInput.classList.add('is-valid');
        cedulaStatus.style.display = 'inline';
        cedulaStatus.textContent = 'Cédula disponible para registro';
        empleadoEncontrado = false;
        ultimoEmpleadoBloqueado = null;
    }

    function limpiarValidacionCedula() {
        cedulaInput.classList.remove('is-valid');
        cedulaStatus.style.display = 'none';
        cedulaError.style.display = 'none';
        registrarBtn.disabled = false;
        registrarBtn.classList.remove('btn-secondary');
        registrarBtn.classList.add('btn-success');
    }

    // Verificar cédula al cambiar el input
    cedulaInput.addEventListener('input', function() {
        const cedula = cedulaInput.value.trim();
        
        if (cedula.length === 0) {
            limpiarValidacionCedula();
            desbloquearCamposEmpleado(true);
            return;
        }

        // Validar formato de cédula si es necesario
        if (!/^\d+$/.test(cedula)) {
            cedulaError.style.display = 'inline';
            cedulaError.textContent = 'La cédula debe contener solo números';
            registrarBtn.disabled = true;
            return;
        } else {
            cedulaError.style.display = 'none';
        }

        // Consultar si la cédula existe
        fetch("{{ url('/empleados/verificar-cedula') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cedula: cedula })
        })
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                bloquearCamposEmpleado(data.empleado);
            } else {
                // Si la cédula que desbloqueó los campos es la misma que la última bloqueada, limpiar
                if (ultimoEmpleadoBloqueado && cedula !== ultimoEmpleadoBloqueado) {
                    desbloquearCamposEmpleado(true); // Limpiar campos solo si venía de un bloqueo
                } else {
                    desbloquearCamposEmpleado(false); // No limpiar si ya estaba desbloqueado
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            limpiarValidacionCedula();
        });
    });

    // Envío del formulario
    const form = document.getElementById('empleadoForm');
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
            submitBtn.disabled = empleadoEncontrado;
            submitBtn.innerHTML = 'Registrar';
        });
    });
});
</script>
@endsection