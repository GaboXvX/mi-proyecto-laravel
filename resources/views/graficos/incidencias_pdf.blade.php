<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Incidencias</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
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
        .content {
            margin-top: 30px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .filters span {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary-card {
            display: inline-block;
            width: 18%;
            margin-right: 1%;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            color: white;
            text-align: center;
            vertical-align: top;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .chart-container {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .chart-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            background-color: #f5f5f5;
            padding: 5px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }
        .report-subtitle {
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<header>
    <div style="text-align: center;">
        @if(isset($membrete))
            {!! $membrete !!}
        @else
            <div class="report-title">Reporte Estadístico de Incidencias</div>
            <div class="report-subtitle">Sistema de Gestión de Incidencias</div>
        @endif
    </div>
</header>

<footer>
    Generado el {{ now()->format('d/m/Y H:i:s') }} | Sistema de Gestión de Incidencias
</footer>

<div class="content">
    <!-- Filtros aplicados -->
    <div class="filters">
        <div><span>Período:</span> {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} al {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
        @if($filters['tipo_incidencia_id'])
            <div><span>Tipo:</span> {{ App\Models\tipoIncidencia::find($filters['tipo_incidencia_id'])->nombre ?? 'Todos' }}</div>
        @endif
        @if($filters['nivel_incidencia_id'])
            <div><span>Nivel:</span> {{ App\Models\nivelIncidencia::find($filters['nivel_incidencia_id'])->nombre ?? 'Todos' }}</div>
        @endif
        @if($filters['institucion_id'])
            <div><span>Institución:</span> {{ App\Models\Institucion::find($filters['institucion_id'])->nombre ?? 'Todas' }}</div>
        @endif
        @if($filters['estacion_id'])
            <div><span>Estación:</span> {{ App\Models\InstitucionEstacion::find($filters['estacion_id'])->nombre ?? 'Todas' }}</div>
        @endif
    </div>

    <!-- Resumen estadístico -->
    <div style="text-align: center; margin-bottom: 30px;">
        <div class="summary-card" style="background-color: #007bff;">
            <div>Total Incidencias</div>
            <div class="value">{{ $totalIncidencias }}</div>
        </div>
        
        <div class="summary-card" style="background-color: #28a745;">
            <div>Atendidas</div>
            <div class="value">{{ $incidenciasAtendidas }}</div>
            <div>({{ $totalIncidencias > 0 ? round(($incidenciasAtendidas/$totalIncidencias)*100, 1) : 0 }}%)</div>
        </div>
        
        <div class="summary-card" style="background-color: #ffc107;">
            <div>Pendientes</div>
            <div class="value">{{ $incidenciasPendientes }}</div>
            <div>({{ $totalIncidencias > 0 ? round(($incidenciasPendientes/$totalIncidencias)*100, 1) : 0 }}%)</div>
        </div>
        
        <div class="summary-card" style="background-color: #dc3545;">
            <div>Por Vencer</div>
            <div class="value">{{ $incidenciasPorVencer }}</div>
        </div>
    </div>

   <!-- Gráficos -->
   <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Incidencias por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="estadoChart" height="250" width="100%"></canvas>
                    <!-- Leyenda nativa de Chart.js, no personalizada -->
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Incidencias por Nivel</h5>
                </div>
                <div class="card-body">
                    <canvas id="nivelChart" height="250" width="100%"></canvas>
                    <!-- Leyenda nativa de Chart.js, no personalizada -->
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>