@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2 class="mb-4">Estadísticas de Incidencias</h2>

    <!-- Botón de descarga -->
    <div class="text-end mb-3">
        <button id="descargarPDF" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Descargar PDF
        </button>
        <a href="{{ route('graficos.incidencias.download') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success ms-2">
            <i class="fas fa-download"></i> Descargar Reporte
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('graficos.incidencias') }}" id="filtrosForm">
                <!-- Filtros de fecha, tipo y nivel -->
                <div class="row">
                    <div class="col-md-3">
                        <label for="start_date">Fecha Inicio</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">Fecha Fin</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <label for="tipo_incidencia_id">Tipo</label>
                        <select class="form-control" name="tipo_incidencia_id">
                            <option value="">Todos</option>
                            @foreach($tiposIncidencia as $tipo)
                                <option value="{{ $tipo->id_tipo_incidencia }}" {{ $filters['tipo_incidencia_id'] == $tipo->id_tipo_incidencia ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="nivel_incidencia_id">Nivel</label>
                        <select class="form-control" name="nivel_incidencia_id">
                            <option value="">Todos</option>
                            @foreach($nivelesIncidencia as $nivel)
                                <option value="{{ $nivel->id_nivel_incidencia }}" {{ $filters['nivel_incidencia_id'] == $nivel->id_nivel_incidencia ? 'selected' : '' }}>
                                    {{ $nivel->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-none"> <!-- Botón oculto, ya no se usa -->
                        <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
                    </div>
                </div>

                <!-- Filtros de institución y estación -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="institucion_id">Institución</label>
                        <select class="form-control" name="institucion_id" id="institucion_id">
                            <option value="">Todas</option>
                            @foreach($instituciones as $institucion)
                                <option value="{{ $institucion->id_institucion }}" {{ $filters['institucion_id'] == $institucion->id_institucion ? 'selected' : '' }}>
                                    {{ $institucion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="estacion_id">Estación</label>
                        <select class="form-control" name="estacion_id" id="estacion_id">
                            <option value="">Todas</option>
                            @foreach($estaciones as $estacion)
                                <option value="{{ $estacion->id_institucion_estacion }}" {{ $filters['estacion_id'] == $estacion->id_institucion_estacion ? 'selected' : '' }}>
                                    {{ $estacion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- detalles de las estadisticas -->
    <div class="row d-flex">
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-primary h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Total Incidencias</h5>
                    <p class="card-text text-center display-4">{{ $totalIncidencias }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-success h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Atendidas</h5>
                    <p class="card-text text-center display-4">{{ $incidenciasAtendidas }}</p>
                    <p class="card-text">{{ $totalIncidencias > 0 ? round(($incidenciasAtendidas/$totalIncidencias)*100, 1) : 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-warning h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Pendientes</h5>
                    <p class="card-text display-4 text-center">{{ $incidenciasPendientes }}</p>
                    <p class="card-text">{{ $totalIncidencias > 0 ? round(($incidenciasPendientes/$totalIncidencias)*100, 1) : 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-danger h-100 rounded-4">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-center">Por Vencer</h5>
                    <p class="card-text display-4 text-center">{{ $incidenciasPorVencer }}</p>
                </div>
            </div>
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
                    <canvas id="estadoChart" height="250"></canvas>
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
                    <canvas id="nivelChart" height="250"></canvas>
                    <!-- Leyenda nativa de Chart.js, no personalizada -->
                </div>
            </div>
        </div>
    </div>
</div>
<style>
body.loading {
    cursor: wait;
}
body.loading::after {
    content: "Cargando...";
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    z-index: 1000;
}
</style>
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script src="{{ asset('js/jspdf.umd.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let estadoChart, nivelChart;
    let debounceTimer;
    let initialChartsRendered = false;

    function debounce(fn, delay = 350) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fn, delay);
    }

    function formatPercentage(value, total) {
        if (!total || total === 0) return '0%';
        return `${Math.round((value / total) * 100)}%`;
    }

    // Dibuja los porcentajes solo en los segmentos visibles, pero SIEMPRE respecto al total global de datos
    function drawPercentages(chart) {
        const ctx = chart.ctx;
        ctx.save();
        ctx.font = 'bold 16px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        const meta = chart.getDatasetMeta(0);
        const data = chart.data.datasets[0].data;
        // El total global debe venir de chart.data._totalGlobal si existe, si no, usar suma de todos los datos
        const total = chart.data._totalGlobal || data.reduce((a, b) => a + b, 0) || 1;
        meta.data.forEach((arc, i) => {
            if (!arc.hidden) {
                let model = arc.getProps ? arc.getProps(['startAngle', 'endAngle', 'outerRadius', 'innerRadius', 'x', 'y'], true) : arc._model;
                const angle = (model.startAngle + model.endAngle) / 2;
                const radius = model.innerRadius + (model.outerRadius - model.innerRadius) * 0.6;
                const x = model.x + Math.cos(angle) * radius;
                const y = model.y + Math.sin(angle) * radius;
                ctx.fillStyle = '#fff';
                ctx.strokeStyle = 'rgba(0,0,0,0.25)';
                ctx.lineWidth = 3;
                ctx.shadowColor = 'rgba(0,0,0,0.4)';
                ctx.shadowBlur = 4;
                ctx.strokeText(formatPercentage(data[i], total), x, y);
                ctx.shadowBlur = 0;
                ctx.fillText(formatPercentage(data[i], total), x, y);
            }
        });
        ctx.restore();
    }

    // También corregimos el tooltip para que el total sea el global
    // --- LEYENDA NATIVA CON TACHADO Y OCULTAR PORCENTAJE EN KPI ---
    // Genera labels con tachado según visibilidad
    function generateLegendLabels(chart) {
        const meta = chart.getDatasetMeta(0);
        return chart.data.labels.map((label, i) => {
            const hidden = meta.data[i] && meta.data[i].hidden === true;
            return {
                text: label,
                fillStyle: chart.data.datasets[0].backgroundColor[i],
                strokeStyle: '#fff',
                lineWidth: 2,
                hidden: hidden,
                index: i,
                fontColor: '#333',
                fontStyle: hidden ? 'line-through' : 'normal',
                custom: { strike: hidden }
            };
        });
    }

    // Alterna visibilidad y fuerza redraw, pero los KPIs SIEMPRE muestran el porcentaje real del total
    function legendOnClick(e, legendItem, legend) {
        const chart = legend.chart;
        const index = legendItem.index;
        const meta = chart.getDatasetMeta(0);
        // Si ya está oculto, mostrarlo normalmente
        if (meta.data[index].hidden) {
            meta.data[index].hidden = false;
            chart.update();
            return;
        }
        // Contar cuántos segmentos quedarían visibles
        const visibles = meta.data.filter(arc => !arc.hidden).length;
        // Si es el último visible, animar retracción
        if (visibles === 1) {
            const arc = meta.data[index];
            let scale = 1;
            function retract() {
                scale -= 0.08;
                arc.options = arc.options || {};
                arc.options.outerRadius = arc.outerRadius * Math.max(scale, 0.1);
                chart.update('none');
                if (scale > 0.1) {
                    requestAnimationFrame(retract);
                } else {
                    meta.data[index].hidden = true;
                    // Restaurar radio original
                    delete arc.options.outerRadius;
                    chart.update();
                }
            }
            retract();
            return;
        }
        // Ocultar normalmente
        meta.data[index].hidden = true;
        chart.update();
    }

    // Los KPIs SIEMPRE muestran el porcentaje real del total
    function syncKPIWithLegend(meta, labels) {
        // No ocultar porcentajes, siempre visibles
        // Atendidas = primer segmento, Pendientes = segundo (ajustar si cambia el orden)
        const atendidasKPI = document.querySelector('.card.text-white.bg-success .card-text:not(.display-4)');
        const pendientesKPI = document.querySelector('.card.text-white.bg-warning .card-text:not(.display-4)');
        atendidasKPI.style.visibility = 'visible';
        pendientesKPI.style.visibility = 'visible';
    }

    // CSS para tachar la leyenda
    const style = document.createElement('style');
    style.innerHTML = `
    .chartjs-legend li.strike label {
        text-decoration: line-through !important;
        opacity: 0.6;
    }
    `;
    document.head.appendChild(style);

    // Parchea la leyenda para aplicar la clase 'strike' a los ocultos
    function patchLegendStrike(chart) {
        setTimeout(() => {
            const legend = chart.canvas.parentNode.querySelector('.chartjs-legend');
            if (legend) {
                legend.querySelectorAll('li').forEach((li, i) => {
                    const meta = chart.getDatasetMeta(0);
                    if (meta.data[i] && meta.data[i].hidden) {
                        li.classList.add('strike');
                    } else {
                        li.classList.remove('strike');
                    }
                });
            }
        }, 10);
    }

    // Hacer clic en una sección del gráfico lleva a la lista filtrada
    function addChartClickNavigation(chart, tipo) {
        chart.canvas.onclick = function(evt) {
            const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
            if (points.length > 0) {
                const idx = points[0].index;
                const label = chart.data.labels[idx];
                // Redirigir a la lista de incidencias filtrada
                let url = '/incidencias?';
                if (tipo === 'estado') {
                    url += 'estado=' + encodeURIComponent(label);
                } else if (tipo === 'nivel') {
                    url += 'prioridad=' + encodeURIComponent(label);
                }
                window.location.href = url;
            }
        };
    }

    function renderCharts(incidenciasPorEstado, incidenciasPorNivel) {
        if (estadoChart) estadoChart.destroy();
        if (nivelChart) nivelChart.destroy();

        // Guardar el total global para incidencias por estado
        const totalEstado = incidenciasPorEstado.values.reduce((a, b) => a + b, 0) || 1;
        // Guardar el total global para incidencias por nivel
        const totalNivel = incidenciasPorNivel._totalGlobal || incidenciasPorNivel.values.reduce((a, b) => a + b, 0) || 1;

        // Configuración común para ambos gráficos
        function buildOptions(tipo) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    onComplete: function() {
                        drawPercentages(this);
                        patchLegendStrike(this);
                        if (tipo === 'estado') {
                            syncKPIWithLegend(this.getDatasetMeta(0), this.data.labels);
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            generateLabels: function(chart) {
                                return generateLegendLabels(chart);
                            },
                            font: function(context) {
                                // Parche: proteger si context.legendItem es undefined
                                const item = context && context.legendItem ? context.legendItem : {};
                                return {
                                    weight: 'bold',
                                    style: item.hidden ? 'normal' : 'normal',
                                    lineHeight: 1.2
                                };
                            },
                            color: function(context) {
                                return '#333';
                            }
                        },
                        onClick: legendOnClick
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                // El total SIEMPRE debe ser la suma de todos los datos, no solo los visibles
                                const total = context.dataset.data.reduce((a, b) => a + b, 0) || 1;
                                return `${label}: ${value} (${formatPercentage(value, total)})`;
                            },
                            afterBody: function(context) {
                                if (!context[0] || !context[0].label) return [];
                                if (tipo === 'estado') {
                                    const estado = context[0].label;
                                    const detalles = incidenciasPorEstado.detalles;
                                    let info = ['Detalle por niveles:'];
                                    if (detalles[estado]) {
                                        const totalPorEstado = Object.values(detalles[estado]).reduce((a, b) => a + b, 0) || 1;
                                        for (const [nivel, cantidad] of Object.entries(detalles[estado])) {
                                            if (cantidad > 0) {
                                                info.push(`${nivel}: ${cantidad} (${formatPercentage(cantidad, totalPorEstado)})`);
                                            }
                                        }
                                    } else {
                                        info.push('Sin detalles disponibles');
                                    }
                                    return info;
                                }
                                return [];
                            }
                        }
                    }
                }
            };
        }

        // Gráfico por estado
        const estadoCtx = document.getElementById('estadoChart').getContext('2d');
        estadoChart = new Chart(estadoCtx, {
            type: 'pie',
            data: {
                labels: incidenciasPorEstado.labels,
                datasets: [{
                    data: incidenciasPorEstado.values,
                    backgroundColor: incidenciasPorEstado.colors,
                    borderWidth: 1
                }],
                _totalGlobal: totalEstado
            },
            options: buildOptions('estado')
        });
        addChartClickNavigation(estadoChart, 'estado');

        // Gráfico por nivel
        const nivelCtx = document.getElementById('nivelChart').getContext('2d');
        nivelChart = new Chart(nivelCtx, {
            type: 'doughnut',
            data: {
                labels: incidenciasPorNivel.labels,
                datasets: [{
                    data: incidenciasPorNivel.values,
                    backgroundColor: incidenciasPorNivel.colors,
                    borderWidth: 1
                }],
                _totalGlobal: totalNivel
            },
            options: buildOptions('nivel')
        });
        addChartClickNavigation(nivelChart, 'nivel');
    }

    // Función para actualizar KPIs (sin porcentajes al lado de los botones)
    function updateKPIs(data) {
        document.querySelector('.card.text-white.bg-primary .display-4').textContent = data.totalIncidencias;
        document.querySelector('.card.text-white.bg-success .display-4').textContent = data.incidenciasAtendidas;
        document.querySelector('.card.text-white.bg-warning .display-4').textContent = data.incidenciasPendientes;
        document.querySelector('.card.text-white.bg-danger .display-4').textContent = data.incidenciasPorVencer;
        // Restaurar porcentajes en las tarjetas
        const total = data.totalIncidencias > 0 ? data.totalIncidencias : 1;
        document.querySelector('.card.text-white.bg-success .card-text:not(.display-4)').textContent = `${Math.round((data.incidenciasAtendidas/total)*100)}%`;
        document.querySelector('.card.text-white.bg-warning .card-text:not(.display-4)').textContent = `${Math.round((data.incidenciasPendientes/total)*100)}%`;
    }

    // Función para obtener los filtros actuales
    function getFilters() {
        const form = document.getElementById('filtrosForm');
        return new URLSearchParams(new FormData(form)).toString();
    }

    // Función para cargar estaciones según institución (y actualizar filtro)
    function loadEstacionesAndFilter(institucionId, callback) {
        const estacionSelect = document.getElementById('estacion_id');
        estacionSelect.innerHTML = '<option value="">Cargando...</option>';
        estacionSelect.disabled = true;
        if (institucionId) {
            fetch(`/api/estaciones-por-institucion/${institucionId}`)
                .then(response => response.json())
                .then(data => {
                    estacionSelect.innerHTML = '<option value="">Todas</option>';
                    if (data && data.length > 0) {
                        data.forEach(estacion => {
                            estacionSelect.innerHTML += `<option value="${estacion.id_institucion_estacion}">${estacion.nombre}</option>`;
                        });
                    }
                    estacionSelect.disabled = false;
                    if (callback) callback();
                })
                .catch(() => {
                    estacionSelect.innerHTML = '<option value="">Error al cargar</option>';
                    estacionSelect.disabled = false;
                    if (callback) callback();
                });
        } else {
            estacionSelect.innerHTML = '<option value="">Todas</option>';
            estacionSelect.disabled = false;
            if (callback) callback();
        }
    }

    // Función para filtrar y actualizar todo
    function filtrarYActualizar() {
        document.body.classList.add('loading');
        fetch(`{{ route('graficos.incidencias') }}?${getFilters()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.ok ? response.json() : Promise.reject('Error en la respuesta'))
        .then(data => {
            updateKPIs(data);
            // Si el backend retorna incidenciasPorNivel._totalGlobal, pásalo
            if (data.incidenciasPorNivel && typeof data.incidenciasPorNivel._totalGlobal === 'number') {
                data.incidenciasPorNivel._totalGlobal = data.incidenciasPorNivel._totalGlobal;
            } else {
                // fallback: usar suma de todos los valores
                data.incidenciasPorNivel._totalGlobal = data.incidenciasPorNivel.values.reduce((a, b) => a + b, 0) || 1;
            }
            renderCharts(data.incidenciasPorEstado, data.incidenciasPorNivel);
        })
        .catch(() => {
            alert('Ocurrió un error al filtrar las incidencias.');
        })
        .finally(() => document.body.classList.remove('loading'));
    }

    // Prevenir el submit tradicional del formulario
    document.getElementById('filtrosForm').addEventListener('submit', function(e) {
        e.preventDefault();
    });

    // Listeners para todos los filtros (con debounce)
    document.querySelectorAll('input, select').forEach(el => {
        if (el.id === 'institucion_id') {
            el.addEventListener('change', function() {
                loadEstacionesAndFilter(this.value, () => debounce(filtrarYActualizar));
            });
        } else {
            el.addEventListener('change', () => debounce(filtrarYActualizar));
        }
    });
    // Incluir los inputs de fecha explícitamente
    document.querySelectorAll('input[name="start_date"], input[name="end_date"]').forEach(el => {
        el.addEventListener('change', () => debounce(filtrarYActualizar));
    });

    // Inicializar gráficos con los datos iniciales SOLO después de un pequeño timeout para evitar bloqueos de renderizado
    setTimeout(function() {
        renderCharts(@json($incidenciasPorEstado), @json($incidenciasPorNivel));
        initialChartsRendered = true;
    }, 10);

    // === DESCARGA PDF ===
    document.getElementById('descargarPDF').addEventListener('click', async function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'p', unit: 'pt', format: 'a4' });
        let y = 40;
        // Título
        doc.setFontSize(18);
        doc.text('Estadísticas de Incidencias', 40, y);
        y += 30;
        // === MEMBRETE (cabecera institucional) ===
        // Si existe un membrete institucional, agrégalo al PDF
        let membreteHtml = @json($membreteHtml ?? '');
        if (membreteHtml && membreteHtml.length > 0) {
            // Crear un elemento temporal para extraer solo el texto (sin HTML)
            let tempDiv = document.createElement('div');
            tempDiv.innerHTML = membreteHtml;
            let membreteText = tempDiv.textContent || tempDiv.innerText || '';
            doc.setFontSize(11);
            doc.setTextColor(30,30,30);
            doc.text(membreteText.trim(), 40, y, {maxWidth: 500});
            y += 30;
        }
        // Filtros
        doc.setFontSize(12);
        const filtros = [];
        const form = document.getElementById('filtrosForm');
        const fd = new FormData(form);
        if (fd.get('start_date') && fd.get('end_date')) {
            filtros.push(`Período: ${fd.get('start_date')} al ${fd.get('end_date')}`);
        }
        if (fd.get('tipo_incidencia_id')) {
            const tipo = form.querySelector('select[name="tipo_incidencia_id"] option:checked').textContent.trim();
            filtros.push(`Tipo: ${tipo}`);
        }
        if (fd.get('nivel_incidencia_id')) {
            const nivel = form.querySelector('select[name="nivel_incidencia_id"] option:checked').textContent.trim();
            filtros.push(`Nivel: ${nivel}`);
        }
        if (fd.get('institucion_id')) {
            const inst = form.querySelector('select[name="institucion_id"] option:checked').textContent.trim();
            filtros.push(`Institución: ${inst}`);
        }
        if (fd.get('estacion_id')) {
            const est = form.querySelector('select[name="estacion_id"] option:checked').textContent.trim();
            filtros.push(`Estación: ${est}`);
        }
        filtros.forEach(f => {
            doc.text(f, 40, y);
            y += 18;
        });
        y += 10;
        // KPIs
        const kpis = document.querySelectorAll('.row.d-flex .card');
        kpis.forEach(card => {
            const title = card.querySelector('.card-title').textContent.trim();
            const value = card.querySelector('.display-4').textContent.trim();
            let percent = '';
            const percentEl = card.querySelector('.card-text:not(.display-4)');
            if (percentEl) percent = percentEl.textContent.trim();
            let kpiText = `${title}: ${value}`;
            if (percent) kpiText += ` (${percent})`;
            doc.text(kpiText, 40, y);
            y += 18;
        });
        y += 10;
        // Gráficos como imágenes
        async function addChartImage(canvasId, titulo) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const imgData = canvas.toDataURL('image/png', 1.0);
            doc.setFontSize(14);
            doc.text(titulo, 40, y);
            y += 10;
            doc.addImage(imgData, 'PNG', 40, y, 220, 220);
            y += 230;
        }
        await addChartImage('estadoChart', 'Incidencias por Estado');
        await addChartImage('nivelChart', 'Incidencias por Nivel');
        y += 10;
        // === TABLAS DETALLADAS ===
        // Si jsPDF autotable está disponible, usarla
        if (window.jspdf && window.jspdf.autoTable) {
            // Tabla: Incidencias por Estado
            doc.setFontSize(14);
            doc.text('Incidencias por Estado', 40, y);
            y += 10;
            doc.autoTable({
                startY: y,
                head: [['Estado', 'Cantidad', 'Porcentaje']],
                body: (function() {
                    const total = estadoChart.data._totalGlobal || estadoChart.data.datasets[0].data.reduce((a,b)=>a+b,0) || 1;
                    return estadoChart.data.labels.map((label, i) => [
                        label,
                        estadoChart.data.datasets[0].data[i],
                        total > 0 ? ((estadoChart.data.datasets[0].data[i]/total*100).toFixed(1)+'%') : '0%'
                    ]);
                })(),
                theme: 'grid',
                margin: { left: 40, right: 40 },
                styles: { fontSize: 11 },
                headStyles: { fillColor: [245,245,245], textColor: 20, fontStyle: 'bold' },
            });
            y = doc.lastAutoTable.finalY + 20;
            // Tabla: Incidencias por Nivel de Prioridad
            doc.setFontSize(14);
            doc.text('Incidencias por Nivel de Prioridad', 40, y);
            y += 10;
            doc.autoTable({
                startY: y,
                head: [['Nivel', 'Cantidad', 'Porcentaje']],
                body: (function() {
                    const total = nivelChart.data._totalGlobal || nivelChart.data.datasets[0].data.reduce((a,b)=>a+b,0) || 1;
                    return nivelChart.data.labels.map((label, i) => [
                        label,
                        nivelChart.data.datasets[0].data[i],
                        total > 0 ? ((nivelChart.data.datasets[0].data[i]/total*100).toFixed(1)+'%') : '0%'
                    ]);
                })(),
                theme: 'grid',
                margin: { left: 40, right: 40 },
                styles: { fontSize: 11 },
                headStyles: { fillColor: [245,245,245], textColor: 20, fontStyle: 'bold' },
            });
            y = doc.lastAutoTable.finalY + 20;
        } else {
            // Fallback: generar tablas manualmente en el PDF con mejor estructura visual
            function drawManualTable(title, headers, rows, startY) {
                let y = startY;
                doc.setFontSize(14);
                doc.setTextColor(40,40,40);
                doc.text(title, 40, y);
                y += 14;
                doc.setDrawColor(180,180,180);
                doc.setLineWidth(0.5);
                // Encabezados
                doc.setFontSize(11);
                doc.setTextColor(60,60,60);
                let colX = [50, 250, 350];
                headers.forEach((h, i) => {
                    doc.text(h, colX[i], y);
                });
                y += 10;
                doc.line(45, y, 420, y); // línea bajo encabezado
                y += 6;
                doc.setFontSize(11);
                doc.setTextColor(20,20,20);
                rows.forEach(row => {
                    row.forEach((cell, i) => {
                        doc.text(String(cell), colX[i], y);
                    });
                    y += 13;
                });
                y += 8;
                return y;
            }
            // Estado
            const totalEstado = estadoChart.data._totalGlobal || estadoChart.data.datasets[0].data.reduce((a,b)=>a+b,0) || 1;
            const rowsEstado = estadoChart.data.labels.map((label, i) => [
                label,
                estadoChart.data.datasets[0].data[i],
                totalEstado > 0 ? ((estadoChart.data.datasets[0].data[i]/totalEstado*100).toFixed(1)+'%') : '0%'
            ]);
            y = drawManualTable('Incidencias por Estado', ['Estado', 'Cantidad', 'Porcentaje'], rowsEstado, y);
            // Nivel
            const totalNivel = nivelChart.data._totalGlobal || nivelChart.data.datasets[0].data.reduce((a,b)=>a+b,0) || 1;
            const rowsNivel = nivelChart.data.labels.map((label, i) => [
                label,
                nivelChart.data.datasets[0].data[i],
                totalNivel > 0 ? ((nivelChart.data.datasets[0].data[i]/totalNivel*100).toFixed(1)+'%') : '0%'
            ]);
            y = drawManualTable('Incidencias por Nivel de Prioridad', ['Nivel', 'Cantidad', 'Porcentaje'], rowsNivel, y);
        }
        // Pie de página
        doc.setFontSize(10);
        doc.setTextColor(80,80,80);
        doc.text('Generado el: {{ now()->format('d/m/Y H:i:s') }}', 40, 820);
        doc.save('estadisticas_incidencias.pdf');
    });
});
</script>
@endsection