@extends('layouts.app')
@section('content')
    <style>
        .status-pending {
            color: orange;
        }

        .status-accepted {
            color: green;
            font-weight: bold;
        }

        .status-not-verified {
            color: orange;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .btn-custom {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 5px;
            width: 90px; 
            margin: 0 5px; 
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



        <!-- Alertas de éxito y errores -->
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

        <!-- Contenido -->
        <div class="container">
            <div class="table-container">
                <h2>Detalles de las Peticiones</h2>

                <table class="table table-striped align-middle" id="tablaPeticiones">
                    <thead>
                        <tr>
                            <th>Estado de Petición</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cédula</th>
                            <th>Email</th>
                            <th>Nombre de Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las filas se llenarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        async function cargarPeticiones() {
            try {
                const response = await fetch("{{ route('peticiones.index') }}", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest" // Indicar que es una solicitud AJAX
                    }
                });
                const peticiones = await response.json();

                const tablaBody = document.querySelector("#tablaPeticiones tbody");
                tablaBody.innerHTML = ""; // Limpiar la tabla antes de llenarla

                peticiones.forEach(peticion => {
                    let estadoClass = '';
                    if (peticion.id_estado_usuario == 1) {
                        estadoClass = 'status-accepted';
                    } else if (peticion.id_estado_usuario == 3) {
                        estadoClass = 'status-not-verified';
                    } else if (peticion.id_estado_usuario == 4) {
                        estadoClass = 'status-rejected';
                    }

                    const fila = `
                        <tr>
                            <td class="${estadoClass}">${peticion.estado_usuario}</td>
                            <td>${peticion.nombre}</td>
                            <td>${peticion.apellido}</td>
                            <td>${peticion.cedula}</td>
                            <td>${peticion.email}</td>
                            <td>${peticion.nombre_usuario}</td>
                            <td>
                                <div>
                                    ${
                                        peticion.id_estado_usuario == 3
                                        ? `
                                            <form action="{{ route('peticion.aceptar', '') }}/${peticion.id_usuario}" method="post" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-custom btn-accept">Aceptar</button>
                                            </form>
                                            <form action="{{ route('peticiones.rechazar', '') }}/${peticion.id_usuario}" method="post" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-custom btn-reject">Rechazar</button>
                                            </form>
                                        `
                                        : ''
                                    }
                                </div>
                            </td>
                        </tr>
                    `;
                    tablaBody.innerHTML += fila;
                });
            } catch (error) {
                console.error("Error al cargar las peticiones:", error);
            }
        }

        // Cargar las peticiones al cargar la página
        document.addEventListener("DOMContentLoaded", cargarPeticiones);

        // Opcional: Recargar las peticiones cada 30 segundos
        setInterval(cargarPeticiones, 30000);
    </script>

@endsection