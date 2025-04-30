@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Atender Incidencia</h2>
    <p><strong>Código:</strong> {{ $incidencia->cod_incidencia }}</p>
    <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>

    <form id="atender-incidencia-form" action="{{ route('incidencias.atender.guardar', $incidencia->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción de la atención:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="prueba_fotografica" class="form-label">Prueba fotográfica:</label>
            <input type="file" id="prueba_fotografica" name="prueba_fotografica" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
            <small class="text-muted">Formatos aceptados: JPEG, PNG, JPG (Máx. 2MB)</small>
        </div>

        <button type="submit" class="btn btn-success" id="submit-btn">Guardar Atención</button>
        <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('atender-incidencia-form');
    const submitBtn = document.getElementById('submit-btn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Deshabilitar botón para evitar múltiples envíos
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
        
        // Mostrar loader
        Swal.fire({
            title: 'Procesando',
            html: 'Guardando la atención de la incidencia...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Crear FormData y enviar
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Ocurrió un error al procesar la solicitud',
                confirmButtonText: 'Entendido'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Guardar Atención';
        });
    });
});
</script>
@endsection