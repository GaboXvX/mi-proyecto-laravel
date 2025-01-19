<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Petición</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .table-container {
            margin: 20px auto;
            max-width: 1000px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
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

        .status-pending {
            color: orange;
        }

        .status-active {
            color: green;
        }

        .status-inactive {
            color: red;
        }

        /* Estilo de los botones */
        .btn-custom {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 5px;
            width: 90px; /* Un tamaño más pequeño */
            margin: 0 5px; /* Espaciado entre botones */
        }

        .btn-accept {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .btn-accept:hover {
            background-color: #218838;
            color: white;
        }

        .btn-reject {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-reject:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>

<body>
    @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="container">
        <div class="table-container">
            <h1>Detalles de las Peticiones</h1>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Rol solicitado</th>
                        <th>Estado de Petición</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Email</th>
                        <th>Nombre de Usuario</th>
                        
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peticiones as $peticion)
                        <tr>
                            <td>{{ $peticion->rol->rol }}</td>
                            <td class="status-pending">{{ $peticion->estado_peticion }}</td>
                            <td>{{ $peticion->nombre }}</td>
                            <td>{{ $peticion->apellido }}</td>
                            <td>{{ $peticion->cedula }}</td>
                            <td>{{ $peticion->email }}</td>
                            <td>{{ $peticion->nombre_usuario }}</td>
                            

                            <td class="status-inactive">{{ $peticion->estado }}</td>
                            @if($peticion->estado_peticion=='No verificado')
                            <td>
                                <div>
                                <form action="{{route('peticion.aceptar',$peticion->id_peticion)}}" method="post" >
                                
                                    @csrf
                                    <button type="submit" class="btn-custom btn-accept">Aceptar</button>
                                </form>
                                <form action="{{route('peticiones.rechazar',$peticion->id_peticion)}}" method="post" >
                                    @csrf
                                    <button type="submit" class="btn-custom btn-reject">Rechazar</button>
                                </div>
                                @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
