@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Registrar Incidencia</h2>

    <div id="alert-container"></div>

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
                    <option value="{{ $direccion->id_direccion }}" data-estado="{{ $direccion->estado->id_estado }}">
                        {{$direccion->estado->nombre}} - {{$direccion->municipio->nombre}} - {{$direccion->parroquia->nombre}} - {{$direccion->urbanizacion->nombre}} {{$direccion->sector->nombre}}- {{$direccion->comunidad->nombre}}- {{$direccion->calle}}- {{$direccion->manzana}}-
                        {{$direccion->numero_de_vivienda}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="institucion" class="form-label">Institución:</label>
            <select id="institucion" name="institucion" class="form-select" required>
                <option value="" disabled selected>--Seleccione una institución--</option>
                @foreach ($instituciones as $institucion)
                    <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="estacion" class="form-label">Estación:</label>
            <select id="estacion" name="estacion" class="form-select" required>
                <option value="" disabled selected>--Seleccione una estación--</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const direccionSelect = document.getElementById('direccion');
        const institucionSelect = document.getElementById('institucion');
        const estacionSelect = document.getElementById('estacion');

        async function cargarEstaciones() {
            const direccionId = direccionSelect.value;
            const estadoId = direccionSelect.options[direccionSelect.selectedIndex]?.getAttribute('data-estado');
            const institucionId = institucionSelect.value;

            // Limpiar el selector de estaciones
            estacionSelect.innerHTML = '<option value="" disabled selected>--Seleccione una estación--</option>';

            if (!direccionId || !estadoId || !institucionId) return;

            try {
                const response = await fetch(`/instituciones-estaciones/estado/${estadoId}/institucion/${institucionId}`);
                const data = await response.json();

                if (data.success) {
                    // Poblar el selector de estaciones
                    data.estaciones.forEach(estacion => {
                        const option = document.createElement('option');
                        option.value = estacion.id_institucion_estacion;
                        option.textContent = `${estacion.nombre} (Municipio: ${estacion.municipio.nombre})`;
                        estacionSelect.appendChild(option);
                    });

                    if (data.estaciones.length === 0) {
                        alert('No hay estaciones disponibles para esta combinación.');
                    }
                } else {
                    alert(data.message || 'Error al cargar las estaciones.');
                }
            } catch (error) {
                console.error('Error al cargar las estaciones:', error);
                alert('Ocurrió un error al cargar las estaciones. Intente nuevamente.');
            }
        }

        direccionSelect.addEventListener('change', cargarEstaciones);
        institucionSelect.addEventListener('change', cargarEstaciones);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form-registrar-incidencia');

        form.addEventListener('submit', async function (event) {
            event.preventDefault(); // Evitar el envío tradicional del formulario

            const formData = new FormData(form);
            const actionUrl = form.action;

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Mostrar mensaje de éxito con SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: result.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Redirigir al comprobante
                        window.location.href = result.redirect_url;
                    });
                } else {
                    // Mostrar mensaje de error con SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'Ocurrió un error al registrar la incidencia.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            } catch (error) {
                console.error('Error al enviar el formulario:', error);
                // Mostrar mensaje de error inesperado con SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'Ocurrió un error inesperado. Intente nuevamente.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
</script>
@endsection