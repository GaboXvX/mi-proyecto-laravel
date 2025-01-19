<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico de Incidencias</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> <!-- Incluir jsPDF -->
    <style>
        /* Estilos para el botón de descarga */
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
</head>
<body>

    <div class="filter-container">
        <form action="{{ route('estadisticas') }}" method="GET">
            <!-- Filtro de fecha -->
            <label for="start_date">Fecha de inicio:</label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate->toDateString() }}">
            
            <label for="end_date">Fecha de fin:</label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate->toDateString() }}">

            <!-- Filtro de tipo de incidencia -->
            <label for="tipo_incidencia">Tipo de incidencia:</label>
            <select id="tipo_incidencia" name="tipo_incidencia">
                <option value="" {{ $tipoIncidencia == '' ? 'selected' : '' }}>Todos</option>
                <option value="agua potable" {{ $tipoIncidencia == 'agua potable' ? 'selected' : '' }}>Agua Potable</option>
                <option value="agua servida" {{ $tipoIncidencia == 'agua servida' ? 'selected' : '' }}>Agua Servida</option>
            </select>

            <button type="submit">Filtrar</button>
        </form>
    </div>

    <div style="width: 80%; margin: 0 auto;">
        <canvas id="myChart" width="600" height="300"></canvas>
    </div>

    <button class="download-btn" id="downloadPdfBtn">Descargar PDF</button>

    <script>
        var ctx = document.getElementById('myChart').getContext('2d');

        var myChart = new Chart(ctx, {
            type: 'bar',  
            data: {
                labels: @json($labels),  
                datasets: [
                    {
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
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                        },
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
            const doc = new jsPDF();
            const imageUrl = myChart.toBase64Image();

           
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

            
            doc.addImage(imageUrl, 'PNG', 10, currentY, 180, 90);

            
            doc.save('informe_incidencias.pdf');
        });
    </script>

</body>
</html>
