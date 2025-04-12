<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Incidencias</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            font-size: 0.875rem;
        }
        .table-container {
            margin: 10px auto;
            max-width: 1000px;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            font-size: 0.875rem;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        .table tbody tr:nth-child(even) {
            background-color: #ffffff;
        }
        .table .incidencia-status {
            font-weight: bold;
        }
        .status-pending {
            color: orange;
        }
        .status-resolved {
            color: green;
        }
        .status-closed {
            color: red;
        }
        .btn-atendido {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 0.875rem;
        }
        .btn-atendido:hover {
            background-color: #218838;
        }
        .btn-volver {
            background-color: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
            font-size: 0.875rem;
        }
        .btn-volver:hover {
            background-color: #0056b3;
        }
        .btn-download {
            background-color: #17a2b8;
            color: white;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: background-color 0.3s ease;
            font-size: 0.875rem;
        }
        .btn-download:hover {
            background-color: #138496;
        }
        .form-control {
            border-radius: 6px;
            font-size: 0.875rem;
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            font-weight: bold;
            font-size: 0.875rem;
            display: block;
            margin-bottom: 5px;
        }
        .table-container h1 {
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 15px;
        }
        .table-responsive {
            overflow-x: auto;
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
                <a href="#layouts" class="nav-link" data-bs-toggle="collapse">
                    <i class="bi bi-search"></i>
                    <span>Consultar</span>
                    <span class="right-icon px-2"><i class="bi bi-chevron-down"></i></span>
                </a>
                <div class="collapse" id="layouts">
                    <ul class="navbar-nav ps-3">
                        @can('ver empleados')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="nav-link px-3">
                                <i class="bi bi-people"></i>
                                <span>Usuarios</span>
                            </a>
                        </li>
                        @endcan
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
                        
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="nav-link px-3">
                                <i class="bi bi-envelope"></i>
                                <span>Peticiones</span>
                            </a>
                        </li>
                      
                        
                    </ul>
                </div>
            </li>
            @can('ver grafica incidencia')
            <li class="nav-item">
                <a href="{{ route('estadisticas') }}" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Estadísticas</span>
                </a>
            </li>
            @endcan
        </ul>
        <hr class="text-secondary">
    </nav>
    
    <main class="main-content">
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
                    <li><a class="dropdown-item" href="{{ route('usuarios.configuracion') }}">Perfil</a></li>
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

    <div class="container table-container">
        <a href="{{route('home')}}" class="btn-volver">Volver</a>
        <h1 class="text-center">Lista de Incidencias</h1>
        <form action="{{route('incidencias.buscar')}}" method="post">
            <input type="search" name="buscar" placeholder="Ingrese un código">
            @csrf
            <button type="submit"> buscar</button>
        </form>
        <br><br>
      
        <label for="fecha_inicio" class="form-label">Selecciona el rango de fechas:</label>
        <div class="d-flex">
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" />
            <span class="mx-2">hasta</span>
            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" />
        </div>

        <ul id="resultados" class="mt-3"></ul>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
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
                        <th>Marcar como Atendido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incidencias as $incidencia)
                        <tr>
                            @if($incidencia->estado != 'atendido')
                            <td>{{$incidencia->cod_incidencia}}</td>
                            <td>{{ $incidencia->tipo_incidencia }}</td>
                            <td>{{ $incidencia->descripcion }}</td>
                            <td>{{ $incidencia->nivel_prioridad }}</td>
                            <td class="incidencia-status 
                                        @if($incidencia->estado == 'Pendiente') status-pending 
                                        @elseif($incidencia->estado == 'Resuelta') status-resolved 
                                        @elseif($incidencia->estado == 'Cerrada') status-closed 
                                        @endif">
                                {{ $incidencia->estado }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($incidencia->created_at)->format('d-m-Y H:i:s') }}</td>
                            <td>
                                @if($incidencia->persona)
                                    {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}
                                @else
                                    No registrado
                                @endif
                            </td>
                            <td>
                                @if($incidencia->lider)
                                    {{ $incidencia->lider->nombre }} {{ $incidencia->lider->apellido }}
                                @else
                                    No asignado
                                @endif
                            </td>
                           
                                <td>
                                    <form action="{{route('incidencias.atender', $incidencia->slug)}}" method="post">
                                        @csrf
                                        <button type="submit" class="btn-atendido">Atendido</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
   
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');


    [fechaInicio, fechaFin].forEach(input => {
        input.addEventListener('change', function() {
            let fechaInicioValue = fechaInicio.value;
            let fechaFinValue = fechaFin.value;

           
            if (fechaInicioValue && fechaFinValue) {
                console.log('Rango de fechas seleccionado:', fechaInicioValue, 'a', fechaFinValue);

                fetch('/filtrar-incidencias-por-fechas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        fecha_inicio: fechaInicioValue,
                        fecha_fin: fechaFinValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Datos recibidos:', data);

                    let listaResultados = document.getElementById('resultados');
                    listaResultados.innerHTML = '';

                    let tbody = document.querySelector('.table tbody');
                    tbody.innerHTML = '';

                    if (data.incidencias && data.incidencias.length > 0) {
                        data.incidencias.forEach(incidencia => {
                            let tr = document.createElement('tr');

                            let fecha = new Date(incidencia.created_at);
                            let fechaFormateada = fecha.toLocaleString('es-ES', {
                                weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit',
                                hour: '2-digit', minute: '2-digit', second: '2-digit'
                            });

                            tr.innerHTML = `
                                <td>${incidencia.cod_incidencia}</td> <!-- Código de incidencia -->
                                <td>${incidencia.tipo_incidencia}</td>
                                <td>${incidencia.descripcion}</td>
                                <td>${incidencia.nivel_prioridad}</td>
                                <td class="incidencia-status ${getStatusClass(incidencia.estado)}">${incidencia.estado}</td>
                                <td>${fechaFormateada}</td>
                                <td>${incidencia.persona ? incidencia.persona.nombre + ' ' + incidencia.persona.apellido : 'No registrado'}</td>
                                <td>${incidencia.lider ? incidencia.lider.nombre + ' ' + incidencia.lider.apellido : 'No asignado'}</td>
                                ${incidencia.estado !== 'atendido' ? `
                                    <td>
                                        <form action="/incidencias/${incidencia.slug}/atender" method="post">
                                            @csrf
                                            <button type="submit" class="btn-atendido">Atendido</button>
                                        </form>
                                    </td>
                                ` : ''}
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        let tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="9" class="text-center">No se encontraron incidencias para el rango de fechas seleccionado.</td>';
                        tbody.appendChild(tr);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    });
});

function getStatusClass(estado) {
    switch (estado) {
        case 'Pendiente':
            return 'status-pending';
        case 'Resuelta':
            return 'status-resolved';
        case 'Cerrada':
            return 'status-closed';
        default:
            return '';
    }
}

    </script>
    
</body>

</html>
