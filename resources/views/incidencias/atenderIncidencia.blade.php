@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Atender Incidencia</h4>
        </div>
        <div class="card-body">
            <p><strong>Código:</strong> {{ $incidencia->cod_incidencia }}</p>
            <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>

            <form id="multi-step-form" action="{{ route('incidencias.atender.guardar', $incidencia->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Paso 1 -->
                <div id="step-1" class="step">
                    <h5 class="h5 text-primary">Paso 1: Datos del Personal</h4>

                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-info btn-sm" onclick="cargarDatosUsuario()">
                            <i class="bi bi-person-fill me-1"></i> Personal Autorizado
                        </button>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                            <input type="text" name="cedula" id="cedula" class="form-control" required pattern="\d+" placeholder="Ej: 12345678">
                        </div>

                        <div class="col-md-4">
                            <label for="nacionalidad" class="form-label">Nacionalidad <span class="text-danger">*</span></label>
                            <select name="nacionalidad" id="nacionalidad" class="form-select" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="V">Venezolano (V)</option>
                                <option value="E">Extranjero (E)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" name="telefono" id="telefono" class="form-control" required pattern="\d{10,15}" placeholder="Ej: 04141234567">
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" id="apellido" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-primary" onclick="nextStep()">
                            Siguiente <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div id="step-2" class="step d-none">
                    <h4 class="h5 text-primary">Paso 2: Atención de la Incidencia</h4>

                    <div class="mb-3 mt-2">
                        <label for="descripcion" class="form-label">Descripción de la atención <span class="text-danger">*</span></label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required placeholder="Describe cómo fue atendida la incidencia..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="prueba_fotografica" class="form-label">Prueba fotográfica <span class="text-danger">*</span></label>
                        <input type="file" name="prueba_fotografica" id="prueba_fotografica" class="form-control" accept=".jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG. Máx: 2MB.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="previousStep()">
                            <i class="bi bi-arrow-left-circle me-1"></i> Anterior
                        </button>
                        <button type="submit" class="btn btn-success" id="submit-btn">
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
