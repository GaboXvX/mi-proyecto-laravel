<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial - {{ $empleado->nombre }} {{ $empleado->apellido }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #999;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            height: 60px;
            margin-bottom: 10px;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
        }

        .title-box {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px 15px;
            background-color: #f9f9f9;
        }

        .title-box h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .badge {
            font-size: 13px;
            padding: 3px 7px;
            background-color: #eee;
            border-radius: 4px;
        }

        .timeline-item {
            border-left: 3px solid #007bff;
            margin-bottom: 15px;
            padding-left: 10px;
        }

        .timeline-item h6 {
            font-size: 14px;
            margin: 0;
            color: #007bff;
        }

        .timeline-date {
            font-size: 12px;
            color: #555;
            margin-bottom: 3px;
        }

        .timeline-content {
            font-size: 13px;
            color: #333;
        }

        .timeline-item.success {
            border-left-color: #28a745;
        }

        .timeline-item.danger {
            border-left-color: #dc3545;
        }

        .timeline-item.info {
            border-left-color: #17a2b8;
        }

        .timeline-item.creacion_empleado {
            border-left-color: #6f42c1;
        }

        footer {
            position: fixed;
            bottom: 30px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <!-- Encabezado / Logo -->
    <div class="header">
        @if(isset($logoBase64))
            <img src="{{ $logoBase64 }}" style="height: 60px; margin-bottom: 10px;"><br>
        @endif
        {!! $membrete !!}
    </div>

    <!-- Título del historial -->
    <div class="title-box">
        <h2>Historial de {{ $empleado->nombre }} {{ $empleado->apellido }}</h2>
        <span class="badge">{{ $empleado->nacionalidad }}-{{ $empleado->cedula }}</span>
    </div>

    <!-- Línea de tiempo -->
    @foreach($historial as $evento)
        <div class="timeline-item {{ $evento['color'] }}">
            <h6>{{ $evento['titulo'] }}</h6>
            <div class="timeline-date">{{ $evento['fecha']->format('d/m/Y') }}</div>
            @isset($evento['usuario'])
                <div class="timeline-date">Por: {{ $evento['usuario'] }}</div>
            @endisset
            <div class="timeline-content">{{ $evento['descripcion'] }}</div>
        </div>
    @endforeach

    <!-- Pie de página -->
    <footer>
        {!! $pie_html ?? '' !!}<br>
        Generado el {{ now()->format('d/m/Y H:i:s') }}
    </footer>

</body>
</html>