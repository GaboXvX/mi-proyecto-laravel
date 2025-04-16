@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .status-pending {
        color: orange;
    }
    .status-resolved {
        color: green;
    }
    .status-closed {
        color: red;
    }
    .alert {
        border-radius: 6px;
        font-weight: bold;
    }
    .alert-success {
        background-color: #28a745;
        color: white;
    }
    .alert-danger {
        background-color: #dc3545;
        color: white;
    }
    .btn-atender {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn-atender:hover {
        background-color: #218838;
    }
    .btn-atender:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
    }
</style>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="table-container">
    <div class="d-flex justify-content-between align-item-center mb-3">
        <h2>Lista de Incidencias</h2>
        <div class="gen-pdf">
            @can('descargar listado incidencias')
            <form id="generar-pdf-form" action="{{ route('incidencias.generarPDF') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" id="pdf-fecha-inicio" name="fecha_inicio">
                <input type="hidden" id="pdf-fecha-fin" name="fecha_fin">
                <input type="hidden" id="pdf-estado" name="estado">
                <button type="submit" class="btn btn-primary">Generar PDF</button>
            </form>
            @endcan
        </div>
    </div>
   
    <!-- Filters -->
    <div class="d-flex filters-container gap-2">
        <form id="busqueda-codigo-form" class="input-group input-group-sm">
            <button class="input-group-text btn btn-primary" id="basic-addon1" type="button">
                <i class="bi bi-search"></i>
            </button>
            <input type="text" id="codigo-busqueda" class="form-control form-control-sm" placeholder="Ingrese un código">
        </form>
        <form id="filtros-form">
            @csrf
            <label for="fecha_inicio" class="form-label">Selecciona el período:</label>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex">
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2 mb-3" />
                    <span class="m-2">hasta</span>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control ml-2 mb-3" />
                </div>
                <select class="form-select form-select-sm w-50 m-2" aria-label="Select status" name="estado" id="estado">
                    <option value="Todos" selected>Todos</option>
                    <option value="Atendido">Atendido</option>
                    <option value="Por atender">Por atender</option>
                </select>
            </div>
        </form>
    </div>

    <div id="resultados" class="mt-3"></div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Código de incidencia</th>
                    <th>Tipo de Incidencia</th>
                    <th>Descripción</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Creación</th>
                    <th>Registrado por</th>
                    <th>Líder</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="incidencias-tbody">
                @foreach ($incidencias as $incidencia)
                    <tr data-incidencia-id="{{ $incidencia->slug }}">
                        <td>{{ $incidencia->cod_incidencia }}</td>
                        <td>{{ $incidencia->tipo_incidencia }}</td>
                        <td>{{ $incidencia->descripcion }}</td>
                        <td>{{ $incidencia->nivel_prioridad }}</td>
                        <td class="incidencia-status 
                                    @if($incidencia->estado == 'Por atender') status-pending 
                                    @elseif($incidencia->estado == 'Atendido') status-resolved 
                                    @endif">
                            {{ $incidencia->estado }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($incidencia->created_at)->format('d-m-Y H:i:s') }}</td>
                        <td>
                            @if($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
                                {{ $incidencia->usuario->empleadoAutorizado->nombre }} {{ $incidencia->usuario->empleadoAutorizado->apellido }}
                                <strong>V-</strong>{{ $incidencia->usuario->empleadoAutorizado->cedula }}
                            @else
                                <em>No registrado</em>
                            @endif
                        </td>
                        <td>
                            @if($incidencia->lider && $incidencia->lider->personas)
                                {{ $incidencia->lider->personas->nombre ?? 'Nombre no disponible' }} 
                                {{ $incidencia->lider->personas->apellido ?? 'Apellido no disponible' }} 
                                <strong>V-</strong>{{ $incidencia->lider->personas->cedula ?? 'Cédula no disponible' }}
                            @else
                                <em>No tiene un líder asignado</em>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                @can('descargar grafica incidencia')
                                <a href="{{ route('incidencias.descargar', ['slug' => $incidencia->slug]) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                                @endcan
                                
                                @can('cambiar estado de incidencias')
                                @if($incidencia->estado == 'Por atender')
                                <button class="btn btn-atender btn-sm" onclick="atenderIncidencia('{{ $incidencia->slug }}')">
                                    <i class="bi bi-check-circle"></i> Atender
                                </button>
                                @else
                                <button class="btn btn-atender btn-sm" disabled>
                                    <i class="bi bi-check-circle"></i> Atendido
                                </button>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    class FiltroIncidencias {
        constructor(codigoInputId, fechaInicioId, fechaFinId, estadoId, tbodyId, url) {
            this.codigoInput = document.getElementById(codigoInputId);
            this.fechaInicio = document.getElementById(fechaInicioId);
            this.fechaFin = document.getElementById(fechaFinId);
            this.estado = document.getElementById(estadoId);
            this.tbody = document.getElementById(tbodyId);
            this.url = url;

            // Event listeners
            this.codigoInput.addEventListener('input', () => this.buscarPorCodigo());
            this.fechaInicio.addEventListener('change', () => this.filtrarIncidencias());
            this.fechaFin.addEventListener('change', () => this.filtrarIncidencias());
            this.estado.addEventListener('change', () => this.filtrarIncidencias());
        }

        async buscarPorCodigo() {
            const codigo = this.codigoInput.value;

            if (codigo.length === 0) {
                await this.filtrarIncidencias();
                return;
            }

            try {
                const response = await fetch(this.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ codigo })
                });

                const data = await response.json();
                this.mostrarResultados(data.incidencias);
            } catch (error) {
                console.error('Error al buscar por código:', error);
            }
        }

        async filtrarIncidencias() {
            const fechaInicio = this.fechaInicio.value;
            const fechaFin = this.fechaFin.value;
            const estado = this.estado.value;

            try {
                const response = await fetch(this.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ fecha_inicio: fechaInicio, fecha_fin: fechaFin, estado })
                });

                const data = await response.json();
                this.mostrarResultados(data.incidencias);
            } catch (error) {
                console.error('Error al filtrar incidencias:', error);
            }
        }

        mostrarResultados(incidencias) {
            this.tbody.innerHTML = '';

            if (incidencias && incidencias.length > 0) {
                incidencias.forEach(incidencia => {
                    const fecha = new Date(incidencia.created_at);
                    const fechaFormateada = `${fecha.getDate().toString().padStart(2, '0')}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}-${fecha.getFullYear()} ${fecha.getHours().toString().padStart(2, '0')}:${fecha.getMinutes().toString().padStart(2, '0')}:${fecha.getSeconds().toString().padStart(2, '0')}`;

                    const usuario = incidencia.usuario || {};
                    const empleado = usuario.empleado_autorizado || {};
                    const lider = incidencia.lider || {};
                    const persona = lider.personas || {};

                    const registradoPor = empleado.nombre 
                        ? `${empleado.nombre} ${empleado.apellido} <strong>V-</strong>${empleado.cedula}`
                        : '<em>No registrado</em>';

                    const liderInfo = persona.nombre 
                        ? `${persona.nombre} ${persona.apellido} <strong>V-</strong>${persona.cedula}`
                        : '<em>No tiene un líder asignado</em>';

                    const tr = document.createElement('tr');
                    tr.setAttribute('data-incidencia-id', incidencia.slug);
                    tr.innerHTML = `
                        <td>${incidencia.cod_incidencia}</td>
                        <td>${incidencia.tipo_incidencia}</td>
                        <td>${incidencia.descripcion}</td>
                        <td>${incidencia.nivel_prioridad}</td>
                        <td class="incidencia-status ${this.getStatusClass(incidencia.estado)}">${incidencia.estado}</td>
                        <td>${fechaFormateada}</td>
                        <td>${registradoPor}</td>
                        <td>${liderInfo}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="/incidencias/descargar/${incidencia.slug}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                                ${incidencia.estado === 'Por atender' ? 
                                    `<button class="btn btn-atender btn-sm" onclick="atenderIncidencia('${incidencia.slug}')">
                                        <i class="bi bi-check-circle"></i> Atender
                                    </button>` : 
                                    `<button class="btn btn-atender btn-sm" disabled>
                                        <i class="bi bi-check-circle"></i> Atendido
                                    </button>`}
                            </div>
                        </td>
                    `;

                    this.tbody.appendChild(tr);
                });
            } else {
                this.tbody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron incidencias para los filtros seleccionados.</td></tr>';
            }
        }

        getStatusClass(estado) {
            switch (estado) {
                case 'Atendido': return 'status-resolved';
                case 'Por atender': return 'status-pending';
                default: return '';
            }
        }
    }

    // Función para atender incidencia
    // Función para atender incidencia - Versión mejorada
    async function atenderIncidencia(slug) {
    // 1. Confirmación del usuario
    if (!confirm('¿Está seguro que desea marcar esta incidencia como atendida?')) {
        return;
    }

    // 2. Selección de elementos con verificación
    const fila = document.querySelector(`tr[data-incidencia-id="${slug}"]`);
    if (!fila) {
        mostrarNotificacion('error', 'No se encontró la incidencia en la interfaz');
        return;
    }

    const boton = fila.querySelector('.btn-atender');
    const celdaEstado = fila.querySelector('.incidencia-status');
    if (!boton || !celdaEstado) {
        mostrarNotificacion('error', 'Elementos de la interfaz no encontrados');
        return;
    }

    // 3. Estado de carga
    const textoOriginal = boton.innerHTML;
    boton.innerHTML = '<i class="bi bi-hourglass"></i> Procesando...';
    boton.disabled = true;

    try {
        // 4. Solicitud al servidor
        const response = await fetch(`/incidencias/${slug}/atender`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        // 5. Manejo de respuesta
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `Error ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'La operación no fue exitosa');
        }

        // 6. Actualizar interfaz
        celdaEstado.textContent = 'Atendido';
        celdaEstado.className = 'incidencia-status status-resolved';
        boton.innerHTML = '<i class="bi bi-check-circle"></i> Atendido';
        boton.disabled = true;

        // 7. Notificación de éxito
        mostrarNotificacion('success', data.message || 'Incidencia marcada como atendida');

    } catch (error) {
        console.error('Error al atender incidencia:', error);
        
        // 8. Restaurar estado original
        boton.innerHTML = textoOriginal;
        boton.disabled = false;

        // 9. Notificación de error
        const mensajeError = error.message.includes('Failed to fetch') 
            ? 'Error de conexión. Verifique su red.'
            : error.message;
            
        mostrarNotificacion('error', mensajeError);
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(tipo, mensaje) {
    const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
    const notificacion = document.createElement('div');
    notificacion.className = `alert ${alertClass} fixed-top mx-auto mt-3`;
    notificacion.style.width = 'fit-content';
    notificacion.style.maxWidth = '80%';
    notificacion.style.zIndex = '9999';
    notificacion.textContent = mensaje;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 5000);
}

    // Inicializar la clase cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        new FiltroIncidencias(
            'codigo-busqueda',
            'fecha_inicio',
            'fecha_fin',
            'estado',
            'incidencias-tbody',
            '/filtrar-incidencia'
        );

        document.getElementById('generar-pdf-form').addEventListener('submit', function (e) {
            const fechaInicio = document.getElementById('fecha_inicio').value || '';
            const fechaFin = document.getElementById('fecha_fin').value || '';
            const estado = document.getElementById('estado').value || 'Todos';

            document.getElementById('pdf-fecha-inicio').value = fechaInicio;
            document.getElementById('pdf-fecha-fin').value = fechaFin;
            document.getElementById('pdf-estado').value = estado;
        });
    });
</script>
@endsection