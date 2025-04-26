@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Registrar Incidencia</h2>

    <div id="alert-container"></div> <!-- Contenedor para mostrar mensajes -->

    <form id="form-registrar-incidencia" action="{{ route('incidencias.store') }}" method="POST">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <input type="hidden" name="id_persona" value="{{ $persona->id_persona }}" />

        <div class="mb-3">
            <label for="tipo_incidencia" class="form-label">Tipo de Incidencia:</label>
            <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                <option value="" disabled selected>--Seleccione--</option>
                <option value="agua potable">Agua Potable</option>
                <option value="agua servida">Agua Servida</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="nivel_prioridad" class="form-label">Nivel de Prioridad:</label>
            <select id="nivel_prioridad" name="nivel_prioridad" class="form-select" required>
                <option value="" disabled selected>--Seleccione--</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección:</label>
            <select id="direccion" name="direccion" class="form-select" required>
                <option value="" disabled selected>--Seleccione--</option>
                @foreach ($persona->direccion as $direccion)
                    <option value="{{ $direccion->id_direccion }}">
                        {{ $direccion->comunidad->nombre }} - {{ $direccion->calle }} - Casa: {{ $direccion->numero_de_vivienda }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>

<script>
    document.getElementById('form-registrar-incidencia').addEventListener('submit', async function (event) {
        event.preventDefault(); // Evitar el envío tradicional del formulario

        const form = event.target;
        const formData = new FormData(form);
        const alertContainer = document.getElementById('alert-container');
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Limpiar mensajes anteriores
        alertContainer.innerHTML = '';
        
        // Deshabilitar el botón para evitar múltiples envíos
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrando...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error en la respuesta del servidor');
            }

            // Mostrar mensaje de éxito
            alertContainer.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${data.message || 'Incidencia registrada correctamente'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            // Redirigir a la URL proporcionada después de 2 segundos
            if (data.redirect_url) {
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 2000);
            }

        } catch (error) {
            console.error('Error al registrar la incidencia:', error);
            
            // Mostrar mensaje de error específico
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> ${error.message || 'Ocurrió un error al registrar la incidencia. Por favor, verifica los datos e intenta nuevamente.'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Habilitar el botón nuevamente
            submitButton.disabled = false;
            submitButton.textContent = 'Registrar';
        }
    });
</script>
@endsection