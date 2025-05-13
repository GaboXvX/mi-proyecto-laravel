@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2 class="mb-4">Estadísticas de Incidencias</h2>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('graficos.incidencias') }}">
                <!-- Filtros de fecha, tipo y nivel -->
                <div class="row">
                    <div class="col-md-3">
                        <label for="start_date">Fecha Inicio</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">Fecha Fin</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <label for="tipo_incidencia_id">Tipo</label>
                        <select class="form-control" name="tipo_incidencia_id">
                            <option value="">Todos</option>
                            @foreach($tiposIncidencia as $tipo)
                                <option value="{{ $tipo->id_tipo_incidencia }}" {{ $filters['tipo_incidencia_id'] == $tipo->id_tipo_incidencia ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="nivel_incidencia_id">Nivel</label>
                        <select class="form-control" name="nivel_incidencia_id">
                            <option value="">Todos</option>
                            @foreach($nivelesIncidencia as $nivel)
                                <option value="{{ $nivel->id_nivel_incidencia }}" {{ $filters['nivel_incidencia_id'] == $nivel->id_nivel_incidencia ? 'selected' : '' }}>
                                    {{ $nivel->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
                    </div>
                </div>

                <!-- Filtros de institución y estación -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="institucion_id">Institución</label>
                        <select class="form-control" name="institucion_id" id="institucion_id">
                            <option value="">Todas</option>
                            @foreach($instituciones as $institucion)
                                <option value="{{ $institucion->id_institucion }}" {{ $filters['institucion_id'] == $institucion->id_institucion ? 'selected' : '' }}>
                                    {{ $institucion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="estacion_id">Estación</label>
                        <select class="form-control" name="estacion_id" id="estacion_id">
                            <option value="">Todas</option>
                            @foreach($estaciones as $estacion)
                                <option value="{{ $estacion->id_institucion_estacion }}" {{ $filters['estacion_id'] == $estacion->id_institucion_estacion ? 'selected' : '' }}>
                                    {{ $estacion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- detalles de las estadisticas -->
    <div class="row d-flex">
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-primary h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Total Incidencias</h5>
                    <p class="card-text text-center display-4">{{ $totalIncidencias }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-success h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Atendidas</h5>
                    <p class="card-text text-center display-4">{{ $incidenciasAtendidas }}</p>
                    <p class="card-text">{{ $totalIncidencias > 0 ? round(($incidenciasAtendidas/$totalIncidencias)*100, 1) : 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-warning h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Pendientes</h5>
                    <p class="card-text display-4 text-center">{{ $incidenciasPendientes }}</p>
                    <p class="card-text">{{ $totalIncidencias > 0 ? round(($incidenciasPendientes/$totalIncidencias)*100, 1) : 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-danger h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Por Vencer</h5>
                    <p class="card-text display-4 text-center">{{ $incidenciasPorVencer }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Incidencias por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="estadoChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Incidencias por Nivel</h5>
                </div>
                <div class="card-body">
                    <canvas id="nivelChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/chart.umd.min.js') }}"></script>

<script>
    // Gráfico por estado
    // Gráfico por estado
const estadoCtx = document.getElementById('estadoChart').getContext('2d');
new Chart(estadoCtx, {
    type: 'pie',
    data: {
        labels: @json($incidenciasPorEstado['labels']),
        datasets: [{
            data: @json($incidenciasPorEstado['values']),
            backgroundColor: @json($incidenciasPorEstado['colors']),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    },
                    afterBody: function(context) {
                        const estado = context[0].label;
                        const detalles = @json($incidenciasPorEstado['detalles']);
                        let info = ['Detalle por niveles:'];
                        
                        if (detalles[estado]) {
                            for (const [nivel, cantidad] of Object.entries(detalles[estado])) {
                                if (cantidad > 0) {
                                    const totalNivel = Object.values(detalles[estado]).reduce((a, b) => a + b, 0);
                                    const porcentajeNivel = Math.round((cantidad / totalNivel) * 100);
                                    info.push(`${nivel}: ${cantidad} (${porcentajeNivel}%)`);
                                }
                            }
                        } else {
                            info.push('Sin detalles disponibles');
                        }
                        
                        return info;
                    }
                }
            },
            legend: {
                position: 'right',
            },
            datalabels: {
                formatter: (value, ctx) => {
                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    const percentage = Math.round((value / total) * 100);
                    return `${value}\n(${percentage}%)`;
                },
                color: '#fff',
                font: {
                    weight: 'bold',
                    size: 12
                }
            }
        }
    }
});

    // Gráfico por nivel
    // Gráfico por nivel
const nivelCtx = document.getElementById('nivelChart').getContext('2d');
new Chart(nivelCtx, {
    type: 'doughnut',
    data: {
        labels: @json($incidenciasPorNivel['labels']),
        datasets: [{
            data: @json($incidenciasPorNivel['values']),
            backgroundColor: @json($incidenciasPorNivel['colors']),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            },
            legend: {
                position: 'right',
            },
            datalabels: {
                formatter: (value, ctx) => {
                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    const percentage = Math.round((value / total) * 100);
                    return `${value}\n(${percentage}%)`;
                },
                color: '#fff',
                font: {
                    weight: 'bold',
                    size: 12
                }
            }
        }
    }
});

    // Cargar estaciones según institución seleccionada
    document.getElementById('institucion_id').addEventListener('change', function() {
    const institucionId = this.value;
    const estacionSelect = document.getElementById('estacion_id');
    const form = this.closest('form');
    
    estacionSelect.innerHTML = '<option value="">Cargando...</option>';
    estacionSelect.disabled = true;

    if (institucionId) {
        fetch(`/api/estaciones-por-institucion/${institucionId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta');
                return response.json();
            })
            .then(data => {
                estacionSelect.innerHTML = '<option value="">Todas</option>';
                
                if (data && data.length > 0) {
                    data.forEach(estacion => {
                        estacionSelect.innerHTML += 
                            `<option value="${estacion.id_institucion_estacion}">${estacion.nombre}</option>`;
                    });
                } else {
                    estacionSelect.innerHTML = '<option value="">No hay estaciones</option>';
                }
                
                estacionSelect.disabled = false;
                // Forzar envío del formulario después de cargar
                form.dispatchEvent(new Event('submit'));
            })
            .catch(error => {
                console.error('Error:', error);
                estacionSelect.innerHTML = '<option value="">Error al cargar</option>';
                estacionSelect.disabled = false;
            });
    } else {
        estacionSelect.innerHTML = '<option value="">Todas</option>';
        estacionSelect.disabled = false;
        form.dispatchEvent(new Event('submit'));
    }
});
</script>
@endsection