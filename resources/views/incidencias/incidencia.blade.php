<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 30px;
            color: #003366;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #777;
            margin-top: 0;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #003366;
        }

        .invoice-details p {
            font-size: 14px;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #888;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin: 5px;
        }

        .btn-download {
            background-color: #28a745;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #6c757d;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        @media print {
            .button-container {
                display: none; 
            }
        }
    </style>
    <title>configuracion</title>
</head>

<body>
    <nav class="sidebar d-flex flex-column p-3" id="sidebar">
        <a href="{{ route('home') }}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
            <img src="{{ asset('img/splash.webp') }}" alt="logo" width="40px">
            <span class="fs-5 fw-bold ms-2 px-3">MinAguas</span>
        </a>
        <hr class="text-secondary">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Panel</span>
                </a>
            </li>
           
            <li class="nav-item">
                <a href="#layouts" class="nav-link" data-bs-toggle="collapse">
                    <i class="bi bi-search"></i>
                    <span>Consultar</span>
                    <span class="right-icon px-2"><i class="bi bi-chevron-down"></i></span>
                </a>
                <div class="collapse" id="layouts">
                    <ul class="navbar-nav ps-3">
                        @role('admin')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="nav-link px-3">
                                <i class="bi bi-people"></i>
                                <span>Usuarios</span>
                            </a>
                        </li>
                        @endrole
                        <li>
                            <a href="{{ route('personas.index') }}" class="nav-link px-3">
                                <i class="bi bi-person-circle"></i>
                                <span>Personas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('incidencias.index') }}" class="nav-link px-3">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Incidencias</span>
                            </a>
                        </li>
                        @role('admin')
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="nav-link px-3">
                                <i class="bi bi-envelope"></i>
                                <span>Peticiones</span>
                            </a>
                        </li>
                       
                        @endrole
                    </ul>
                </div>
            </li>
            @role('admin')
            <li class="nav-item">
                <a href="{{ route('estadisticas') }}" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Estadísticas</span>
                </a>
            </li>
            @endrole
        </ul>
        <hr class="text-secondary">
    </nav>

    <main class="main-content">
        <div class="topbar d-flex align-items-center justify-content-between">
            <button class="btn btn-light burger-btn" id="menuToggle">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <button class="btn btn-light me-2">
                    <i class="bi bi-bell"></i>
                </button>
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('usuarios.configuracion') }}">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

   

    <div class="container" id="comprobante-container">
        <div class="header">
            <h1>Comprobante de Incidencia</h1>
            <p>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</p>
        </div>

        <div class="invoice-details">
            <h3>Información de la Incidencia:</h3>
            <div class="details-section">
                <p><strong>Código de Incidencia:</strong> {{ $incidencia->cod_incidencia }}</p>
                <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
                <p><strong>Tipo de Incidencia:</strong> {{ $incidencia->tipo_incidencia }}</p>
                <p><strong>Nivel de Prioridad:</strong> {{ $incidencia->nivel_prioridad }}</p>
                <p><strong>Estado:</strong> {{ $incidencia->estado }}</p>
                <p><strong>Fecha de Creación:</strong> {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if($incidencia->id_persona)
            <div class="details-section">
                <p><strong>Persona Afectada:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
                    @if($incidencia->persona->es_lider==1)
                    <p><strong>¿Es lider? </strong> <br>
                    {{$incidencia->persona->es_lider ? 'si' :'No'}}
                    @else
                    <p><strong>Lugar de la incidencia:</strong></p>

                    @if($incidencia->direccion)
                        <p>
                            <strong>Estado:</strong> {{ $incidencia->direccion->estado->nombre }},
                            <strong>Municipio:</strong> {{ $incidencia->direccion->municipio->nombre }},
                            <strong>Parroquia:</strong> {{ $incidencia->direccion->parroquia->nombre }},
                            <strong>Urbanización:</strong> {{ $incidencia->direccion->urbanizacion->nombre }},
                            <strong>Sector:</strong> {{ $incidencia->direccion->sector->nombre }},
                            <strong>Comunidad:</strong> {{ $incidencia->direccion->comunidad->nombre }},
                            <strong>Calle:</strong> {{ $incidencia->direccion->calle }},
                            
                            @if($incidencia->direccion->manzana)
                                <strong>Manzana:</strong> {{ $incidencia->direccion->manzana }},
                            @endif
                    
                            @if($incidencia->direccion->numero_de_vivienda)
                                <strong>N° de vivienda:</strong> {{ $incidencia->direccion->numero_de_vivienda }},
                            @endif
                        </p>
                    @else
                        <p><em>No hay dirección asociada a esta incidencia.</em></p>
                    @endif
                    
                    
                    <p><strong>Lider comunitario </strong> <br>
                        @if($incidencia->lider)
                        {{$incidencia->lider->personas->nombre ?? 'Nombre no disponible'}} 
                        {{$incidencia->lider->personas->apellido ?? 'Nombre no disponible'}} <strong>V-</strong>
                        {{$incidencia->lider->personas->cedula ?? 'Nombre no disponible'}}
                    @else
                        <p>No tiene un líder asignado</p>
                    @endif
                                        @endif
            </div>
        @endif

        <div class="details-section">
            <p><strong>Registrado por:</strong> 
                @if($incidencia->usuario)
                    @if($incidencia->usuario->empleadoAutorizado)
                        {{ $incidencia->usuario->empleadoAutorizado->nombre }} {{ $incidencia->usuario->empleadoAutorizado->apellido }}
                        <strong>V-</strong>{{ $incidencia->usuario->empleadoAutorizado->cedula }}
                    @else
                        <em>Empleado autorizado no asignado</em>
                    @endif
                @else
                    <em>Usuario no asignado</em>
                @endif
            </p>
        </div>

        </div>

        <div class="footer">
            <p class="comprobante">
                Comprobante emitido por el Ministerio del Poder Popular para la Atención de las Aguas (Minaguas).
            </p>
        </div>

        <div class="button-container">
            <button class="btn btn-download" id="downloadPdfBtn">Descargar PDF</button>
            <a href="{{ route('personas.index') }}" class="btn btn-back">Volver</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        document.getElementById('downloadPdfBtn').addEventListener('click', function() {
            const element = document.getElementById('comprobante-container');
            
            document.querySelector('.button-container').style.display = 'none';

            html2pdf()
                .from(element)
                .save('comprobante_incidencia.pdf')
                .finally(() => {
                    document.querySelector('.button-container').style.display = 'block';
                });
        });
    </script>
</html>
