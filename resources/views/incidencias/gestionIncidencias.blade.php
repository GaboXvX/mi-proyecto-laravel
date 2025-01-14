<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Incidencias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        /* Estilos básicos */
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
        <!-- Formulario de selección de fechas -->
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
                            @if($incidencia->estado != 'atendido')
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
    // Al cambiar las fechas de inicio o fin, realizar el filtrado automáticamente
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    // Escuchar cambios en los campos de fecha
    [fechaInicio, fechaFin].forEach(input => {
        input.addEventListener('change', function() {
            let fechaInicioValue = fechaInicio.value;
            let fechaFinValue = fechaFin.value;

            // Verificar que ambas fechas sean seleccionadas
            if (fechaInicioValue && fechaFinValue) {
                console.log('Rango de fechas seleccionado:', fechaInicioValue, 'a', fechaFinValue);

                fetch('/filtrar-incidencia', {
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
