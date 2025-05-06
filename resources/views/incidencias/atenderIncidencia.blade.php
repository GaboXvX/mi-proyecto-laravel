@extends('layouts.app')

@section('content')
<div class="table-container mt-5">
    <h2 class="mb-4">Atender Incidencia</h2>
    <p><strong>Código:</strong> {{ $incidencia->cod_incidencia }}</p>
    <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>

    <form id="multi-step-form" action="{{ route('incidencias.atender.guardar', $incidencia->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Paso 1: Datos del personal -->
        <div id="step-1" class="step">
            <h4 class="text-primary mb-3">Paso 1: Datos del Personal</h4>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control form-control-sm" >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido:</label>
                    <input type="text" name="apellido" id="apellido" class="form-control form-control-sm" >
                </div>

                <div class="col-md-2 mb-3">
                    <label for="nacionalidad" class="form-label">Nac.:</label>
                    <select name="nacionalidad" id="nacionalidad" class="form-select form-select-sm" >
                        <option value="V">V</option>
                        <option value="E">E</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="text" name="cedula" id="cedula" class="form-control form-control-sm" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" class="form-control form-control-sm" >
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary btn-sm" onclick="nextStep()">Siguiente</button>
            </div>
        </div>

        <!-- Paso 2: Atención -->
        <div id="step-2" class="step d-none">
            <h4 class="text-primary mb-3">Paso 2: Atención de la Incidencia</h4>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción de la atención:</label>
                <textarea name="descripcion" id="descripcion" class="form-control form-control-sm" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="prueba_fotografica" class="form-label">Prueba fotográfica:</label>
                <input type="file" name="prueba_fotografica" id="prueba_fotografica" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg" required>
                <small class="text-muted">Formatos permitidos: JPEG, PNG, JPG (máx. 2MB)</small>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" onclick="previousStep()">Anterior</button>
                <button type="submit" class="btn btn-success btn-sm" id="submit-btn">Guardar Atención</button>
            </div>
        </div>
    </form>
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
