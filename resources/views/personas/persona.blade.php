@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3" id="error-alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3" id="validation-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="text-center">Datos de la Persona</h2>
        <div class="mt-3">
            <!-- Botón Volver utilizando ruta de Laravel -->
            <a href="{{ route('personas.index') }}" class="btn btn-primary fw-bold">Volver</a>
        </div>

        <!-- Card para mostrar la información personal -->
        <div class="card mt-4">
            <div class="card-header">
                Información Personal
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $persona->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Apellido:</th>
                            <td>{{ $persona->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Cédula:</th>
                            <td>{{ $persona->cedula }}</td>
                        </tr>
                        <tr>
                            <th>Correo Electrónico:</th>
                            <td>{{ $persona->correo }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $persona->telefono }}</td>
                        </tr>
                        <tr>
                            <th>Responsable:</th>
                            <td>{{ $persona->user->nombre }} {{ $persona->user->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Creado en:</th>
                            <td>{{ $persona->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card para mostrar las direcciones -->
        <div class="card mt-4">
            <div class="card-header">
                Direcciones
            </div>
            <div class="card-body">
                @foreach($persona->direccion as $direccion)
                    <div class="card mb-4">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th>Estado:</th>
                                        <td>{{ $direccion->estado ? $direccion->estado->nombre : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Municipio:</th>
                                        <td>{{ $direccion->municipio ? $direccion->municipio->nombre : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Parroquia:</th>
                                        <td>{{ $direccion->parroquia->nombre ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Urbanización:</th>
                                        <td>{{ $direccion->urbanizacion->nombre ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sector:</th>
                                        <td>{{ $direccion->sector->nombre ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Comunidad:</th>
                                        <td>{{ $direccion->comunidad->nombre ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Calle:</th>
                                        <td>{{ $direccion->calle ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Manzana:</th>
                                        <td>{{ $direccion->manzana ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Número de Vivienda:</th>
                                        <td>{{ $direccion->numero_de_vivienda ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>¿Es Principal?</th>
                                        <td>
                                            <span class="
                                            @if($direccion->es_principal)
                                                text-success  <!-- Clase para color verde -->
                                            @else
                                                text-danger  <!-- Clase para color rojo -->
                                            @endif
                                            ">
                                            {{ $direccion->es_principal ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Registro:</th>
                                        <td>{{ $direccion->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <hr>

        <h3>Reportes de Incidencias</h3>
        @if($persona->incidencias->isEmpty())
            <p>No hay incidencias registradas para esta persona.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo de Incidencia</th>
                        <th>Descripción</th>
                        <th>Nivel de Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($persona->incidencias as $incidencia)
                        <tr>
                            <td>{{ $incidencia->tipo_incidencia }}</td>
                            <td>{{ $incidencia->descripcion }}</td>
                            <td>{{ $incidencia->nivel_prioridad }}</td>
                            <td>{{ $incidencia->estado }}</td>
                            <td>{{ $incidencia->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $persona->slug]) }}">Modificar incidencia</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        setTimeout(function() {
            document.getElementById('error-alert')?.style.display = 'none';
            document.getElementById('validation-errors')?.style.display = 'none';
        }, 2000);
    </script>
@endsection
