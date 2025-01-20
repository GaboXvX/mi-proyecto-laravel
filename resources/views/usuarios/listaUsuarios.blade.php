<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Contenedor de la página */
        .container {
            width: 90%;
            max-width: 1100px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Título principal */
        h1 {
            text-align: center;
            color: #1E3A8A; /* Azul oscuro */
        }

        /* Estilos de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #3B82F6; /* Azul claro */
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #F0F9FF; /* Azul muy claro */
        }

        /* Estilo de los enlaces */
        a {
            color: #3B82F6; /* Azul claro */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Estilos de los alertas */
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #D1FAE5; /* Verde claro */
            color: #065F46;
        }

        .alert-error {
            background-color: #FEE2E2; /* Rojo claro */
            color: #B91C1C;
        }

        /* Botón Volver */
        .btn-volver {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #3B82F6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-volver:hover {
            background-color: #2563EB; /* Azul más oscuro */
        }

        /* Botones de acción (Editar y Deshabilitar) */
        .btn-edit,
        .btn-disable {
            padding: 8px 12px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-restaurar {
            padding: 8px 12px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-edit {
            background-color: #3B82F6; /* Azul claro */
        }

        .btn-edit:hover {
            background-color: #2563EB; /* Azul más oscuro */
        }
        .btn-restaurar {
            background-color: #44a118; /* Gris */
        }
        .btn-restaurar:hover {
            background-color: #3f7c23; /* Gris */
        }
        .btn-disable {
            background-color: #A1A1A1; /* Gris */
        }

        .btn-disable:hover {
            background-color: #6B6B6B; /* Gris oscuro */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Lista de Usuarios</h1>

        <a href="{{ route('home') }}" class="btn-volver">Volver</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cédula</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        @if($usuario->role->rol=='registrador')
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->apellido }}</td>
                        <td>{{ $usuario->cedula }}</td>
                        <td>{{ $usuario->email}}</td>
                        <td>{{ $usuario->estado }}</td>
                        <td>{{ $usuario->created_at }}</td>
                        <td>
                         <form action="{{ route('usuarios.restaurar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-restaurar">Restaurar</button>
                            </form>
                           @if($usuario->estado=="activo")
                            <form action="{{ route('usuarios.desactivar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-disable">Deshabilitar</button>
                            </form>
                           @else
                            <form action="{{ route('usuarios.activar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-disable">activar</button>
                            </form>
                             @endif
                        </td>
                    @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
