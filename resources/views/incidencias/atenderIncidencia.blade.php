@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h5 class="mb-0">Atender Incidencia</h5>
        </div>
        <div class="card-body py-3">
            <p class="mb-2"><strong>Código:</strong> {{ $incidencia->cod_incidencia }}</p>
            <p class="mb-2"><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>

            <form id="multi-step-form" action="{{ route('incidencias.atender.guardar', $incidencia->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-info btn-sm py-2 px-3" onclick="cargarDatosUsuario()">
                        <i class="bi bi-person-fill me-1"></i> Personal Autorizado
                    </button>
                </div>

                {{-- Paso 1 --}}
                <div id="step-1" class="step">
                    <h6 class="h6 text-primary mb-3">Paso 1: Datos del Personal</h6>

                    <div id="empleado-info" class="alert alert-info mt-3 d-none">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <span id="empleado-mensaje"></span>
                    </div>

                    <div class="row justify-content-center mt-3">
                        <div class="col-md-7">
                            <div class="mb-2">
                                <label for="cedula" class="form-label small mb-0">Cédula <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="cedula" id="cedula" class="form-control solo-numeros py-2" maxlength="8" required pattern="\d+" placeholder="Ej: 12345678" oninput="limpiarAlertaEmpleado()">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="nacionalidad" class="form-label small mb-0">Nacionalidad <span class="text-danger">*</span></label>
                                <select name="nacionalidad" id="nacionalidad" class="form-select form-select-sm py-2" required onchange="limpiarAlertaEmpleado()">
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="V">Venezolano (V)</option>
                                    <option value="E">Extranjero (E)</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="telefono" class="form-label small mb-0">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" name="telefono" id="telefono" class="form-control solo-numeros form-control-sm py-2" maxlength="11" required pattern="\d{10,15}" placeholder="Ej: 04141234567">
                            </div>

                            <div class="mb-2">
                                <label for="nombre" class="form-label small mb-0">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control solo-letras form-control-sm py-2" required>
                            </div>

                            <div class="mb-2">
                                <label for="apellido" class="form-label small mb-0">Apellido <span class="text-danger">*</span></label>
                                <input type="text" name="apellido" id="apellido" class="form-control solo-letras form-control-sm py-2" required>
                            </div>

                            <div class="mb-2">
                                <label for="genero" class="form-label small mb-0">Género <span class="text-danger">*</span></label>
                                <select name="genero" id="genero" class="form-select form-select-sm py-2" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="O">Otro</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="institucion" class="form-label small mb-0">Institución de Procedencia <span class="text-danger">*</span></label>
                                <select name="institucion" id="institucion" class="form-select form-select-sm py-2" required>
                                    <option value="" selected disabled>Seleccione una institución</option>
                                    @foreach(\App\Models\Institucion::orderBy('nombre')->get() as $institucion)
                                        <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="estacion" class="form-label small mb-0">Estación de Procedencia <span class="text-danger">*</span></label>
                                <select name="estacion" id="estacion" class="form-select form-select-sm py-2" required>
                                    <option value="" selected disabled>Seleccione una estación</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('incidencias.index', $incidencia->slug) }}" class="btn btn-secondary btn-sm py-2 px-3">
                            Cancelar
                        </a>
                        <button type="button" class="btn btn-primary btn-sm py-2 px-3" onclick="nextStep()">
                            Siguiente <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- Paso 2 --}}
                <div id="step-2" class="step d-none">
                    <h6 class="h6 text-primary mb-3">Paso 2: Atención de la Incidencia</h6>

                    <div class="mb-3 mt-2">
                        <label for="descripcion" class="form-label small">Descripción de la atención <span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="descripcion" class="form-control form-control-sm py-2" rows="2" required placeholder="Describe cómo fue atendida la incidencia..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Pruebas fotográficas <span class="text-danger">*</span></label>
                        <input type="file" name="pruebas_fotograficas[]" class="form-control form-control-sm py-2" accept=".jpg,.jpeg,.png" required>
                        <input type="file" name="pruebas_fotograficas[]" class="form-control form-control-sm py-2 mt-1" accept=".jpg,.jpeg,.png">
                        <input type="file" name="pruebas_fotograficas[]" class="form-control form-control-sm py-2 mt-1" accept=".jpg,.jpeg,.png">
                        <small class="form-text text-muted small">Puedes subir hasta 3 fotos. La primera es obligatoria. Máx: 2MB cada una.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm py-2 px-3" onclick="previousStep()">
                            <i class="bi bi-arrow-left-circle me-1"></i> Anterior
                        </button>
                        <button type="submit" class="btn btn-success btn-sm py-2 px-3" id="submit-btn">
                            <i class="bi bi-save me-1"></i> Guardar Atención
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

{{-- Scripts --}}
<script>
function nextStep() {
    document.getElementById('step-1').classList.add('d-none');
    document.getElementById('step-2').classList.remove('d-none');
}

function previousStep() {
    document.getElementById('step-2').classList.add('d-none');
    document.getElementById('step-1').classList.remove('d-none');
}

// NUEVO: Limpia la alerta al cambiar los campos
function limpiarAlertaEmpleado() {
    const info = document.getElementById('empleado-info');
    const mensaje = document.getElementById('empleado-mensaje');
    mensaje.textContent = '';
    info.classList.add('d-none');
}

// MODIFICADO: ahora usa nacionalidad + cedula como ID único
function buscarEmpleado() {
    const cedula = document.getElementById('cedula').value.trim();
    const empleadoInfo = document.getElementById('empleado-info');
    const empleadoMensaje = document.getElementById('empleado-mensaje');

    if (!cedula) {
        empleadoInfo.className = 'alert alert-danger mt-3';
        empleadoMensaje.textContent = 'Por favor ingrese la cédula';
        empleadoInfo.classList.remove('d-none');
        return;
    }

    empleadoInfo.className = 'alert alert-info mt-3';
    empleadoMensaje.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Buscando empleado...';
    empleadoInfo.classList.remove('d-none');

    fetch(`/personal-de-reparaciones/buscar/${cedula}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.encontrado) {
                empleadoInfo.className = 'alert alert-success mt-3';
                empleadoMensaje.textContent = `Empleado encontrado: ${data.nombre} ${data.apellido}. Puede ser asignado.`;

                // Llenar los campos con los datos del empleado
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('apellido').value = data.apellido;
                document.getElementById('telefono').value = data.telefono || '';
                
                // Si viene la nacionalidad del backend, la establecemos
                if (data.nacionalidad) {
                    document.getElementById('nacionalidad').value = data.nacionalidad;
                }

                // Bloquear campos que vienen del sistema
                document.getElementById('nombre').readOnly = true;
                document.getElementById('apellido').readOnly = true;
            } else {
                empleadoInfo.className = 'alert alert-warning mt-3';
                empleadoMensaje.textContent = 'Personal no registrado. Puede proceder a registrarlo.';

                // Limpiar campos (excepto cédula y nacionalidad)
                document.getElementById('nombre').value = '';
                document.getElementById('apellido').value = '';
                document.getElementById('telefono').value = '';

                // Dejar campos editables
                document.getElementById('nombre').readOnly = false;
                document.getElementById('apellido').readOnly = false;
            }
        })
        .catch(error => {
            empleadoInfo.className = 'alert alert-danger mt-3';
            empleadoMensaje.textContent = 'Error al buscar el empleado. Intente nuevamente.';
            console.error('Error:', error);
        });
}


// --- CARGA DINÁMICA DE ESTACIONES SEGÚN INSTITUCIÓN ---
document.getElementById('institucion').addEventListener('change', function() {
    const institucionId = this.value;
    const estacionSelect = document.getElementById('estacion');
    estacionSelect.innerHTML = '<option value="" disabled selected>Seleccione una estación</option>';
    estacionSelect.value = '';
    estacionSelect.disabled = true;
    if (!institucionId) return;
    fetch(`/personal-reparacion/estaciones/${institucionId}`)
        .then(response => response.json())
        .then(({ success, data }) => {
            estacionSelect.innerHTML = '<option value="" disabled selected>Seleccione una estación</option>';
            if (success && data.length > 0) {
                data.forEach(estacion => {
                    const nombre = estacion.codigo ? `${estacion.nombre} (${estacion.codigo})` : estacion.nombre;
                    const option = new Option(nombre, estacion.id);
                    estacionSelect.add(option);
                });
                estacionSelect.disabled = false;
            } else {
                estacionSelect.innerHTML = '<option value="" disabled selected>No hay estaciones disponibles</option>';
            }
        })
        .catch(error => {
            console.error('Error al cargar estaciones:', error);
            estacionSelect.innerHTML = '<option value="" disabled selected>Error al cargar estaciones</option>';
        });
});

// --- BÚSQUEDA AUTOMÁTICA DE PERSONAL POR CÉDULA ---
const cedulaInput = document.getElementById('cedula');
let timeoutBuscarEmpleado = null;
cedulaInput.addEventListener('input', function() {
    clearTimeout(timeoutBuscarEmpleado);
    const cedula = this.value.trim();
    if (cedula.length < 7) {
        limpiarCamposPersonal();
        desbloquearCamposPersonal();
        return;
    }
    timeoutBuscarEmpleado = setTimeout(() => buscarEmpleadoAuto(cedula), 400);
});

function limpiarCamposPersonal() {
    document.getElementById('nombre').value = '';
    document.getElementById('apellido').value = '';
    document.getElementById('telefono').value = '';
    document.getElementById('nacionalidad').value = '';
    document.getElementById('genero').value = '';
    document.getElementById('institucion').value = '';
    document.getElementById('estacion').innerHTML = '<option value="" selected disabled>Seleccione una estación</option>';
    document.getElementById('estacion').value = '';
}
function desbloquearCamposPersonal() {
    ['nombre','apellido','telefono'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.readOnly = false;
            el.classList.remove('bg-light');
        }
    });
    ['nacionalidad','institucion','estacion'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('bg-light');
            el.removeAttribute('data-readonly');
        }
    });
}
function bloquearCamposPersonal() {
    ['nombre','apellido','telefono','genero'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.readOnly = true;
            el.classList.add('bg-light');
            if (el.tagName === 'SELECT') {
                el.setAttribute('data-readonly', 'true');
                el.addEventListener('mousedown', function(e) {
                    if (el.getAttribute('data-readonly') === 'true') {
                        e.preventDefault();
                    }
                });
                el.addEventListener('keydown', function(e) {
                    if (el.getAttribute('data-readonly') === 'true') {
                        e.preventDefault();
                    }
                });
            }
        }
    });
    // Para los select, solo visualmente deshabilitados, pero no disabled
    ['nacionalidad','institucion','estacion'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('bg-light');
            el.setAttribute('data-readonly', 'true');
            el.addEventListener('mousedown', function(e) {
                if (el.getAttribute('data-readonly') === 'true') {
                    e.preventDefault();
                }
            });
            el.addEventListener('keydown', function(e) {
                if (el.getAttribute('data-readonly') === 'true') {
                    e.preventDefault();
                }
            });
        }
    });
}

function buscarEmpleadoAuto(cedula) {
    if (!cedula) return;
    const empleadoInfo = document.getElementById('empleado-info');
    const empleadoMensaje = document.getElementById('empleado-mensaje');
    empleadoInfo.className = 'alert alert-info mt-3';
    empleadoMensaje.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Buscando empleado...';
    empleadoInfo.classList.remove('d-none');
    fetch(`/personal-de-reparaciones/buscar/${cedula}`)
        .then(response => response.json())
        .then(data => {
            if (data.encontrado) {
                empleadoInfo.className = 'alert alert-success mt-3';
                empleadoMensaje.textContent = `Empleado encontrado: ${data.nombre} ${data.apellido}. Puede ser asignado.`;
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('apellido').value = data.apellido;
                document.getElementById('telefono').value = data.telefono || '';
                document.getElementById('nacionalidad').value = data.nacionalidad || '';
                document.getElementById('genero').value = data.genero || '';
                if (data.id_institucion) {
                    document.getElementById('institucion').value = data.id_institucion;
                    fetch(`/personal-reparacion/estaciones/${data.id_institucion}`)
                        .then(response => response.json())
                        .then(({ success, data: estaciones }) => {
                            const estacionSelect = document.getElementById('estacion');
                            estacionSelect.innerHTML = '<option value="" disabled selected>Seleccione una estación</option>';
                            if (success && estaciones.length > 0) {
                                estaciones.forEach(estacion => {
                                    const nombre = estacion.codigo ? `${estacion.nombre} (${estacion.codigo})` : estacion.nombre;
                                    const option = new Option(nombre, estacion.id);
                                    estacionSelect.add(option);
                                });
                                if (data.id_institucion_estacion) {
                                    estacionSelect.value = data.id_institucion_estacion;
                                }
                            }
                        });
                }
                bloquearCamposPersonal();
            } else {
                empleadoInfo.className = 'alert alert-warning mt-3';
                empleadoMensaje.textContent = 'Personal no registrado. Puede proceder a registrarlo.';
                limpiarCamposPersonal();
                desbloquearCamposPersonal();
            }
        })
        .catch(error => {
            empleadoInfo.className = 'alert alert-danger mt-3';
            empleadoMensaje.textContent = 'Error al buscar el empleado. Intente nuevamente.';
            limpiarCamposPersonal();
            desbloquearCamposPersonal();
            console.error('Error:', error);
        });
}


// Manejo del formulario
document.getElementById('multi-step-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';

    Swal.fire({
        title: 'Procesando',
        html: 'Guardando la atención de la incidencia...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            // Si la respuesta no es JSON válida, intenta extraer el mensaje de error
            return response.json().catch(() => {
                throw new Error('Error inesperado en el servidor.');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => window.location.href = data.redirect);
        } else {
            throw new Error(data.message || 'Error desconocido.');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error al procesar la solicitud.'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Guardar Atención';
    });
});
</script>
@endsection
