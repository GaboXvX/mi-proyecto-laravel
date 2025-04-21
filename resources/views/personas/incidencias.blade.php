@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">Incidencias de {{ $persona->nombre }} {{ $persona->apellido }}</h2>

    @if($incidencias->isEmpty())
        <p class="alert alert-warning">No hay incidencias registradas para esta persona.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Codigo Incidencia</th>
                    <th>Tipo de Incidencia</th>
                    <th>Descripción</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incidencias as $incidencia)
                    <tr>
                        <td>{{ $incidencia->cod_incidencia }}</td>
                        <td>{{ $incidencia->tipo_incidencia }}</td>
                        <td>{{ $incidencia->descripcion }}</td>
                        <td>{{ $incidencia->nivel_prioridad }}</td>
                        <td>{{ $incidencia->estado }}</td>
                        <td>{{ $incidencia->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($incidencia->estado !== 'Atendido')
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editIncidenciaModal-{{ $incidencia->id_incidencia }}">
                                    Modificar incidencia
                                </button>
                            @endif
                            <a href="{{ route('incidencias.descargar', ['slug' => $incidencia->slug]) }}" class="btn btn-success btn-sm" title="Descargar comprobante">
                                <i class="bi bi-download"></i> Descargar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $incidencias->links() }}
        </div>
    @endif
</div>

<!-- Modales para cada incidencia -->
@foreach($incidencias as $incidencia)
@if($incidencia->estado !== 'Atendido')
<div class="modal fade" id="editIncidenciaModal-{{ $incidencia->id_incidencia }}" tabindex="-1" aria-labelledby="editIncidenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIncidenciaModalLabel">Modificar Incidencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="global-alerts-{{ $incidencia->id_incidencia }}" class="alert d-none"></div>

                <form id="editIncidenciaForm-{{ $incidencia->id_incidencia }}" action="{{ route('incidencias.update', $incidencia->id_incidencia) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id_persona" value="{{ $incidencia->id_persona }}" class="form-control mb-3">

                    <div class="mb-3">
                        <label for="tipo_incidencia" class="form-label">Tipo de incidencia:</label>
                        <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                            <option value="" disabled>--Seleccione--</option>
                            <option value="agua potable" {{ old('tipo_incidencia', $incidencia->tipo_incidencia) == 'agua potable' ? 'selected' : '' }}>Agua Potable</option>
                            <option value="agua servida" {{ old('tipo_incidencia', $incidencia->tipo_incidencia) == 'agua servida' ? 'selected' : '' }}>Agua Servida</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion', $incidencia->descripcion) }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="nivel_prioridad" class="form-label">Nivel de Incidencia:</label>
                        <select name="nivel_prioridad" id="nivel_prioridad" class="form-select" required>
                            <option value="" disabled>--Seleccione--</option>
                            <option value="1" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '4' ? 'selected' : '' }}>4</option>
                            <option value="5" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '5' ? 'selected' : '' }}>5</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <select id="direccion" name="direccion" class="form-select" required>
                            <option value="" disabled selected>--Seleccione--</option>
                            @foreach ($persona->direccion as $direccion)
                                <option value="{{ $direccion->id_direccion }}" {{ $direccion->id_direccion == $incidencia->id_direccion ? 'selected' : '' }}>
                                     {{ $direccion->parroquia->nombre }} - {{ $direccion->urbanizacion->nombre }} - {{ $direccion->sector->nombre }} - {{ $direccion->comunidad->nombre }} -  {{ $direccion->calle }} -{{ $direccion->manzana }} -{{ $direccion->numero_de_vivienda }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <input type="text" id="estado" name="estado" value="{{ old('estado', $incidencia->estado) }}" class="form-control" readonly>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn-{{ $incidencia->id_incidencia }}">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Para cada formulario de incidencia
    document.querySelectorAll('[id^="editIncidenciaForm-"]').forEach(form => {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const formId = form.id.split('-')[1];
            const submitBtn = document.getElementById(`submitBtn-${formId}`);
            const globalAlerts = document.getElementById(`global-alerts-${formId}`);
            
            // Limpiar errores previos
            globalAlerts.classList.add('d-none');
            globalAlerts.innerHTML = '';
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
                const feedback = el.nextElementSibling;
                if (feedback) feedback.textContent = '';
            });

            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = ` 
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Procesando...
            `;

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    // Mostrar errores de campo específico
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            const input = form.querySelector(`[name="${field}"]`);
                            const errorElement = input?.nextElementSibling;
                            
                            if (input && errorElement) {
                                input.classList.add('is-invalid');
                                errorElement.textContent = messages[0];
                            }
                        });
                    }
                    
                    // Mostrar mensaje global si existe
                    if (data.message) {
                        globalAlerts.classList.remove('d-none');
                        globalAlerts.classList.add('alert-danger');
                        globalAlerts.innerHTML = data.message;
                        globalAlerts.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    // Éxito - cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById(`editIncidenciaModal-${formId}`));
                    modal.hide();
                    
                    // Mostrar mensaje de éxito
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success';
                    successAlert.textContent = data.message || 'Incidencia actualizada correctamente';
                    document.querySelector('.container').prepend(successAlert);

                    // Eliminar mensaje después de 2 segundos
                    setTimeout(() => successAlert.remove(), 2000);

                    // Recargar la página después de 20 segundos
                    setTimeout(() => {
                        window.location.reload();  // Recargar la página
                    }, 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                globalAlerts.classList.remove('d-none');
                globalAlerts.classList.add('alert-danger');
                globalAlerts.innerHTML = 'Error de conexión. Por favor intente nuevamente.';
                globalAlerts.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Guardar Cambios';
            }
        });
    });
});
</script>
@endsection
