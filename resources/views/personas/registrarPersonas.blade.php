<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Captura de Datos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        .form-control, .form-select {
            font-size: 0.9rem;
            padding: 0.5rem;
        }

        .btn {
            font-size: 0.9rem;
            padding: 0.6rem;
        }

        .alert {
            font-size: 0.9rem;
        }

        .container {
            max-width: 600px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5 p-4 bg-white rounded shadow-sm">
        <h1 class="mb-4 text-center">Formulario de Captura de Datos</h1>

        @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('personas.index') }}" class="btn btn-secondary btn-sm">Ir a la lista</a>
            <a href="{{ route('home') }}" class="btn btn-primary">Volver</a>
        </div>

        <form action="{{ route('personas.store') }}" method="POST" id="form">
            @csrf

            <!-- Campos básicos -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" id="apellido" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
            </div>

            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula:</label>
                <input type="text" id="cedula" name="cedula" class="form-control" value="{{ old('cedula') }}" required>
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" class="form-control" value="{{ old('correo') }}" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" class="form-control" pattern="[0-9]{10>=11}" placeholder="Ej: 1234567890" value="{{ old('telefono') }}" required>
            </div>

            <!-- Estado y Municipio -->
            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select name="estado" id="estado" class="form-select" required disabled>
                    <option value="Sucre" {{ old('estado') == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="municipio" class="form-label">Municipio:</label>
                <select name="municipio" id="municipio" class="form-select" required disabled>
                    <option value="Sucre" {{ old('municipio') == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                </select>
            </div>

            <!-- Parroquia -->
            <div class="mb-3">
                <label for="parroquia" class="form-label">Parroquia:</label>
                <select name="parroquia" id="parroquia" class="form-select" required>
                    <option value="">Seleccione una parroquia</option>
                    <option value="Valentín Valiente">Valentín Valiente</option>
                    <option value="Altagracia">Altagracia</option>
                    <option value="Santa Inés">Santa Inés</option>
                    <option value="San Juan">San Juan</option>
                    <option value="Ayacucho">Ayacucho</option>
                    <option value="Gran Mariscal">Gran Mariscal</option>
                    <option value="Raúl Leoni">Raúl Leoni</option>
                </select>
            </div>

            <!-- Urbanización -->
            <div class="mb-3">
                <label for="urbanizacion" class="form-label">Urbanización:</label>
                <select name="urbanizacion" id="urbanizacion" class="form-select" required>
                    <option value="">Seleccione una urbanización</option>
                    <!-- Las opciones de urbanización se agregarán dinámicamente -->
                </select>
            </div>

            <!-- Sector -->
            <div class="mb-3">
                <label for="sector" class="form-label">Sector:</label>
                <select name="sector" id="sector" class="form-select" required>
                    <option value="">Seleccione un sector</option>
                    <!-- Los sectores serán actualizados dinámicamente según la urbanización seleccionada -->
                </select>
            </div>

            <!-- Comunidad -->
            <div class="mb-3">
                <label for="comunidad" class="form-label">Comunidad:</label>
                <select name="comunidad" id="comunidad" class="form-select" required>
                    <option value="">Seleccione una comunidad</option>
                    <!-- Las comunidades serán actualizadas dinámicamente según el sector seleccionado -->
                </select>
            </div>

            <!-- Líder Comunitario -->
            <div class="mb-3">
                <label for="lider_comunitario" class="form-label">Líder Comunitario:</label>
                <select name="lider_comunitario" id="lider_comunitario" class="form-select" required>
                    <option value="">Seleccione un líder comunitario</option>
                    <!-- Los líderes serán actualizados dinámicamente según la comunidad seleccionada -->
                </select>
            </div>

            <!-- Calle, Manzana y Número de Casa -->
            <div class="mb-3">
                <label for="calle" class="form-label">Calle:</label>
                <input type="text" id="calle" name="calle" class="form-control" value="{{ old('calle') }}" required>
            </div>

            <div class="mb-3">
                <label for="manzana" class="form-label">Manzana:</label>
                <input type="text" id="manzana" name="manzana" class="form-control" value="{{ old('manzana') }}" required>
            </div>

            <div class="mb-3">
                <label for="num_casa" class="form-label">Número de Casa:</label>
                <input type="number" id="num_casa" name="num_casa" class="form-control" value="{{ old('num_casa') }}" required min="1" step="1">
            </div>

            <button type="submit" class="btn btn-primary w-100">Enviar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const parroquiaSelect = document.getElementById('parroquia');
            const urbanizacionSelect = document.getElementById('urbanizacion');
            const sectorSelect = document.getElementById('sector');
            const comunidadSelect = document.getElementById('comunidad');
            const liderSelect = document.getElementById('lider_comunitario');
    
            // Datos de urbanizaciones, sectores, comunidades y líderes
            const parroquiasUrbanizaciones = {
                'Altagracia': {
                    'La Llanada': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'Cambio de Rumbo', lider: 'Juan Pérez' },
                                    { nombre: 'Cuatro de Marzo', lider: 'Ana González' }
                                ]
                            },
                            '2': {
                                comunidades: [
                                    { nombre: 'La Esperanza', lider: 'Carlos Rodríguez' },
                                    { nombre: 'Sol Naciente', lider: 'María Sánchez' },
                                    { nombre: 'Pueblo Nuevo', lider: 'Luis Díaz' }
                                ]
                            }
                        }
                    },
                    'Brasil': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'Los Amigos', lider: 'Pedro Martínez' },
                                    { nombre: 'Los Vencedores', lider: 'Isabel López' }
                                ]
                            },
                            '2': {
                                comunidades: [
                                    { nombre: 'Las Palmas', lider: 'Roberto Pérez' },
                                    { nombre: 'Los Robles', lider: 'Laura Fernández' },
                                    { nombre: 'Jardines del Sol', lider: 'Felipe Martínez' }
                                ]
                            }
                        }
                    },
                    'San Juan': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'Mirador del Sol', lider: 'Adriana Ramírez' },
                                    { nombre: 'La Esperanza 2', lider: 'José Morales' }
                                ]
                            }
                        }
                    }
                },
                'Valentín Valiente': {
                    'El Rosal': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'El Encanto', lider: 'Pedro Gómez' },
                                    { nombre: 'La Muralla', lider: 'Elena Ruiz' }
                                ]
                            }
                        }
                    },
                    'La Colina': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'Campo Alegre', lider: 'Juan Martínez' },
                                    { nombre: 'Los Naranjos', lider: 'Sandra López' }
                                ]
                            },
                            '2': {
                                comunidades: [
                                    { nombre: 'Altos de la Colina', lider: 'Carlos Jiménez' },
                                    { nombre: 'Río Claro', lider: 'Ricardo Pérez' }
                                ]
                            }
                        }
                    }
                },
                'Santa Inés': {
                    'La Candelaria': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'Nuevo Horizonte', lider: 'Lina González' },
                                    { nombre: 'La Sombra', lider: 'Antonio Ramírez' }
                                ]
                            }
                        }
                    }
                },
                'Gran Mariscal': {
                    'Los Molinos': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'Valles del Sol', lider: 'Julio Rodríguez' },
                                    { nombre: 'Sol y Luna', lider: 'Ana María Suárez' }
                                ]
                            }
                        }
                    }
                },
                'Raúl Leoni': {
                    'La Estrella': {
                        sectores: {
                            '1': {
                                comunidades: [
                                    { nombre: 'La Cruz', lider: 'Carlos Hernández' },
                                    { nombre: 'El Ávila', lider: 'Luis Fernández' }
                                ]
                            }
                        }
                    }
                }
            };
    
            // Rellenar urbanizaciones según la parroquia seleccionada
            parroquiaSelect.addEventListener('change', function () {
                urbanizacionSelect.innerHTML = '<option value="">Seleccione una urbanización</option>';
                sectorSelect.innerHTML = '<option value="">Seleccione un sector</option>';
                comunidadSelect.innerHTML = '<option value="">Seleccione una comunidad</option>';
                liderSelect.innerHTML = '<option value="">Seleccione un líder comunitario</option>';
    
                const parroquiaSeleccionada = parroquiaSelect.value;
    
                if (parroquiaSeleccionada && parroquiasUrbanizaciones[parroquiaSeleccionada]) {
                    const urbanizaciones = Object.keys(parroquiasUrbanizaciones[parroquiaSeleccionada]);
    
                    urbanizaciones.forEach(function (urbanizacion) {
                        const option = document.createElement('option');
                        option.value = urbanizacion;
                        option.textContent = urbanizacion;
                        urbanizacionSelect.appendChild(option);
                    });
                }
            });
    
            // Rellenar sectores según la urbanización seleccionada
            urbanizacionSelect.addEventListener('change', function () {
                const parroquiaSeleccionada = parroquiaSelect.value;
                const urbanizacionSeleccionada = urbanizacionSelect.value;
    
                sectorSelect.innerHTML = '<option value="">Seleccione un sector</option>';
                comunidadSelect.innerHTML = '<option value="">Seleccione una comunidad</option>';
                liderSelect.innerHTML = '<option value="">Seleccione un líder comunitario</option>';
    
                if (parroquiaSeleccionada && urbanizacionSeleccionada) {
                    const sectores = parroquiasUrbanizaciones[parroquiaSeleccionada][urbanizacionSeleccionada].sectores;
    
                    Object.keys(sectores).forEach(function (sector) {
                        const option = document.createElement('option');
                        option.value = sector;
                        option.textContent = sector;
                        sectorSelect.appendChild(option);
                    });
                }
            });
    
            // Rellenar comunidades según el sector seleccionado
            sectorSelect.addEventListener('change', function () {
                const parroquiaSeleccionada = parroquiaSelect.value;
                const urbanizacionSeleccionada = urbanizacionSelect.value;
                const sectorSeleccionado = sectorSelect.value;
    
                comunidadSelect.innerHTML = '<option value="">Seleccione una comunidad</option>';
                liderSelect.innerHTML = '<option value="">Seleccione un líder comunitario</option>';
    
                if (parroquiaSeleccionada && urbanizacionSeleccionada && sectorSeleccionado) {
                    const comunidades = parroquiasUrbanizaciones[parroquiaSeleccionada][urbanizacionSeleccionada].sectores[sectorSeleccionado].comunidades;
    
                    comunidades.forEach(function (comunidad) {
                        const option = document.createElement('option');
                        option.value = comunidad.nombre;
                        option.textContent = comunidad.nombre;
                        comunidadSelect.appendChild(option);
                    });
                }
            });
    
            // Rellenar líderes según la comunidad seleccionada
            comunidadSelect.addEventListener('change', function () {
                const comunidadSeleccionada = comunidadSelect.value;
    
                liderSelect.innerHTML = '<option value="">Seleccione un líder comunitario</option>';
    
                if (comunidadSeleccionada) {
                    // Buscar líder para la comunidad seleccionada
                    Object.keys(parroquiasUrbanizaciones).forEach(parroquia => {
                        Object.keys(parroquiasUrbanizaciones[parroquia]).forEach(urbanizacion => {
                            Object.keys(parroquiasUrbanizaciones[parroquia][urbanizacion].sectores).forEach(sector => {
                                const sectorData = parroquiasUrbanizaciones[parroquia][urbanizacion].sectores[sector];
                                const comunidadData = sectorData.comunidades.find(c => c.nombre === comunidadSeleccionada);
    
                                if (comunidadData) {
                                    const option = document.createElement('option');
                                    option.value = comunidadData.lider;
                                    option.textContent = comunidadData.lider;
                                    liderSelect.appendChild(option);
                                }
                            });
                        });
                    });
                }
            });
        });
    </script>
    
    
    
</body>

</html>
