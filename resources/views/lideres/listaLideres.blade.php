<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Liders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        table {
            border-collapse: collapse;
        }

        td,
        th {
            padding: 12px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-group .btn {
            flex: 1 0 auto;
        }

    </style>
</head>

<body class="bg-light">

    <div class="container my-5">
        <h1 class="mb-4 text-center">Lista de Lideres</h1>
        
         <form action="{{ route('lideres.buscar') }}" method="POST">
            @csrf
            <input type="search" name="buscar" id="buscar" placeholder="Buscar por cédula">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form> 
        
        
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-3">
          
           
            <a href="{{ route('home') }}" class="btn btn-primary">Volver</a>
             <a href="{{ route('lideres.create') }}" class="btn btn-primary">registrar lider</a>
      
        </div>
        @if (!empty($lideres) && count($lideres) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Correo Electrónico</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lideres as $lider)
                        <tr>
                            <td>{{ $lider->nombre }}</td>
                            <td>{{ $lider->apellido }}</td>
                            <td>{{ $lider->cedula }}</td>
                            <td>{{ $lider->correo }}</td>
                            <td>{{ $lider->telefono }}</td>
                            <td>
                               
                                <div class="btn-group">
                                    
                                    <a href="{{ route('lideres.show',$lider->slug) }}" class="btn btn-info btn-sm">Ver</a>                 
                                    <a href="{{ route('incidenciaslider.create', $lider->slug) }}" class="btn btn-success btn-sm">Añadir Incidencia</a>
                                    <a href="{{ route('lideres.edit',$lider->slug) }}" class="btn btn-info btn-sm">modificar</a>                 
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
          <!-- Si no se encontró ninguna persona -->
          <p class="alert alert-warning">No se encontró ningun lider con esa cédula.</p>
          @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
