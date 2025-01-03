<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Movimientos</title>

    {{-- Estilos internos --}}
    <style>
        /* Estilos básicos de la tabla */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background-color: #fff;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.9rem;
            /* Reducir tamaño de fuente */
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px 10px;
            /* Reducir padding */
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: normal;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .pagination {
            justify-content: center;
            margin-top: 15px;
            display: flex;
        }

        .pagination li {
            list-style: none;
            margin-right: 5px;
        }

        .pagination li a {
            padding: 4px 8px;
            /* Reducir tamaño de paginación */
            color: #007bff;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .pagination li a:hover {
            background-color: #007bff;
            color: white;
        }

        .pagination .active a {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .alert {
            padding: 10px;
            background-color: #ffc107;
            color: #333;
            margin-bottom: 20px;
        }

        .alert.warning {
            background-color: #ffcc00;
        }

        ul {
            margin: 0;
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Lista de Movimientos</h1>

        {{-- Verificar si hay movimientos --}}
        @if ($movimientos->isEmpty())
            <div class="alert alert-warning">
                No hay movimientos registrados.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>movimiento n°</th>
                        <th>ID Incidencia</th>
                        <th>Responsable</th>
                        <th>ID Persona</th>
                        <th>ID Líder</th>
                        <th>Valor Nuevo</th>
                        <th>Valor Anterior</th>
                        <th>Acción</th>
                        <th>Fecha de Creación</th>
                        <th>Última Actualización</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->id_movimiento }}</td>
                            <td>{{ $movimiento->id_incidencia }}</td>
                            <td>{{ $movimiento->usuario->nombre_usuario }} {{ $movimiento->usuario->cedula }}</td>
                            <td>@if($movimiento->Persona)
                               {{ $movimiento->persona->cedula}}
                                 @else
                                 no registrado
                               @endif
                            </td>
                            <td> @if($movimiento->lider)
                               {{ $movimiento->lider->cedula }}
                                @else
                                  no registrado
                                @endif
                            </td>

                            {{-- Mostrar valor nuevo de forma legible --}}
                            <td>
                                @php
                                    $valorNuevo = json_decode($movimiento->valor_nuevo, true);
                                @endphp

                                @if (is_array($valorNuevo))
                                    <ul>
                                        @foreach ($valorNuevo as $key => $value)
                                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $movimiento->valor_nuevo }} {{-- Si no es un JSON válido, mostrar el valor tal cual --}}
                                @endif
                            </td>

                            {{-- Mostrar valor anterior de forma legible --}}
                            <td>
                                @php
                                    $valorAnterior = json_decode($movimiento->valor_anterior, true);
                                @endphp

                                @if (is_array($valorAnterior))
                                    <ul>
                                        @foreach ($valorAnterior as $key => $value)
                                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $movimiento->valor_anterior }} {{-- Si no es un JSON válido, mostrar el valor tal cual --}}
                                @endif
                            </td>

                            <td>{{ $movimiento->accion }}</td>
                            <td>{{ \Carbon\Carbon::parse($movimiento->created_at)->format('d-m-Y h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($movimiento->updated_at)->format('d-m-Y h:i A') }}</td>
                            

                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginación --}}
            <div class="pagination">
                {{ $movimientos->links() }}
            </div>
        @endif
    </div>

</body>

</html>
