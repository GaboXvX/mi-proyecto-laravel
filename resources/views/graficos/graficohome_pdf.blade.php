<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        .header, .footer { text-align: center; }
        .filtros { margin: 20px 0; text-align: center; }
        .grafico-container { text-align: center; margin-top: 30px; }
        .grafico-container img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="height: 60px;"><br>
        @endif
        {!! $membrete !!}
    </div>

    <h3 style="text-align: center; margin-top: 10px;">Crecimiento de Incidencias</h3>

    <div class="filtros">
        <strong>Tipo:</strong> {{ $tipo }} &nbsp;&nbsp;
        <strong>Nivel:</strong> {{ $nivel }} &nbsp;&nbsp;
        <strong>Mes:</strong> {{ $mes }} &nbsp;&nbsp;
        <strong>Año:</strong> {{ $anio }}
    </div>

    <div class="grafico-container">
        <img src="{{ $imagenGrafico }}" alt="Gráfico de Incidencias">
    </div>

    <div class="footer" style="margin-top: 30px;">
        {!! $pie_html !!}
    </div>
</body>
</html>
