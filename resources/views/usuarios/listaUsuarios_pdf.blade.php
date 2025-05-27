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

        h2{
            font-size: 18px;
            margin: 2px 0;
            color: #333;
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
        .footer {
            text-align: right;
            font-size: 10px;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Membrete -->
    <div class="header">
        <h1>Ministerio del Poder Popular para la Atención de las Aguas</h1>
    </div>
    <h2 class="text-center">Lista de Empleados</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cédula</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Creación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->empleadoAutorizado->nombre }}</td>
                    <td>{{ $usuario->empleadoAutorizado->apellido }}</td>
                    <td>{{ $usuario->empleadoAutorizado->cedula }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        @if ($usuario->id_estado_usuario == 1) Aceptado
                        @elseif ($usuario->id_estado_usuario == 2) Desactivado
                        @elseif ($usuario->id_estado_usuario == 3) No Verificado
                        @elseif ($usuario->id_estado_usuario == 4) Rechazado
                        @else Desconocido
                        @endif
                    </td>
                    <td>{{ $usuario->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>