<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: Arial, sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
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