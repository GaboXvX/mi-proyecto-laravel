<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="Fri, 01 Jan 1990 00:00:00 GMT">
    

    <title>Minaguas - Sistema de Atención de Aguas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f1f3f5;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .hero-section {
            background-color: #003366;
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-family: 'Courier New', Courier, monospace;
            border-right: 0.15em solid #fff;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
            animation: typing 3s steps(30) 1s forwards, blink 0.75s step-end infinite;
        }

        .hero-section p {
            font-size: 1.2rem;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
        }

        .btn-custom:hover {
            background-color: #0056b3;
            color: white;
        }

        .card {
            margin-top: 30px;
        }

        @keyframes typing {
            from {
                width: 0;
            }

            to {
                width: 100%;
            }
        }

        @keyframes blink {
            50% {
                border-color: transparent;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Minaguas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('personas.index') }}">Personas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lideres.index') }}">Líderes Comunitarios</a>
                    </li>
                 
                   @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usuarios.index') }}">Usuarios</a>
                    </li>
                    @endrole
                    @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('estadisticas') }}">estadisticas</a>
                    </li>
                    @endrole
                    @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('movimientos') }}">movimientos</a>
                    </li>
                    @endrole
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usuarios.configuracion') }}">Configuración</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1>Bienvenidos al Sistema Minaguas</h1>
            <p>Ministerio del Poder Popular para la Atención de las Aguas</p>
            <a href="{{ route('personas.create') }}" class="btn btn-custom btn-lg">Registrar Persona</a>
        </div>
    </section>

    <div class="container">
        
        <div class="row mt-4">
            @role('admin')
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Usuarios</div>
                    <div class="card-body">
                        <h3>{{ $totalUsuarios }}</h3>
                        <p>Total de usuarios registrados.</p>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-primary">Gestionar Usuarios</a>
                    </div>
                </div>
            </div>
            @endrole
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Incidencias</div>
                    <div class="card-body">
                        <h3>{{ $totalIncidencias }}</h3>
                        <p>Total de incidencias registradas.</p>
                        <a href="{{ route('incidencias.index') }}" class="btn btn-primary">Gestionar Incidencias</a>
                    </div>
                </div>
            </div>
           @role('admin')
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Peticiones</div>
                    <div class="card-body">
                        <h3>{{ $totalPeticiones }}</h3>
                        <p>Total de peticiones pendientes.</p>
                        <a href="{{ route('peticiones.index') }}" class="btn btn-primary">Gestionar Peticiones</a>
                    </div>
                </div>
            </div>
            @endrole
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Personas</div>
                    <div class="card-body">
                        <h3>{{ $totalPersonas }}</h3>
                        <p>Total de personas registradas.</p>
                        <a href="{{ route('personas.index') }}" class="btn btn-primary">Gestionar Personas</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h2>Accesos Directos</h2>
                <div class="list-group">
                    <a href="{{ route('personas.create') }}" class="list-group-item list-group-item-action">Registrar Nueva Persona</a>
                    <a href="{{ route('lideres.index') }}" class="list-group-item list-group-item-action">Gestionar Líderes</a>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h2>Búsquedas Rápidas</h2>
                <form action="{{ route('personas.buscar') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control" name="buscar" placeholder="Buscar persona..." required>
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>

    <footer class="mt-5 py-4 text-center" style="background-color: #003366; color: white;">
        <p>&copy; 2024 Ministerio del Poder Popular para la Atención de las Aguas | Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
