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

    @if(!empty($tablaDatos))
    <h4 style="margin-top: 30px;">Resumen de Incidencias</h4>
    <table width="100%" style="border-collapse: collapse; margin-top: 10px; font-size: 12px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #ccc; padding: 6px;">Mes</th>
                <th style="border: 1px solid #ccc; padding: 6px;">Tipo</th>
                <th style="border: 1px solid #ccc; padding: 6px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tablaDatos as $fila)
                <tr>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $fila['mes'] }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $fila['tipo'] }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $fila['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif


    <div class="footer" style="margin-top: 30px;">
        {!! $pie_html !!}
    </div>
</body>
</html>
