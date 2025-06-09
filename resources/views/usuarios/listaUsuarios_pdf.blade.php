<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h1 {
            font-size: 18px;
            margin: 5px 0;
            color: #333;
        }
        h2 {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
            text-align: center;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
            padding-top: 10px;
        }
        .text-center {
            text-align: center;
        }
        .section-title {
            background-color: #f2f2f2;
            padding: 8px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            border-left: 4px solid #333;
        }
        .status-active {
            color: green;
        }
        .status-inactive {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="text-center">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" style="max-height: 80px; margin-bottom: 10px;"><br>
            @endif
            {!! $membrete ?? '' !!}
        </div>
    </div>

    <h2>Lista Completa de Empleados</h2>
    
    <div class="section-title">Empleados Registrados en el Sistema</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cédula</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuariosRegistrados as $index => $usuario)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $usuario->empleadoAutorizado->nombre ?? 'N/A' }}</td>
                    <td>{{ $usuario->empleadoAutorizado->apellido ?? 'N/A' }}</td>
                    <td>
                        @if($usuario->empleadoAutorizado)
                            {{ $usuario->empleadoAutorizado->nacionalidad ?? 'V' }}-{{ $usuario->empleadoAutorizado->cedula ?? '' }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        @switch($usuario->id_estado_usuario)
                            @case(1) Aceptado @break
                            @case(2) Desactivado @break
                            @case(3) No Verificado @break
                            @case(4) Rechazado @break
                            @default Desconocido
                        @endswitch
                    </td>
                    <td>{{ $usuario->created_at->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Empleados Autorizados (No Registrados)</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cédula</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th>Fecha Autorización</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empleadosSinUsuario as $index => $empleado)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $empleado->nombre }}</td>
                    <td>{{ $empleado->apellido }}</td>
                    <td>{{ $empleado->nacionalidad }}-{{ $empleado->cedula }}</td>
                    <td>{{ $empleado->cargo->nombre_cargo ?? 'No definido' }}</td>
                    <td class="{{ $empleado->es_activo ? 'status-active' : 'status-inactive' }}">
                        {{ $empleado->es_activo ? 'Activo' : 'Inactivo' }}
                    </td>
                    <td>{{ $empleado->created_at->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        @isset($pie_html)
            {!! $pie_html !!}<br>
        @endisset
        <span style="color: #6c757d; font-size: 0.9em;">
            Generado el {{ now()->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}
        </span>
    </footer>
</body>
</html>