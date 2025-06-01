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
    @isset($pie_html)
        {!! $pie_html !!}<br>
    @endisset
    <span style="color: #6c757d; font-size: 0.9em;">
        Generado el {{ now()->format('d/m/Y H:i:s') }}
    </span>
</footer>

<main>
    <!-- Título -->
    <br><br>
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
                    <p><span class="label-bold">Estado:</span> {{ $incidencia->direccionIncidencia->estado->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Municipio:</span> {{ $incidencia->direccionIncidencia->municipio->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Parroquia:</span> {{ $incidencia->direccionIncidencia->parroquia->nombre ?? 'N/A' }}</p>
                </div>
                <div class="col-6">
                    <p><span class="label-bold">Urbanización:</span> {{ $incidencia->direccionIncidencia->urbanizacion->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Sector:</span> {{ $incidencia->direccionIncidencia->sector->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Comunidad:</span> {{ $incidencia->direccionIncidencia->comunidad->nombre ?? 'N/A' }}</p>
                </div>
            </div>
            <p><span class="label-bold">Punto de Referencia:</span> {{ $incidencia->direccionIncidencia->punto_de_referencia ?? 'No especificado' }}</p>
        </div>
    </div>
<!-- Instituciones de Apoyo -->
@if($incidencia->institucionesApoyo && $incidencia->institucionesApoyo->count() > 0)
<div class="bg-section">
    <h4 class="section-title"><i class="fas fa-hands-helping me-2"></i> Instituciones de Apoyo</h4>
    <div class="row">
        @foreach($incidencia->institucionesApoyo as $apoyo)
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $apoyo->institucion->nombre ?? 'Institución no especificada' }}</h5>
                    @if($apoyo->Estacion)
                    <p class="card-text">
                        <span class="label-bold">Estación:</span> 
                        {{ $apoyo->Estacion->nombre }}
                    </p>
                    @endif
                    @if($apoyo->institucion && $apoyo->institucion->municipio)
                    <p class="card-text">
                        <span class="label-bold">Ubicación:</span> 
                        {{ $apoyo->institucion->municipio->nombre }}, 
                        {{ $apoyo->institucion->estado->nombre }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
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
            <p><span class="label-bold">Cédula:</span>{{ $incidencia->usuario->empleadoAutorizado->cedula }}</p>
            <p><span class="label-bold">Teléfono:</span> {{ $incidencia->usuario->empleadoAutorizado->telefono ?? 'N/A' }}</p>
            <p><span class="label-bold">Género:</span> {{ $incidencia->usuario->empleadoAutorizado->genero ?? 'N/A' }}</p>
        @else
            <p class="text-muted"><em>Información no disponible</em></p>
        @endif
    </div>
    <!-- Persona relacionada -->
    <div class="section">
        <div class="section-title">Persona Relacionada</div>
        @if ($incidencia->persona)
            <p><span class="label-bold">Nombre:</span> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
            <p><span class="label-bold">Cédula:</span> {{ $incidencia->persona->cedula }}</p>
            <p><span class="label-bold">Teléfono:</span> {{ $incidencia->persona->telefono ?? 'N/A' }}</p>
            <p><span class="label-bold">Género:</span> {{ $incidencia->persona->genero ?? 'N/A' }}</p>
        @else
            <p class="text-muted"><em>No hay persona relacionada</em></p>
        @endif
    </div>

    <div class="mt-3">
    <h5><i class="fas fa-camera"></i> Pruebas Fotográficas</h5>
    <div class="row">
        @if(isset($reparacion) && $reparacion && $reparacion->pruebasFotograficas && $reparacion->pruebasFotograficas->isNotEmpty())
            @foreach($reparacion->pruebasFotograficas as $foto)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        @php
                            // Si estamos generando PDF (logoBase64 está presente), usar base64 para la imagen
                            $isPdf = isset($logoBase64);
                            $imgSrc = '';
                            if ($isPdf && $foto->ruta && file_exists(public_path('storage/'.$foto->ruta))) {
                                $imgData = base64_encode(file_get_contents(public_path('storage/'.$foto->ruta)));
                                $imgSrc = 'data:image/jpeg;base64,' . $imgData;
                            } else {
                                $imgSrc = asset('storage/'.$foto->ruta);
                            }
                        @endphp
                        <img src="{{ $imgSrc }}"
                             class="card-img-top img-fluid"
                             alt="Prueba fotográfica"
                             style="max-height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ $foto->observacion ?? 'Sin descripción' }}
                                </small>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ $foto->created_at->format('d/m/Y H:i') }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No se encontraron pruebas fotográficas asociadas a esta reparación.
                </div>
            </div>
        @endif
    </div>
</div>

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

    <!-- Imágenes de la incidencia (Antes) -->
    <div class="section">
        <div class="section-title">Imágenes de la Incidencia (Antes)</div>
        <div class="row">
            @php
                $fotosAntes = isset($imagenesAntes)
                    ? $imagenesAntes
                    : ($incidencia->pruebasFotograficas ? $incidencia->pruebasFotograficas->where('etapa_foto', 'Antes') : collect());
            @endphp
            @forelse($fotosAntes as $foto)
                @if($foto->ruta && file_exists(public_path('storage/' . $foto->ruta)))
                    <div class="col-6" style="margin-bottom: 10px; text-align: center;">
                        <img src="{{ public_path('storage/' . $foto->ruta) }}" style="max-width: 100%; max-height: 180px; border: 1px solid #ccc; border-radius: 4px;">
                        @if($foto->observacion)
                            <div class="mt-1" style="font-size: 11px; color: #555;">{{ $foto->observacion }}</div>
                        @endif
                    </div>
                @endif
            @empty
                <div class="col-12 text-muted"><em>No hay imágenes adjuntas</em></div>
            @endforelse
        </div>
    </div>

</main>

</body>
</html>
