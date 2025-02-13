<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}" />
    <title>Minaguas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
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
</head>    
<body>
    <nav class="sidebar d-flex flex-column p-3" id="sidebar">
        <a href="{{route('home')}}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
            <img src="{{ asset('img/splash.webp') }}" alt="logo" width="40px">
            <span class="fs-5 fw-bold ms-2 px-3">MinAguas</span>
        </a>
        <hr class="text-secondary">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Panel</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('lideres.index') }}" class="nav-link">
                    <i class="bi bi-person-badge"></i>
                    <span>Líderes Comunitarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#layouts" class="nav-link" data-bs-toggle="collapse">
                    <i class="bi bi-search"></i>
                    <span>Consultar</span>
                    <span class="right-icon px-2"><i class="bi bi-chevron-down"></i></span>
                </a>
                <div class="collapse" id="layouts">
                    <ul class="navbar-nav ps-3">
                        @role('admin')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="nav-link px-3">
                                <i class="bi bi-people"></i>
                                <span>Usuarios</span>
                            </a>
                        </li>
                        @endrole
                        <li>
                            <a href="{{ route('personas.index') }}" class="nav-link px-3">
                                <i class="bi bi-person-circle"></i>
                                <span>Personas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('incidencias.index') }}" class="nav-link px-3">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Incidencias</span>
                            </a>
                        </li>
                        @role('admin')
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="nav-link px-3">
                                <i class="bi bi-envelope"></i>
                                <span>Peticiones</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('movimientos.index') }}" class="nav-link px-3">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Movimientos</span>
                            </a>
                        </li>
                        @endrole
                    </ul>
                </div>
            </li>
            @role('admin')
            <li class="nav-item">
                <a href="{{ route('estadisticas') }}" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Estadísticas</span>
                </a>
            </li>
            @endrole
        </ul>
        <hr class="text-secondary">
    </nav>
    
    <div class="main-content">
        <div class="topbar d-flex align-items-center justify-content-between">
            <button class="btn btn-light burger-btn" id="menuToggle">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <button class="btn btn-light me-2">
                    <i class="bi bi-bell"></i>
                </button>
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('usuarios.configuracion') }}">Configuración</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    
        <div class="filter-container">
            <form action="{{ route('estadisticas') }}" method="GET">
                <div class="row g-3 align-items-end filter-section">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de inicio:</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate->toDateString() }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de fin:</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate->toDateString() }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo de incidencia:</label>
                        <select id="tipo_incidencia" name="tipo_incidencia" class="form-select">
                            <option value="" {{ $tipoIncidencia == '' ? 'selected' : '' }}>Todos</option>
                            <option value="agua potable" {{ $tipoIncidencia == 'agua potable' ? 'selected' : '' }}>Agua Potable</option>
                            <option value="agua servida" {{ $tipoIncidencia == 'agua servida' ? 'selected' : '' }}>Agua Servida</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
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

            document.getElementById('menuToggle').addEventListener('click', function() {
                var sidebar = document.getElementById('sidebar');
                var mainContent = document.querySelector('.main-content');

                sidebar.classList.toggle('collapsed');  
                mainContent.classList.toggle('collapsed');  
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

                doc.addImage(imageUrl, 'PNG', 10, currentY + 10, 180, 80);

                doc.save('incidencias.pdf');
            });
            document.getElementById('menuToggle').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var mainContent = document.querySelector('.main-content');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
        </script>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
