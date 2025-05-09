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

                <div id="step-1" class="step">
                    <h6 class="h6 text-primary mb-3">Paso 1: Datos del Personal</h6>

                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-info btn-sm py-2 px-3" onclick="cargarDatosUsuario()">
                            <i class="bi bi-person-fill me-1"></i> Personal Autorizado
                        </button>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="nacionalidad" class="form-label small mb-0">Nacionalidad <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-3">
                            <select name="nacionalidad" id="nacionalidad" class="form-select form-select-sm py-2" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="V">Venezolano (V)</option>
                                <option value="E">Extranjero (E)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="cedula" class="form-label small mb-0">Cédula <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="cedula" id="cedula" class="form-control form-control-sm py-2" required maxlength="8" placeholder="Ej: 12345678">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="telefono" class="form-label small mb-0">Teléfono <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-4">
                            <input type="tel" name="telefono" id="telefono" class="form-control form-control-sm py-2" required maxlength="11" placeholder="Ej: 04141234567">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="nombre" class="form-label small mb-0">Nombre <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="nombre" id="nombre" class="form-control form-control-sm py-2" required maxlength="12">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="apellido" class="form-label small mb-0">Apellido <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="apellido" id="apellido" class="form-control form-control-sm py-2" required maxlength="12">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary btn-sm py-2 px-3" onclick="nextStep()">
                            Siguiente <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>
                    </div>
                </div>

                <div id="step-2" class="step d-none">
                    <h6 class="h6 text-primary mb-3">Paso 2: Atención de la Incidencia</h6>

                    <div class="mb-3 mt-2">
                        <label for="descripcion" class="form-label small">Descripción de la atención <span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="descripcion" class="form-control form-control-sm py-2" rows="2" required placeholder="Describe cómo fue atendida la incidencia..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="prueba_fotografica" class="form-label small">Prueba fotográfica <span class="text-danger">*</span></label>
                        <input type="file" name="prueba_fotografica" id="prueba_fotografica" class="form-control form-control-sm py-2" accept=".jpg,.jpeg,.png" required>
                        <small class="form-text text-muted small">Formatos permitidos: JPG, JPEG, PNG. Máx: 2MB.</small>
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

<script>
function nextStep() {
    document.getElementById('step-1').classList.add('d-none');
    document.getElementById('step-2').classList.remove('d-none');
}

function previousStep() {
    document.getElementById('step-2').classList.add('d-none');
    document.getElementById('step-1').classList.remove('d-none');
}

document.getElementById('multi-step-form').addEventListener('submit', function() {
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
});
</script>
<script>
document.getElementById('multi-step-form').addEventListener('submit', function(event) {
    event.preventDefault();  // Evita el envío del formulario por defecto

    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';

    // Creamos un FormData para enviar los datos del formulario, incluyendo los archivos
    const formData = new FormData();
    formData.append('descripcion', document.getElementById('descripcion').value);
    formData.append('prueba_fotografica', document.getElementById('prueba_fotografica').files[0]);
    formData.append('cedula', document.getElementById('cedula').value);
    formData.append('nombre', document.getElementById('nombre').value);
    formData.append('apellido', document.getElementById('apellido').value);
    formData.append('nacionalidad', document.getElementById('nacionalidad').value);
    formData.append('telefono', document.getElementById('telefono').value);
    formData.append('_token', '{{ csrf_token() }}');  // Añadir el token CSRF

    // Realizamos la solicitud AJAX
    fetch('{{ route('incidencias.atender.guardar', $incidencia->slug) }}', {
        method: 'POST',
        body: formData  // Enviamos el FormData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = data.redirect;  // Redirige a la lista de incidencias
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Hubo un error al atender la incidencia.',
            }).then(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Guardar Atención';
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al procesar la solicitud.',
        }).then(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Guardar Atención';
        });
    });
});

</script>

@endsection