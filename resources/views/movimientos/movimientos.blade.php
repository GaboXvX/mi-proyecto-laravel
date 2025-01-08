<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Movimientos</title>

    <!-- Enlace a Bootstrap para diseño mejorado -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }

        table {
            width: 100%;
            margin-top: 20px;
            font-size: 0.9rem; /* Tamaño de la fuente reducido */
        }

        th,
        td {
            text-align: left;
            vertical-align: middle;
            padding: 6px 10px; /* Menor padding */
        }

        th {
            background-color: #f1f3f5;
            color: #495057;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f3f5;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination li {
            margin-right: 5px;
        }

        /* Ajustar ancho de las columnas */
        .col-id {
            width: 5%;
        }

        .col-accion {
            width: 15%;
        }

        .col-valores {
            width: 20%;
        }

        .col-relacion {
            width: 10%;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Historial de Movimientos</h1>

        @if ($movimientos->isEmpty())
            <div class="alert alert-warning">
                No hay movimientos registrados.
            </div>
        @else
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="col-id">Movimiento n°</th>
                        <th class="col-accion">Acción</th>
                        <th class="col-valores">Valores Nuevos</th>
                        <th class="col-valores">Valores Antiguos</th>
                        <th class="col-relacion">Usuario</th>
                        <th class="col-relacion">Persona</th>
                        <th class="col-relacion">Líder</th>
                        <th class="col-relacion">Incidencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $movimiento)
                        @php
                            // Decodificar los valores anteriores y nuevos
                            $valorAnterior = json_decode($movimiento->valor_anterior, true) ?? [];
                            $valorNuevo = json_decode($movimiento->valor_nuevo, true) ?? [];
                        @endphp
                        <tr>
                            <td class="col-id">{{ $movimiento->id_movimiento }}</td>
                            <td class="col-accion">{{ $movimiento->accion }}</td>

                            <!-- Valores Nuevos -->
                            <td class="col-valores">
                                @foreach(['nombre', 'apellido', 'cedula', 'correo', 'telefono', 'comunidad', 'sector', 'calle', 'manzana', 'numero_de_casa', 'parroquia'] as $campo)
                                    @if (isset($valorNuevo[$campo]) && $valorNuevo[$campo] != '')
                                        {{ ucfirst($campo) }}: {{ htmlspecialchars($valorNuevo[$campo]) }}<br>
                                    @endif
                                @endforeach

                                <!-- Urbanización -->
                                @if (isset($valorNuevo['urbanizacion']) && $valorNuevo['urbanizacion'] != '')
                                    @php
                                        $urbanizacionNuevo = is_array($valorNuevo['urbanizacion']) ? implode(', ', $valorNuevo['urbanizacion']) : $valorNuevo['urbanizacion'];
                                        $urbanizacionNuevo = strip_tags($urbanizacionNuevo); // Eliminar cualquier HTML extra
                                    @endphp
                                    Urbanización: {{ htmlspecialchars($urbanizacionNuevo) }}<br>
                                @endif

                                <!-- Líder: Mostrar datos del líder -->
                                @if (isset($valorNuevo['lider']) && $valorNuevo['lider'] != '')
                                    Líder: {{ htmlspecialchars($valorNuevo['lider']) }}<br>
                                @endif
                            </td>

                            <!-- Valores Antiguos -->
                            <td class="col-valores">
                                @foreach(['nombre', 'apellido', 'cedula', 'correo', 'telefono', 'comunidad', 'sector', 'calle', 'manzana', 'numero_de_casa', 'parroquia'] as $campo)
                                    @if (isset($valorAnterior[$campo]) && $valorAnterior[$campo] != '')
                                        {{ ucfirst($campo) }}: {{ htmlspecialchars($valorAnterior[$campo]) }}<br>
                                    @endif
                                @endforeach

                                <!-- Urbanización -->
                                @if (isset($valorAnterior['urbanizacion']) && $valorAnterior['urbanizacion'] != '')
                                    @php
                                        $urbanizacionAnterior = is_array($valorAnterior['urbanizacion']) ? implode(', ', $valorAnterior['urbanizacion']) : $valorAnterior['urbanizacion'];
                                        $urbanizacionAnterior = strip_tags($urbanizacionAnterior); // Eliminar cualquier HTML extra
                                    @endphp
                                    Urbanización: {{ htmlspecialchars($urbanizacionAnterior) }}<br>
                                @endif

                                <!-- Líder: Mostrar datos del líder en valor anterior -->
                                @if (isset($valorAnterior['lider']) && $valorAnterior['lider'] != '')
                                    Líder: {{ htmlspecialchars($valorAnterior['lider']) }}<br>
                                @endif

                                <!-- Información de la incidencia (excepto ID) -->
                                @if ($movimiento->incidencia)
                                    <strong>Tipo de Incidencia:</strong> {{ $movimiento->incidencia->tipo_incidencia }}<br>
                                    <strong>Descripción:</strong> {{ $movimiento->incidencia->descripcion }}<br>
                                    <strong>Nivel de Prioridad:</strong> {{ $movimiento->incidencia->nivel_prioridad }}<br>
                                    <strong>Estado:</strong> {{ $movimiento->incidencia->estado }}<br>
                                @endif
                            </td>

                            <!-- Usuario: Cédula, Nombre, Apellido -->
                            <td class="col-relacion">
                                @if ($movimiento->usuario)
                                    Cédula: {{ $movimiento->usuario->cedula }}<br>
                                    Nombre: {{ $movimiento->usuario->nombre }}<br>
                                    Apellido: {{ $movimiento->usuario->apellido }}<br>
                                @endif
                            </td>

                            <!-- Persona: Cédula, Nombre, Apellido -->
                            <td class="col-relacion">
                                @if ($movimiento->persona)
                                    Cédula: {{ $movimiento->persona->cedula }}<br>
                                    Nombre: {{ $movimiento->persona->nombre }}<br>
                                    Apellido: {{ $movimiento->persona->apellido }}<br>
                                @endif
                            </td>

                            <!-- Líder: Cédula, Nombre, Apellido -->
                            <td class="col-relacion">
                                @if ($movimiento->lider)
                                    Cédula: {{ $movimiento->lider->cedula }}<br>
                                    Nombre: {{ $movimiento->lider->nombre }}<br>
                                    Apellido: {{ $movimiento->lider->apellido }}<br>
                                @endif
                            </td>

                            <!-- Incidencia: Mostrar solo el ID -->
                            <td class="col-relacion">
                                @if ($movimiento->incidencia)
                                    <strong>ID Incidencia:</strong> {{ $movimiento->incidencia->id_incidencia }}<br>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginación -->
            <div class="pagination">
                {{ $movimientos->links() }}
            </div>
        @endif
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
