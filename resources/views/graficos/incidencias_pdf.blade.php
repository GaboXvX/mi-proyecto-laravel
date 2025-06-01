<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        .header, .footer { text-align: center; }
        .stat-boxes { display: flex; gap: 10px; margin: 20px 0; }
        .box { flex: 1; padding: 15px; border-radius: 10px; color: white; text-align: center; }
        .bg-primary { background-color: #0d6efd; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; }
        .grafico-container { display: flex; gap: 20px; margin-top: 30px; }
        .grafico-container img { max-width: 100%; height: auto; flex: 1; }
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

    <div class="stat-boxes">
        <div class="box bg-primary">
            <h4>Total Incidencias</h4>
            <p style="font-size: 24px;">{{ $totalIncidencias }}</p>
        </div>
        <div class="box bg-success">
            <h4>Atendidas</h4>
            <p style="font-size: 24px;">{{ $incidenciasAtendidas }}</p>
            <p>{{ $totalIncidencias > 0 ? round(($incidenciasAtendidas/$totalIncidencias)*100, 1) : 0 }}%</p>
        </div>
        <div class="box bg-warning">
            <h4>Pendientes</h4>
            <p style="font-size: 24px;">{{ $incidenciasPendientes }}</p>
            <p>{{ $totalIncidencias > 0 ? round(($incidenciasPendientes/$totalIncidencias)*100, 1) : 0 }}%</p>
        </div>
        <div class="box bg-danger">
            <h4>Por Vencer</h4>
            <p style="font-size: 24px;">{{ $incidenciasPorVencer }}</p>
        </div>
    </div>

    <div class="grafico-container">
        <div><img src="{{ $imagenEstado }}" alt="Gráfico por Estado"></div>
        <div><img src="{{ $imagenNivel }}" alt="Gráfico por Nivel"></div>
    </div>

    <div class="footer" style="margin-top: 30px;">
        {!! $pie_html !!}
    </div>
</body>
</html>
