<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
    body {
        font-family: sans-serif;
        font-size: 11px;
        margin: 30px 40px;
    }

    .header, .footer {
        text-align: center;
        margin-bottom: 10px;
    }

    .stat-boxes {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin: 15px 0;
    }

    .box {
        flex: 1;
        padding: 10px;
        border-radius: 8px;
        color: white;
        text-align: center;
        font-size: 11px;
    }

    .box h4 {
        font-size: 13px;
        margin-bottom: 5px;
    }

    .box p {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }
    
    .grafico-container {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 20px;
    }

    .grafico-container > div {
        flex: 1;
        text-align: center;
    }

    .grafico-container img {
        max-width: 100%;
        height: auto;
    }
</style>

</head>
<body>
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="height: 60px;"><br>
        @endif
        {!! $membrete !!}
    </div>

    <h3 style="text-align: center; margin-top: 10px;">Estadísticas Generales de Incidencias</h3>

    <table width="100%" cellpadding="10" cellspacing="10" style="margin: 20px 0;">
        <tr>
            <td align="center" bgcolor="#0d6efd" style="color: white; border-radius: 8px; padding: 10px;">
                <strong>Total Incidencias</strong><br>
                <span style="font-size: 20px;">{{ $totalIncidencias }}</span>
            </td>
            <td align="center" bgcolor="#198754" style="color: white; border-radius: 8px; padding: 10px;">
                <strong>Atendidas</strong><br>
                <span style="font-size: 20px;">{{ $incidenciasAtendidas }}</span><br>
                {{ $totalIncidencias > 0 ? round(($incidenciasAtendidas/$totalIncidencias)*100, 1) : 0 }}%
            </td>
            <td align="center" bgcolor="#ffc107" style="color: black; border-radius: 8px; padding: 10px;">
                <strong>Pendientes</strong><br>
                <span style="font-size: 20px;">{{ $incidenciasPendientes }}</span><br>
                {{ $totalIncidencias > 0 ? round(($incidenciasPendientes/$totalIncidencias)*100, 1) : 0 }}%
            </td>
            <td align="center" bgcolor="#dc3545" style="color: white; border-radius: 8px; padding: 10px;">
                <strong>Por Vencer</strong><br>
                <span style="font-size: 20px;">{{ $incidenciasPorVencer }}</span>
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="10" cellspacing="0" style="margin-top: 20px;">
        <tr>
            <td align="center" width="50%">
                <strong>Incidencias por Estado</strong><br>
                <img src="{{ $imagenEstado }}" style="width: 100%; max-width: 350px;">
            </td>
            <td align="center" width="50%">
                <strong>Incidencias por Nivel</strong><br>
                <img src="{{ $imagenNivel }}" style="width: 100%; max-width: 350px;">
            </td>
        </tr>
    </table>

    @if(!empty($tablaEstado))
    <h4 style="margin-top: 20px;">Incidencias por Estado</h4>
        <table width="100%" style="border-collapse: collapse; margin-top: 10px; font-size: 12px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ccc; padding: 6px;">Estado</th>
                    <th style="border: 1px solid #ccc; padding: 6px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tablaEstado as $fila)
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 6px;">{{ $fila['categoria'] }}</td>
                        <td style="border: 1px solid #ccc; padding: 6px;">{{ $fila['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($tablaNivel))
        <h4 style="margin-top: 30px;">Incidencias por Nivel</h4>
        <table width="100%" style="border-collapse: collapse; margin-top: 10px; font-size: 12px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ccc; padding: 6px;">Nivel</th>
                    <th style="border: 1px solid #ccc; padding: 6px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tablaNivel as $fila)
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 6px;">{{ $fila['categoria'] }}</td>
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
