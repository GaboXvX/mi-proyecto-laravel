@extends('layouts.app')
@section('content')
   
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}" />
    
    <!-- Usamos las versiones locales en lugar de CDN -->
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('js/jspdf.umd.min.js') }}"></script>
    
    <style>
        .download-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
        }

        .download-btn:hover {
            background-color: #45a049;
        }

        .filter-container {
            margin-bottom: 20px;
        }
    </style>

    <div class="container">
        <!-- filtrado -->
        <form class="card p-4 m-4">
            <div class="row g-3 align-items-end filter-section">
                <div class="col-md-4">
                    <label class="form-label">Fecha de inicio:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">Fecha de fin:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">Tipo de incidencia:</label>
                    <select id="tipo_incidencia" name="tipo_incidencia" class="form-select">
                        <option value="" {{ $tipoIncidencia == '' ? 'selected' : '' }}>Todos</option>
                        <option value="agua potable" {{ $tipoIncidencia == 'agua potable' ? 'selected' : '' }}>Agua Potable</option>
                        <option value="agua servida" {{ $tipoIncidencia == 'agua servida' ? 'selected' : '' }}>Agua Servida</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>

    <!-- grafica -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body" style="width: 100%; height: 400px; margin: 0 auto;">
                    <h3 class="card-title text-center">Gráfica</h3>
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @can('descargar grafica incidencia')
        <button class="download-btn" id="downloadPdfBtn">Descargar PDF</button>
    @endcan

    <script>
        // Asegúrate de que Chart.js esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('myChart').getContext('2d');

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Incidencias Atendidas',
                        data: @json($dataAtendidas),
                        backgroundColor: 'rgba(54, 162, 235, 0.5)', 
                        borderColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 0,
                        barThickness: 30, 
                    },
                    {
                        label: 'Tendencia',
                        data: @json($dataAtendidas),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        type: 'line',
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5,
                                font: { size: 12 }
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: true,
                                maxRotation: 45,
                                minRotation: 45,
                                font: { size: 12 }
                            }
                        }
                    },
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 14 } } },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': ' + tooltipItem.raw + ' incidencias';
                                }
                            }
                        }
                    }
                }
            });

            document.getElementById('downloadPdfBtn').addEventListener('click', function() {
                const { jsPDF } = window.jspdf;

                // Esperar a que el gráfico esté completamente renderizado
                setTimeout(() => {
                    const imageUrl = myChart.toBase64Image();

                    const doc = new jsPDF();
                    doc.setFontSize(18);
                    doc.text('Informe de Incidencias', 10, 10);

                    doc.setFontSize(12);
                    doc.text(`Fecha de Inicio: ${@json($startDate->toDateString())}`, 10, 20);
                    doc.text(`Fecha de Fin: ${@json($endDate->toDateString())}`, 10, 30);
                    if ('{{$tipoIncidencia}}' !== '') {
                        doc.text(`Tipo de Incidencia: {{$tipoIncidencia}}`, 10, 40);
                    }

                    const tableStartY = 50;
                    doc.setFontSize(12);
                    doc.text('Mes', 10, tableStartY);
                    doc.text('Incidencias Atendidas', 80, tableStartY);

                    let currentY = tableStartY + 10;

                    @foreach($labels as $index => $label)
                        doc.text('{{ $label }}', 10, currentY);
                        doc.text('{{ $dataAtendidas[$index] }}', 80, currentY); 
                        currentY += 10;
                    @endforeach

                    // Ajusta el tamaño del gráfico en el PDF
                    const chartWidth = 150; // Ancho reducido
                    const chartHeight = 60; // Alto reducido
                    doc.addImage(imageUrl, 'PNG', 10, currentY + 10, chartWidth, chartHeight);

                    doc.save('incidencias.pdf');
                }, 500); // Espera 500ms para asegurar que el gráfico esté listo
            });
        });
    </script>
    <script>
    // Obtener los elementos del formulario
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    // Guardar el valor original de las fechas
    let originalStartDate = startDateInput.value;
    let originalEndDate = endDateInput.value;

    // Función para validar las fechas
    function validateDates(event) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        // Si la fecha de inicio es posterior a la fecha de fin
        if (startDate > endDate) {
            // Muestra la alerta de SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La fecha de inicio no puede ser posterior a la fecha de fin.',
                confirmButtonText: 'Aceptar'
            });

            // Restaurar el valor original de la fecha de fin (sin cambios)
            if (event.target === startDateInput) {
                startDateInput.value = originalStartDate;
            } else {
                endDateInput.value = originalEndDate;
            }

            // Establece la validación personalizada
            endDateInput.setCustomValidity('La fecha de inicio no puede ser posterior a la fecha de fin.');
            event.preventDefault(); // Prevenir el cambio en el valor del campo de fecha
        } else {
            // Si las fechas son correctas, limpia la validación
            endDateInput.setCustomValidity('');
        }
    }

    // Evento para validar cuando se cambian las fechas
    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
</script>

    <!-- Scripts locales -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
@endsection