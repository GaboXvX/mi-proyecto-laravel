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
                            $valorAnterior = json_decode($movimiento->valor_anterior, true);
                            $valorNuevo = json_decode($movimiento->valor_nuevo, true);
                        @endphp
                        <tr>
                            <td class="col-id">{{ $movimiento->id_movimiento }}</td>
                            <td class="col-accion">{{ $movimiento->accion }}</td>

                            <!-- Valores Nuevos -->
                            <td class="col-valores">
                                @if (isset($valorNuevo['nombre']) && $valorNuevo['nombre'] != ($valorAnterior['nombre'] ?? ''))
                                    Nombre: {{ htmlspecialchars($valorNuevo['nombre']) }}<br>
                                @endif
                                @if (isset($valorNuevo['apellido']) && $valorNuevo['apellido'] != ($valorAnterior['apellido'] ?? ''))
                                    Apellido: {{ htmlspecialchars($valorNuevo['apellido']) }}<br>
                                @endif
                                @if (isset($valorNuevo['cedula']) && $valorNuevo['cedula'] != ($valorAnterior['cedula'] ?? ''))
                                    Cédula: {{ htmlspecialchars($valorNuevo['cedula']) }}<br>
                                @endif
                                @if (isset($valorNuevo['correo']) && $valorNuevo['correo'] != ($valorAnterior['correo'] ?? ''))
                                    Correo: {{ htmlspecialchars($valorNuevo['correo']) }}<br>
                                @endif
                                @if (isset($valorNuevo['telefono']) && $valorNuevo['telefono'] != ($valorAnterior['telefono'] ?? ''))
                                    Teléfono: {{ htmlspecialchars($valorNuevo['telefono']) }}<br>
                                @endif
                                @if (isset($valorNuevo['comunidad']) && $valorNuevo['comunidad'] != ($valorAnterior['comunidad'] ?? ''))
                                    Comunidad: {{ htmlspecialchars($valorNuevo['comunidad']) }}<br>
                                @endif
                                @if (isset($valorNuevo['sector']) && $valorNuevo['sector'] != ($valorAnterior['sector'] ?? ''))
                                    Sector: {{ htmlspecialchars($valorNuevo['sector']) }}<br>
                                @endif
                                @if (isset($valorNuevo['calle']) && $valorNuevo['calle'] != ($valorAnterior['calle'] ?? ''))
                                    Calle: {{ htmlspecialchars($valorNuevo['calle']) }}<br>
                                @endif
                                @if (isset($valorNuevo['manzana']) && $valorNuevo['manzana'] != ($valorAnterior['manzana'] ?? ''))
                                    Manzana: {{ htmlspecialchars($valorNuevo['manzana']) }}<br>
                                @endif
                                @if (isset($valorNuevo['numero_de_casa']) && $valorNuevo['numero_de_casa'] != ($valorAnterior['numero_de_casa'] ?? ''))
                                    Número de Casa: {{ htmlspecialchars($valorNuevo['numero_de_casa']) }}<br>
                                @endif
                                @if (isset($valorNuevo['parroquia']) && $valorNuevo['parroquia'] != ($valorAnterior['parroquia'] ?? ''))
                                    Parroquia: {{ htmlspecialchars($valorNuevo['parroquia']) }}<br>
                                @endif
                                @if (isset($valorNuevo['urbanizacion']) && $valorNuevo['urbanizacion'] != ($valorAnterior['urbanizacion'] ?? ''))
                                    Urbanización: {{ htmlspecialchars($valorNuevo['urbanizacion']) }}<br>
                                @endif
                            </td>

                            <!-- Valores Antiguos -->
                            <td class="col-valores">
                                @if (isset($valorAnterior['nombre']) && $valorAnterior['nombre'] != ($valorNuevo['nombre'] ?? ''))
                                    Nombre: {{ htmlspecialchars($valorAnterior['nombre']) }}<br>
                                @endif
                                @if (isset($valorAnterior['apellido']) && $valorAnterior['apellido'] != ($valorNuevo['apellido'] ?? ''))
                                    Apellido: {{ htmlspecialchars($valorAnterior['apellido']) }}<br>
                                @endif
                                @if (isset($valorAnterior['cedula']) && $valorAnterior['cedula'] != ($valorNuevo['cedula'] ?? ''))
                                    Cédula: {{ htmlspecialchars($valorAnterior['cedula']) }}<br>
                                @endif
                                @if (isset($valorAnterior['correo']) && $valorAnterior['correo'] != ($valorNuevo['correo'] ?? ''))
                                    Correo: {{ htmlspecialchars($valorAnterior['correo']) }}<br>
                                @endif
                                @if (isset($valorAnterior['telefono']) && $valorAnterior['telefono'] != ($valorNuevo['telefono'] ?? ''))
                                    Teléfono: {{ htmlspecialchars($valorAnterior['telefono']) }}<br>
                                @endif
                                @if (isset($valorAnterior['comunidad']) && $valorAnterior['comunidad'] != ($valorNuevo['comunidad'] ?? ''))
                                    Comunidad: {{ htmlspecialchars($valorAnterior['comunidad']) }}<br>
                                @endif
                                @if (isset($valorAnterior['sector']) && $valorAnterior['sector'] != ($valorNuevo['sector'] ?? ''))
                                    Sector: {{ htmlspecialchars($valorAnterior['sector']) }}<br>
                                @endif
                                @if (isset($valorAnterior['calle']) && $valorAnterior['calle'] != ($valorNuevo['calle'] ?? ''))
                                    Calle: {{ htmlspecialchars($valorAnterior['calle']) }}<br>
                                @endif
                                @if (isset($valorAnterior['manzana']) && $valorAnterior['manzana'] != ($valorNuevo['manzana'] ?? ''))
                                    Manzana: {{ htmlspecialchars($valorAnterior['manzana']) }}<br>
                                @endif
                                @if (isset($valorAnterior['numero_de_casa']) && $valorAnterior['numero_de_casa'] != ($valorNuevo['numero_de_casa'] ?? ''))
                                    Número de Casa: {{ htmlspecialchars($valorAnterior['numero_de_casa']) }}<br>
                                @endif
                                @if (isset($valorAnterior['parroquia']) && $valorAnterior['parroquia'] != ($valorNuevo['parroquia'] ?? ''))
                                    Parroquia: {{ htmlspecialchars($valorAnterior['parroquia']) }}<br>
                                @endif
                                @if (isset($valorAnterior['urbanizacion']) && $valorAnterior['urbanizacion'] != ($valorNuevo['urbanizacion'] ?? ''))
                                    Urbanización: {{ htmlspecialchars($valorAnterior['urbanizacion']) }}<br>
                                @endif
                            </td>

                            <!-- Usuario: Cédula, Nombre, Apellido -->
                            <td class="col-relacion">
                                @if ($movimiento->usuario)
                                    Cédula: {{ $movimiento->usuario->cedula }}<br>
                                    Nombre: {{ $movimiento->usuario->nombre }}<br>
                                    Apellido: {{ $movimiento->usuario->apellido }}<br>
                                @else
                                    No disponible
                                @endif
                            </td>

                            <!-- Persona: Cédula, Nombre, Apellido -->
                            <td class="col-relacion">
                                @if ($movimiento->persona)
                                    Cédula: {{ $movimiento->persona->cedula }}<br>
                                    Nombre: {{ $movimiento->persona->nombre }}<br>
                                    Apellido: {{ $movimiento->persona->apellido }}<br>
                                @else
                                    No disponible
                                @endif
                            </td>

                            <!-- Líder: Cédula, Nombre, Apellido -->
                            <td class="col-relacion">
                                @if ($movimiento->lider)
                                    Cédula: {{ $movimiento->lider->cedula }}<br>
                                    Nombre: {{ $movimiento->lider->nombre }}<br>
                                    Apellido: {{ $movimiento->lider->apellido }}<br>
                                @else
                                    No disponible
                                @endif
                            </td>

                            <!-- Incidencia: ID -->
                            <td class="col-relacion">
                                @if ($movimiento->incidencia)
                                    {{ $movimiento->incidencia->id_incidencia }}
                                @else
                                    No disponible
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
