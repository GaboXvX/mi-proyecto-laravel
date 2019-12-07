@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Fecha de inicio:</label>
                    <input type="date" id="start_date" name="start_date" 
                           value="{{ $startDate->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Fecha de fin:</label>
                    <input type="date" id="end_date" name="end_date" 
                           value="{{ $endDate->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="tipo_incidencia_id" class="form-label">Tipo de incidencia:</label>
                    <select id="tipo_incidencia_id" name="tipo_incidencia_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($tiposIncidencia as $tipo)
                            <option value="{{ $tipo->id_tipo_incidencia }}" 
                                {{ $tipoIncidenciaId == $tipo->id_tipo_incidencia ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="denunciante" class="form-label">Denunciante:</label>
                    <select id="denunciante" name="denunciante" class="form-select">
                        <option value="">Todos</option>
                        <option value="con" {{ $denunciante == 'con' ? 'selected' : '' }}>Con denunciante</option>
                        <option value="sin" {{ $denunciante == 'sin' ? 'selected' : '' }}>Sin denunciante</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="institucion_id" class="form-label">Institución:</label>
                    <select id="institucion_id" name="institucion_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($instituciones as $institucion)
                            <option value="{{ $institucion->id_institucion }}" 
                                {{ $institucionId == $institucion->id_institucion ? 'selected' : '' }}>
                                {{ $institucion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="estacion_id" class="form-label">Estación:</label>
                    <select id="estacion_id" name="estacion_id" class="form-select" {{ !$institucionId ? 'disabled' : '' }}>
                        <option value="">Todas</option>
                        @foreach($estaciones as $estacion)
                            <option value="{{ $estacion->id_institucion_estacion }}" 
                                {{ $estacionId == $estacion->id_institucion_estacion ? 'selected' : '' }}>
                                {{ $estacion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráfica y Resumen -->
    <div class="row">
        <!-- Gráfica -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-bar"></i> Porcentaje de Incidencias Atendidas
                    </h5>
                    @can('descargar grafica incidencia')
                        <button id="downloadPdfBtn" class="btn btn-success btn-sm">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div id="chart-container" style="height: 400px; width: 100%;">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Estadístico -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> Resumen Estadístico
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Período:</h6>
                        <p>{{ $startDate->isoFormat('D MMMM YYYY') }} al {{ $endDate->isoFormat('D MMMM YYYY') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Total Incidencias:</h6>
                        <p>{{ array_sum($dataAtendidas) }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Promedio de Atención:</h6>
                        <p>{{ count($porcentajes) > 0 ? round(array_sum($porcentajes) / count($porcentajes), 2) : '0' }}%</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Mejor Mes:</h6>
                        <p>
                            @if(count($porcentajes) > 0)
                                @php
                                    $maxKey = array_search(max($porcentajes), $porcentajes);
                                    $parts = explode('|', $maxKey);
                                    $monthYear = $parts[0];
                                @endphp
                                {{ $monthYear }} ({{ max($porcentajes) }}%)
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Detalles -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-table"></i> Detalle por Mes, Institución y Estación
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Mes</th>
                            <th>Institución</th>
                            <th>Estación</th>
                            <th>Incidencias Atendidas</th>
                            <th>Total Incidencias (Estación)</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labels as $uniqueKey => $label)
                            @php
                                $parts = explode('|', $uniqueKey);
                                $monthYear = $parts[0];
                                $totalMesEstacion = $detalles[$uniqueKey]['total_mes_estacion'] ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $monthYear }}</td>
                                <td>{{ $detalles[$uniqueKey]['institucion'] ?? 'N/A' }}</td>
                                <td>{{ $detalles[$uniqueKey]['estacion'] ?? 'N/A' }}</td>
                                <td>{{ $dataAtendidas[$uniqueKey] }}</td>
                                <td>{{ $totalMesEstacion }}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $porcentajes[$uniqueKey] }}%" 
                                             aria-valuenow="{{ $porcentajes[$uniqueKey] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $porcentajes[$uniqueKey] }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('vendor/jspdf/jspdf.umd.min.js') }}"></script>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
// Variable global para el gráfico
let incidenciaChart = null;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    const dataTable = $('#dataTable').DataTable({
        language: { url: "{{ asset('vendor/datatables/es.json') }}" },
        order: [[0, 'asc']]
    });

    // Cargar estaciones cuando cambia la institución
    $('#institucion_id').change(function() {
        const institucionId = $(this).val();
        const estacionSelect = $('#estacion_id');
        
        if (!institucionId) {
            estacionSelect.empty().append('<option value="">Todas</option>').prop('disabled', true);
            return;
        }

        estacionSelect.prop('disabled', true).empty().append('<option value="">Cargando...</option>');

        $.get(`/api/estaciones-por-institucion/${institucionId}`, function(data) {
            estacionSelect.empty().append('<option value="">Todas</option>');
            data.forEach(estacion => {
                estacionSelect.append(`<option value="${estacion.id}">${estacion.nombre}</option>`);
            });
            estacionSelect.prop('disabled', false);
        }).fail(function() {
            estacionSelect.empty().append('<option value="">Error al cargar</option>');
        });
    });

    // Inicializar el gráfico
    initializeChart();

    // Validación de fechas
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const start = new Date(document.getElementById('start_date').value);
        const end = new Date(document.getElementById('end_date').value);
        if (start > end) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en fechas',
                text: 'La fecha de inicio no puede ser posterior a la fecha de fin.',
                confirmButtonText: 'Entendido'
            });
        }
    });
});

function initializeChart() {
    const ctx = document.getElementById('chart1');
    
    // Destruir gráfico anterior si existe
    if (incidenciaChart) {
        incidenciaChart.destroy();
    }

    // Verificar si el canvas existe
    if (!ctx) {
        console.error('No se encontró el elemento canvas con ID chart1');
        return;
    }

    // Obtener datos del backend
    const labels = @json(array_values($labels));
    const dataAtendidas = @json(array_values($dataAtendidas));
    const porcentajes = @json(array_values($porcentajes));
    const detalles = @json($detalles);

    // Verificar si hay datos para mostrar
    if (labels.length === 0 || dataAtendidas.length === 0 || porcentajes.length === 0) {
        document.getElementById('chart1').closest('.card-body').innerHTML = `
            <div class="alert alert-warning">
                No hay datos disponibles para mostrar el gráfico con los filtros actuales.
            </div>
        `;
        return;
    }

    // Configuración del gráfico
    incidenciaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Incidencias Atendidas',
                    data: dataAtendidas,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Porcentaje de Atención',
                    data: porcentajes,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    type: 'line',
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeInOutQuad'
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Cantidad de Incidencias'
                    },
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Porcentaje (%)'
                    },
                    min: 0,
                    max: 100,
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += context.datasetIndex === 0 ? 
                                    context.parsed.y + ' incidencias' : 
                                    context.parsed.y + '%';
                            }
                            return label;
                        },
                        afterLabel: function(context) {
                            const index = context.dataIndex;
                            const uniqueKey = Object.keys(@json($labels))[index];
                            const detalle = detalles[uniqueKey];
                            return [
                                'Total incidencias (estación): ' + (detalle?.total_mes_estacion || 0),
                                'Institución: ' + (detalle?.institucion || 'N/A'),
                                'Estación: ' + (detalle?.estacion || 'N/A'),
                                'Tipo: ' + (detalle?.tipo_incidencia || 'N/A')
                            ].join('\n');
                        }
                    }
                }
            }
        }
    });

    // Configurar botón de exportar PDF
    document.getElementById('downloadPdfBtn')?.addEventListener('click', function() {
        if (!incidenciaChart) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No hay gráfico disponible para exportar',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');

        // Título y datos del reporte
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('Reporte de Incidencias Atendidas', 15, 15);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.text(`Período: ${@json($startDate->isoFormat('D MMMM YYYY'))} - ${@json($endDate->isoFormat('D MMMM YYYY'))}`, 15, 25);

        @isset($tipoIncidenciaId)
        @if($tipoIncidenciaId)
            doc.text(`Tipo: {{ $tiposIncidencia->firstWhere('id_tipo_incidencia', $tipoIncidenciaId)->nombre ?? 'N/A' }}`, 15, 35);
        @endif
        @endisset
        
        if ('{{ $denunciante }}') {
            doc.text(`Denunciante: {{ $denunciante == 'con' ? 'Con denunciante' : 'Sin denunciante' }}`, 15, 45);
        }

        // Agregar imagen del gráfico
        const chartImg = incidenciaChart.toBase64Image('image/jpeg', 1.0);
        doc.addImage(chartImg, 'JPEG', 15, 55, 180, 90);

        // Detalle de datos
        doc.setFontSize(10);
        doc.text('Detalle por Mes, Institución y Estación', 15, 155);
        doc.setFont('helvetica', 'bold');
        doc.text('Mes', 15, 165);
        doc.text('Institución', 50, 165);
        doc.text('Estación', 90, 165);
        doc.text('Atendidas', 130, 165);
        doc.text('Total', 150, 165);
        doc.text('%', 170, 165);
        doc.setFont('helvetica', 'normal');

        let y = 175;
        @foreach($labels as $uniqueKey => $label)
            @php
                $parts = explode('|', $uniqueKey);
                $monthYear = $parts[0];
                $totalMesEstacion = $detalles[$uniqueKey]['total_mes_estacion'] ?? 0;
            @endphp
            doc.text('{{ $monthYear }}', 15, y);
            doc.text('{{ $detalles[$uniqueKey]['institucion'] ?? 'N/A' }}', 50, y);
            doc.text('{{ $detalles[$uniqueKey]['estacion'] ?? 'N/A' }}', 90, y);
            doc.text('{{ $dataAtendidas[$uniqueKey] }}', 130, y);
            doc.text('{{ $totalMesEstacion }}', 150, y);
            doc.text('{{ $porcentajes[$uniqueKey] }}%', 170, y);
            y += 10;
        @endforeach

        doc.save('reporte_incidencias_{{ now()->format('YmdHis') }}.pdf');
    });
}
</script>
@endsection

@section('styles')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<style>
    .card { border-radius: 0.5rem; }
    .progress { height: 20px; }
    .progress-bar {
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }
    #chart1 {
        width: 100% !important;
        height: 100% !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.25rem;
        border: 1px solid #ddd;
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection