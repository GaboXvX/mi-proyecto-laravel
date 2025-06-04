@extends('layouts.app')
@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="table-container shadow" style="width: 100%; max-width: 600px;">
        <h2 class="text-center mb-4">Editar Empleado Autorizado</h2>
        <form action="{{ route('empleados.update', $empleado->id_empleado_autorizado) }}" method="POST" id="empleadoForm">
            @csrf
            @method('PUT')
           <div class="row g-2 mb-3">
                <div class="col-md-8">
                    <label for="cedula" class="form-label"><span style="color: red;" class="me-2">*</span>Cédula</label>
                <input type="text" name="cedula" id="cedula" class="form-control solo-numeros" value="{{ $empleado->cedula }}" readonly disabled>
                    <span id="cedulaStatus" class="text-muted" style="display:none;"></span>
                    <span id="cedulaError" class="text-danger" style="display:none;"></span>
                </div>
                <div class="col-md-4">
                    <label for="nacionalidad" class="form-label"><span style="color: red;" class="me-2">*</span>Nacionalidad</label>
                <input type="text" name="nacionalidad" id="nacionalidad" class="form-control solo-letras" value="{{ $empleado->nacionalidad }}" readonly disabled>
                </div>
            </div>
            
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label"><span style="color: red;" class="me-2">*</span>Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control solo-letras" required value="{{ old('nombre', $empleado->nombre) }}" maxlength="12">
                </div>
                <div class="col-md-6">
                    <label for="apellido" class="form-label"><span style="color: red;" class="me-2">*</span>Apellido</label>
                    <input type="text" name="apellido" id="apellido" class="form-control solo-letras" required value="{{ old('apellido', $empleado->apellido) }}" maxlength="12">
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="cargo_id" class="form-label"><span style="color: red;" class="me-2">*</span>Cargo</label>
                    <select name="cargo_id" id="cargo_id" class="form-control" required>
                        <option value="">Seleccione un cargo</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id_cargo }}" {{ old('cargo_id', $empleado->id_cargo) == $cargo->id_cargo ? 'selected' : '' }}>{{ $cargo->nombre_cargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="genero" class="form-label">Género</label>
                    <select name="genero" id="genero" class="form-control" required>
                        <option value="">Seleccione un género</option>
                        <option value="M" {{ old('genero', $empleado->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('genero', $empleado->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label"><span style="color: red;" class="me-2">*</span>Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" required value="{{ old('telefono', $empleado->telefono) }}">
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success" id="guardarBtn">Guardar</button>
            </div>
        </form>
        <span class="text-muted d-flex justify-content-center">Los campos señalados con * deben ser rellenados</span>
    </div>
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
    const guardarBtn = document.getElementById('guardarBtn');
    const cedulaStatus = document.getElementById('cedulaStatus');
    const cedulaError = document.getElementById('cedulaError');

    const cedulaOriginal = @json($empleado->cedula);
    let empleadoExistente = false;

    function bloquearCampos(data) {
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
        
        guardarBtn.disabled = true;
        guardarBtn.classList.remove('btn-success');
        guardarBtn.classList.add('btn-secondary');
        
        cedulaError.style.display = 'inline';
        cedulaError.textContent = 'La cédula ya está registrada por otro empleado.';
        cedulaStatus.style.display = 'none';
        cedulaInput.classList.remove('is-valid');
        
        empleadoExistente = true;
    }

    function desbloquearCampos() {
        nombreInput.disabled = false;
        apellidoInput.disabled = false;
        generoInput.disabled = false;
        telefonoInput.disabled = false;
        cargoInput.disabled = false;
        
        guardarBtn.disabled = false;
        guardarBtn.classList.remove('btn-secondary');
        guardarBtn.classList.add('btn-success');
        
        cedulaError.style.display = 'none';
        cedulaStatus.style.display = 'inline';
        cedulaStatus.textContent = 'Cédula válida para actualización';
        cedulaInput.classList.add('is-valid');
        
        empleadoExistente = false;
    }

    function limpiarValidacion() {
        cedulaInput.classList.remove('is-valid', 'is-invalid');
        cedulaStatus.style.display = 'none';
        cedulaError.style.display = 'none';
    }

    // Verificar cédula al cambiar el input
    cedulaInput.addEventListener('input', function() {
        const cedula = cedulaInput.value.trim();
        
        if (cedula.length === 0) {
            limpiarValidacion();
            return;
        }

        // Validar formato de cédula
        if (!/^\d+$/.test(cedula)) {
            cedulaError.style.display = 'inline';
            cedulaError.textContent = 'La cédula debe contener solo números';
            guardarBtn.disabled = true;
            return;
        }

        // Si la cédula es la misma que la original, no hacemos validación
        if (cedula === cedulaOriginal) {
            desbloquearCampos();
            return;
        }

        // Consultar si la cédula existe en otros empleados
        fetch("{{ url('/empleados/verificar-cedula') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                cedula: cedula,
                excluir: cedulaOriginal // Excluimos la cédula actual
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                bloquearCampos(data.empleado);
            } else {
                desbloquearCampos();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            limpiarValidacion();
        });
    });

    // Validación inicial al cargar la página
    if (cedulaInput.value === cedulaOriginal) {
        desbloquearCampos();
    }

    // Envío del formulario
    const form = document.getElementById('empleadoForm');
    form.addEventListener('submit', function(e) {
        if (empleadoExistente) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No puedes guardar los cambios porque la cédula pertenece a otro empleado',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        e.preventDefault();
        guardarBtn.disabled = true;
        guardarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
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
                text: error.message || 'Ocurrió un error al guardar el empleado',
                confirmButtonText: 'Entendido'
            });
        })
        .finally(() => {
            guardarBtn.disabled = empleadoExistente;
            guardarBtn.innerHTML = 'Guardar';
        });
    });
});
</script>
@endsection