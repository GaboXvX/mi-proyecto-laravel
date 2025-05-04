@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3">
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
                    <label for="tipo_incidencia" class="form-label">Tipo de incidencia:</label>
                    <select id="tipo_incidencia" name="tipo_incidencia" class="form-select">
                        <option value="">Todos</option>
                        <option value="agua potable" {{ $tipoIncidencia == 'agua potable' ? 'selected' : '' }}>Agua Potable</option>
                        <option value="agua servida" {{ $tipoIncidencia == 'agua servida' ? 'selected' : '' }}>Agua Servida</option>
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
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Porcentaje de Incidencias Atendidas
                    </h5>
                    @can('descargar grafica incidencia')
                        <button id="downloadPdfBtn" class="btn btn-success btn-sm">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div style="height: 400px;">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Estadístico -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 font-weight-bold text-primary">
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
                                {{ $labels[array_search(max($porcentajes), $porcentajes)] }} ({{ max($porcentajes) }}%)
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
        <div class="card-header bg-white py-3">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table"></i> Detalle por Mes
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
                            <th>Total Incidencias</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labels as $index => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $detalles[$label]['institucion'] ?? 'N/A' }}</td>
                                <td>{{ $detalles[$label]['estacion'] ?? 'N/A' }}</td>
                                <td>{{ $dataAtendidas[$index] }}</td>
                                <td>{{ $dataAtendidas[$index] > 0 ? round($dataAtendidas[$index] / ($porcentajes[$index] / 100)) : 0 }}</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $porcentajes[$index] }}%" 
                                             aria-valuenow="{{ $porcentajes[$index] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $porcentajes[$index] }}%
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
<script src="{{ asset('vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('vendor/jspdf/jspdf.umd.min.js') }}"></script>
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTable
    $('#dataTable').DataTable({
        language: { url: "{{ asset('vendor/datatables/es.json') }}" },
        order: [[0, 'asc']]
    });

    // Chart.js
    // Chart.js - Versión corregida
const ctx = document.getElementById('chart1').getContext('2d');

// Verificar si hay datos para mostrar
if (@json($labels).length === 0) {
    document.getElementById('chart1').closest('.card-body').innerHTML = `
        <div class="alert alert-warning">
            No hay datos disponibles para mostrar el gráfico con los filtros actuales.
        </div>
    `;
} else {
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Incidencias Atendidas',
                    data: @json($dataAtendidas),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Porcentaje de Atención',
                    data: @json($porcentajes),
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
                            return [
                                'Total incidencias: ' + Math.round(@json($dataAtendidas)[index] / (@json($porcentajes)[index] * 100),
                                'Institución: ' + @json($detalles)[context.label].institucion,
                                'Estación: ' + @json($detalles)[context.label].estacion
                            ].join('\n');
                        }
                    }
                }
            }
        }
    });


    // Exportar PDF
    document.getElementById('downloadPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('Reporte de Incidencias Atendidas', 15, 15);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.text(`Período: ${@json($startDate->isoFormat('D MMMM YYYY'))} - ${@json($endDate->isoFormat('D MMMM YYYY'))}`, 15, 25);

        if ('{{ $tipoIncidencia }}') {
            doc.text(`Tipo: {{ ucfirst($tipoIncidencia) }}`, 15, 35);
        }

        if ('{{ $denunciante }}') {
            doc.text(`Denunciante: {{ $denunciante == 'con' ? 'Con denunciante' : 'Sin denunciante' }}`, 15, 45);
        }

        const chartImg = chart.toBase64Image();
        doc.addImage(chartImg, 'JPEG', 15, 55, 180, 90);

        doc.setFontSize(10);
        doc.text('Detalle por Mes', 15, 155);
        doc.setFont('helvetica', 'bold');
        doc.text('Mes', 15, 165);
        doc.text('Institución', 60, 165);
        doc.text('Estación', 100, 165);
        doc.text('Atendidas', 140, 165);
        doc.text('Total', 160, 165);
        doc.text('%', 180, 165);
        doc.setFont('helvetica', 'normal');

        let y = 175;
        @foreach($labels as $index => $label)
            doc.text('{{ $label }}', 15, y);
            doc.text('{{ $detalles[$label]['institucion'] ?? 'N/A' }}', 60, y);
            doc.text('{{ $detalles[$label]['estacion'] ?? 'N/A' }}', 100, y);
            doc.text('{{ $dataAtendidas[$index] }}', 140, y);
            doc.text(Math.round({{ $dataAtendidas[$index] }} / ({{ $porcentajes[$index] }} / 100)).toString(), 160, y);
            doc.text('{{ $porcentajes[$index] }}%', 180, y);
            y += 10;
        @endforeach

        doc.save('reporte_incidencias_{{ now()->format('YmdHis') }}.pdf');
    });

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
    #incidenciasChart {
        max-height: 400px;
        width: 100% !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.25rem;
        border: 1px solid #ddd;
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection
