<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Incidencia {{ $incidencia->cod_incidencia }}</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
        }
        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f0f0f0;
            border-left: 5px solid #007bff;
            padding: 5px 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .col-6 {
            width: 50%;
        }
        .label-bold {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .box {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
        }
        .timeline {
            border-left: 2px solid #ccc;
            padding-left: 15px;
        }
        .timeline-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <div style="text-align: center;">
        @if(isset($logoBase64))
            <img src="{{ $logoBase64 }}" style="height: 60px; margin-bottom: 10px;"><br>
        @endif
        {!! $membrete !!}
    </div>
</header>

<footer>
    Generado el {{ now()->format('d/m/Y H:i:s') }}
</footer>

<main>
    <!-- Título -->
    <br>
    <div class="section">
        <h2 style="text-align: center;">Detalles de la Incidencia</h2>
        <p style="text-align: center;">Código: <strong>{{ $incidencia->cod_incidencia }}</strong></p>
    </div>

    <!-- Información Básica -->
    <div class="section">
        <div class="section-title">Información Básica</div>
        <div class="row">
            <div class="col-6">
                <p><span class="label-bold">Tipo:</span> {{ $incidencia->tipoIncidencia->nombre }}</p>
                <p><span class="label-bold">Fecha creación:</span> {{ $incidencia->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <div class="col-6">
                <p><span class="label-bold">Estado:</span>
                    <span class="badge" style="background-color: {{ $incidencia->estadoIncidencia->color ?? '#6c757d' }}">
                        {{ $incidencia->estadoIncidencia->nombre ?? 'N/A' }}
                    </span>
                </p>
                <p><span class="label-bold">Prioridad:</span>
                    <span class="badge" style="background-color: {{ $incidencia->nivelIncidencia->color ?? '#6c757d' }}">
                        {{ $incidencia->nivelIncidencia->nombre ?? 'N/A' }}
                    </span>
                </p>
                <p><span class="label-bold">Vencimiento:</span>
                    {{ $incidencia->fecha_vencimiento ? $incidencia->fecha_vencimiento->format('d/m/Y H:i:s') : 'Sin fecha' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Ubicación -->
    <div class="section">
        <div class="section-title">Ubicación</div>
        <p><span class="label-bold">Institución:</span> {{ $incidencia->institucion->nombre ?? 'N/A' }}</p>
        <p><span class="label-bold">Estación:</span> {{ $incidencia->estacion->nombre ?? 'N/A' }}</p>
        <p><span class="label-bold">Municipio:</span> {{ $incidencia->estacion->municipio->nombre ?? 'N/A' }}</p>

        <div class="box">
            <div class="row">
                <div class="col-6">
                    <p><span class="label-bold">Estado:</span> {{ $incidencia->direccion->estado->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Municipio:</span> {{ $incidencia->direccion->municipio->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Parroquia:</span> {{ $incidencia->direccion->parroquia->nombre ?? 'N/A' }}</p>
                </div>
                <div class="col-6">
                    <p><span class="label-bold">Urbanización:</span> {{ $incidencia->direccion->urbanizacion->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Sector:</span> {{ $incidencia->direccion->sector->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Comunidad:</span> {{ $incidencia->direccion->comunidad->nombre ?? 'N/A' }}</p>
                </div>
            </div>
            <p><span class="label-bold">Punto de Referencia:</span> {{ $incidencia->direccion->punto_de_referencia ?? 'No especificado' }}</p>
        </div>
    </div>

    <!-- Descripción -->
    <div class="section">
        <div class="section-title">Descripción</div>
        <div class="box">{{ $incidencia->descripcion }}</div>
    </div>

    <!-- Reportado por -->
    <div class="section">
        <div class="section-title">Reportado por</div>
        @if ($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
            <p><span class="label-bold">Nombre:</span> {{ $incidencia->usuario->empleadoAutorizado->nombre }} {{ $incidencia->usuario->empleadoAutorizado->apellido }}</p>
            <p><span class="label-bold">Cédula:</span> V-{{ $incidencia->usuario->empleadoAutorizado->cedula }}</p>
            <p><span class="label-bold">Teléfono:</span> {{ $incidencia->usuario->empleadoAutorizado->telefono ?? 'N/A' }}</p>
        @else
            <p class="text-muted"><em>Información no disponible</em></p>
        @endif
    </div>

    <!-- Reparación -->
    @if ($incidencia->estadoIncidencia && strtolower($incidencia->estadoIncidencia->nombre) == 'atendido' && $reparacion)
        <div class="section">
            <div class="section-title">Detalles de la Reparación</div>
            <p><span class="label-bold">Fecha de atención:</span> {{ $reparacion->created_at->format('d/m/Y H:i:s') }}</p>
            <div class="box">{{ $reparacion->descripcion }}</div>

            <p><span class="label-bold">Técnico:</span> {{ $reparacion->personalReparacion->nombre ?? '' }} {{ $reparacion->personalReparacion->apellido ?? '' }}</p>
            <p><span class="label-bold">Institución:</span> {{ $reparacion->personalReparacion->institucion->nombre ?? 'N/A' }}</p>
            <p><span class="label-bold">Estación:</span> {{ $reparacion->personalReparacion->InstitucionEstacion->nombre ?? 'N/A' }}</p>

            @if ($reparacion->prueba_fotografica)
                <div class="mt-3">
                    <p><strong>Prueba Fotográfica:</strong></p>
                    @if(isset($reparacion->imageSrc))
                        <img src="{{ $reparacion->imageSrc }}" style="max-width: 100%; max-height: 300px;">
                    @else
                        <p>Ruta: {{ $reparacion->prueba_fotografica }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Historial -->
    @if ($incidencia->movimiento && $incidencia->movimiento->count() > 0)
        <div class="section">
            <div class="section-title">Historial de Movimientos</div>
            <div class="timeline">
                @foreach ($incidencia->movimiento as $mov)
                    <div class="timeline-item">
                        <p><strong>{{ $mov->accion }}</strong> - {{ $mov->created_at->format('d/m/Y H:i:s') }}</p>
                        <p>{{ $mov->descripcion }}</p>
                        @if ($mov->usuario)
                            <p><small>Por: {{ $mov->usuario->empleadoAutorizado->nombre }} {{ $mov->usuario->empleadoAutorizado->apellido }} (C.I: {{ $mov->usuario->empleadoAutorizado->cedula }})</small></p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</main>

</body>
</html>
