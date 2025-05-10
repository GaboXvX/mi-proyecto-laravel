@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Estadísticas de Incidencias</h2>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('graficos.incidencias') }}">
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

    <!-- Resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Incidencias</h5>
                    <p class="card-text display-4">{{ $totalIncidencias }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Atendidas</h5>
                    <p class="card-text display-4">{{ $incidenciasAtendidas }}</p>
                    <p class="card-text">{{ $totalIncidencias > 0 ? round(($incidenciasAtendidas/$totalIncidencias)*100, 1) : 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pendientes</h5>
                    <p class="card-text display-4">{{ $incidenciasPendientes }}</p>
                                        <p class="card-text">{{ $totalIncidencias > 0 ? round(($incidenciasPendientes/$totalIncidencias)*100, 1) : 0 }}%</p>

                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Por Vencer</h5>
                    <p class="card-text display-4">{{ $incidenciasPorVencer }}</p>
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

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Evolución Temporal</h5>
                </div>
                <div class="card-body">
                    <canvas id="temporalChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
                        afterBody: function(context) {
                            const estado = context[0].label;
                            const detalles = @json($incidenciasPorEstado['detalles']);
                            let info = ['Detalle por niveles:'];

                            if (detalles[estado]) {
                                for (const [nivel, cantidad] of Object.entries(detalles[estado])) {
                                    if (cantidad > 0) {
                                        info.push(`${nivel}: ${cantidad}`);
                                    }
                                }
                            } else {
                                info.push('Sin detalles disponibles');
                            }

                            return info;
                        }
                    }
                }
            }
        }
    });

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
            responsive: true
        }
    });

    // Gráfico temporal
    // Gráfico temporal de líneas

    // Gráfico temporal de líneas con líneas curvas
    const temporalCtx = document.getElementById('temporalChart').getContext('2d');
    new Chart(temporalCtx, {
        type: 'line',
        data: {
            labels: @json($datosTemporales['labels']),  // Fechas o periodos
            datasets: [
                {
                    label: 'Atendidas',
                    data: @json($datosTemporales['series'][0]['data']),
                    borderColor: 'green',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    fill: false,
                    tension: 0.4, // Hace la línea curva
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Pendientes',
                    data: @json($datosTemporales['series'][1]['data']),
                    borderColor: 'orange',
                    backgroundColor: 'rgba(255, 165, 0, 0.1)',
                    fill: false,
                    tension: 0.4, // Hace la línea curva
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Batalla: Atendidas vs Pendientes',
                    font: {
                        size: 16
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Fecha'
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Cantidad de Incidencias'
                    },
                    beginAtZero: true
                }
            }
        }
    });



    // Cargar estaciones según institución seleccionada
    document.getElementById('institucion_id').addEventListener('change', function() {
        const institucionId = this.value;
        const estacionSelect = document.getElementById('estacion_id');

        if (institucionId) {
            fetch(`/api/estaciones-por-institucion/${institucionId}`)
                .then(response => response.json())
                .then(data => {
                    estacionSelect.innerHTML = '<option value="">Todas</option>';
                    data.forEach(estacion => {
                        estacionSelect.innerHTML += `<option value="${estacion.id}">${estacion.nombre}</option>`;
                    });
                });
        } else {
            estacionSelect.innerHTML = '<option value="">Todas</option>';
        }
    });
</script>
@endsection
