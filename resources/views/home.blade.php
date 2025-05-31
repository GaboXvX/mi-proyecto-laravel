@extends('layouts.app')
@section('content')
    <h3 class="separator">
        Panel
    </h3>

    <!-- Tus tarjetas de acceso se mantienen igual -->
    <div class="card-access">
        <!-- ... -->
    </div>

    <!-- Filtros y gráfica de incidencias -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><strong>Crecimiento de Incidencias</strong></span>
                    <form id="filtros-incidencias" class="d-flex gap-2 align-items-center" autocomplete="off">
                        <label class="mb-0">Tipo:</label>
                        <select name="tipo_incidencia_id" id="tipo_incidencia_id" class="form-select form-select-sm">
                            <option value="">Todos</option>
                        </select>
                        <label class="mb-0 ms-2">Nivel:</label>
                        <select name="nivel_incidencia_id" id="nivel_incidencia_id" class="form-select form-select-sm">
                            <option value="">Todos</option>
                        </select>
                        <label class="mb-0 ms-2">Mes:</label>
                        <select name="mes" id="mes" class="form-select form-select-sm">
                            <option value="">Todos</option>
                        </select>
                        <label class="mb-0 ms-2">Año:</label>
                        <select name="anio" id="anio" class="form-select form-select-sm">
                            <!-- Se llenará con JavaScript -->
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <canvas id="temporalChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header"><strong>5 Incidencias más recientes</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Nivel</th>
                                    <th>Comunidad</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-incidencias-recientes">
                                <tr><td colspan="5" class="text-center">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/chart.umd.min.js"></script>
    <script>
    // Variables globales
    let temporalChart;
    const meses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    // Cargar tipos de incidencia
    async function cargarTiposIncidencia() {
        const select = document.getElementById('tipo_incidencia_id');
        try {
            const res = await fetch('/api/tipos-incidencia');
            const tipos = await res.json();
            tipos.forEach(tipo => {
                const opt = document.createElement('option');
                opt.value = tipo.id_tipo_incidencia;
                opt.textContent = tipo.nombre;
                select.appendChild(opt);
            });
        } catch {}
    }

    // Cargar niveles de incidencia
    async function cargarNivelesIncidencia() {
        const select = document.getElementById('nivel_incidencia_id');
        try {
            const res = await fetch('/api/niveles-incidencia');
            const niveles = await res.json();
            niveles.forEach(nivel => {
                const opt = document.createElement('option');
                opt.value = nivel.id_nivel_incidencia;
                opt.textContent = nivel.nombre;
                select.appendChild(opt);
            });
        } catch {}
    }

    // Cargar años disponibles
    function cargarAnios() {
        const select = document.getElementById('anio');
        const currentYear = new Date().getFullYear();
        const primerAnio = 2000; // Cambia este valor si tu sistema tiene datos desde antes
        // Limpiar y agregar opción "Todos"
        select.innerHTML = '<option value="">Todos</option>';
        // Agregar todos los años desde el primer año hasta el actual
        for (let y = currentYear; y >= primerAnio; y--) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            select.appendChild(opt);
        }
        // Seleccionar año actual por defecto
        select.value = currentYear;
    }

    // Cargar meses
    function cargarMeses() {
        const select = document.getElementById('mes');
        const currentMonth = new Date().getMonth() + 1;
        // Limpiar y agregar opción "Todos"
        select.innerHTML = '<option value="">Todos</option>';
        // Agregar meses
        meses.forEach((nombre, index) => {
            const opt = document.createElement('option');
            opt.value = index + 1;
            opt.textContent = nombre;
            select.appendChild(opt);
        });
        // Seleccionar "Todos" por defecto
        select.value = '';
    }

    // Obtener fechas según filtros
    function getFechasFiltro() {
        const mes = document.getElementById('mes').value;
        const anio = document.getElementById('anio').value;
        
        // Si no se seleccionó mes o año, mostrar todos los datos
        if (!mes || !anio) {
            return { fecha_inicio: null, fecha_fin: null };
        }
        
        // Primer día del mes seleccionado
        const fecha_inicio = `${anio}-${String(mes).padStart(2, '0')}-01`;
        
        // Último día del mes seleccionado
        const ultimoDia = new Date(anio, mes, 0).getDate();
        const fecha_fin = `${anio}-${String(mes).padStart(2, '0')}-${ultimoDia}`;
        
        return { fecha_inicio, fecha_fin };
    }

    // Cargar gráfica
    async function cargarGrafica() {
        const tipo = document.getElementById('tipo_incidencia_id').value;
        const nivel = document.getElementById('nivel_incidencia_id').value;
        const mes = document.getElementById('mes').value;
        const anio = document.getElementById('anio').value;
        let labels = [];
        let datos = [];
        let temporalChartTooltipExtras = null;

        if (anio && !mes) {
            // Si hay año pero mes en "Todos": mostrar evolución de enero hasta el mes actual si es el año actual, o hasta diciembre si es año pasado
            const hoy = new Date();
            const esAnioActual = parseInt(anio) === hoy.getFullYear();
            const mesLimite = esAnioActual ? hoy.getMonth() + 1 : 12;
            const fecha_inicio = `${anio}-01-01`;
            const ultimoDia = new Date(anio, mesLimite, 0).getDate();
            const fecha_fin = `${anio}-${String(mesLimite).padStart(2, '0')}-${ultimoDia}`;
            const params = new URLSearchParams({
                tipo_incidencia_id: tipo,
                nivel_incidencia_id: nivel,
                fecha_inicio,
                fecha_fin,
                agrupado: 'mes'
            });
            try {
                const res = await fetch(`/home/incidencias-temporales?${params.toString()}`);
                const data = await res.json();
                labels = (data.labels || meses).slice(0, mesLimite);
                datos = (data.data || Array(12).fill(0)).slice(0, mesLimite);
                temporalChartTooltipExtras = null;
            } catch (error) {
                console.error('Error al cargar la gráfica:', error);
                labels = meses.slice(0, mesLimite);
                datos = Array(mesLimite).fill(0);
                temporalChartTooltipExtras = null;
            }
        } else if (mes && anio) {
            // Mostrar evolución desde enero hasta el mes seleccionado
            const fecha_inicio = `${anio}-01-01`;
            const ultimoDia = new Date(anio, mes, 0).getDate();
            const fecha_fin = `${anio}-${String(mes).padStart(2, '0')}-${ultimoDia}`;
            const params = new URLSearchParams({
                tipo_incidencia_id: tipo,
                nivel_incidencia_id: nivel,
                fecha_inicio,
                fecha_fin,
                agrupado: 'mes'
            });
            try {
                const res = await fetch(`/home/incidencias-temporales?${params.toString()}`);
                const data = await res.json();
                labels = data.labels; // Ej: ['Enero', ..., 'Mes seleccionado']
                datos = data.data;
                temporalChartTooltipExtras = null;
            } catch (error) {
                console.error('Error al cargar la gráfica:', error);
                labels = meses.slice(0, mes);
                datos = Array.from({length: mes}, () => 0);
                temporalChartTooltipExtras = null;
            }
        } else {
            // Si no hay mes ni año seleccionado, mostrar picos mensuales (día con más incidencias de cada mes)
            const { fecha_inicio: fi, fecha_fin: ff } = getFechasFiltro();
            const params = new URLSearchParams({ tipo_incidencia_id: tipo, nivel_incidencia_id: nivel });
            if (fi && ff) {
                params.append('fecha_inicio', fi);
                params.append('fecha_fin', ff);
            }
            try {
                const res = await fetch(`/home/incidencias-temporales?${params.toString()}`);
                const data = await res.json();
                labels = data.labels;
                datos = data.data;
                // Si el backend devuelve un campo extra con la fecha exacta del pico, úsalo. Si no, parsea desde el label
                temporalChartTooltipExtras = data.fechas_pico || null;
            } catch (error) {
                console.error('Error al cargar la gráfica:', error);
                temporalChartTooltipExtras = null;
            }
        }

        // Crear o actualizar gráfica
        const ctx = document.getElementById('temporalChart').getContext('2d');
        const customTooltip = {
            callbacks: {
                title: function(context) {
                    // Si hay fechas de pico, mostrar la fecha completa en formato cristiano
                    if (temporalChartTooltipExtras && context[0]) {
                        const idx = context[0].dataIndex;
                        const fecha = temporalChartTooltipExtras[idx];
                        if (fecha) {
                            // Formato: 2025-05-30 => 30 de mayo de 2025
                            const partes = fecha.split('-');
                            const mesesLargos = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                            return `${parseInt(partes[2],10)} de ${mesesLargos[parseInt(partes[1],10)-1]} de ${partes[0]}`;
                        }
                    }
                    // Si no, mostrar el label normal
                    return context[0].label;
                }
            }
        };
        if (temporalChart) {
            temporalChart.data.labels = labels;
            temporalChart.data.datasets[0].data = datos;
            temporalChart.options.plugins.tooltip = Object.assign({}, temporalChart.options.plugins.tooltip, customTooltip);
            temporalChart.update();
        } else {
            temporalChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Incidencias registradas',
                        data: datos,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0,123,255,0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                        pointBackgroundColor: '#007bff',
                        pointBorderColor: '#fff',
                        pointStyle: 'circle'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: Object.assign({
                            mode: 'index',
                            intersect: false
                        }, customTooltip)
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Mes' },
                            grid: { display: false }
                        },
                        y: {
                            title: { display: true, text: 'Cantidad' },
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    }

    // Cargar incidencias recientes (sin filtros)
    async function cargarIncidenciasRecientes() {
        try {
            const res = await fetch('/home/incidencias-recientes');
            const incidencias = await res.json();
            const tbody = document.getElementById('tbody-incidencias-recientes');
            tbody.innerHTML = '';
            if (incidencias.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay incidencias recientes</td></tr>';
                return;
            }
            incidencias.slice(0, 5).forEach(incidencia => {
                const estadoColor = incidencia.estado_incidencia?.color || '#888';
                const nivelColor = incidencia.nivel_incidencia?.color || '#888';
                const codigo = incidencia.codigo || incidencia.cod_incidencia || 'N/A';
                const tipo = incidencia.tipo_incidencia?.nombre || incidencia.tipoIncidencia?.nombre || 'N/A';
                const estado = incidencia.estado_incidencia?.nombre || incidencia.estadoIncidencia?.nombre || 'N/A';
                const nivel = incidencia.nivel_incidencia?.nombre || incidencia.nivelIncidencia?.nombre || 'N/A';
                // Comunidad: acceso robusto igual que en incidencias.js
                let comunidad = '<em>Sin comunidad</em>';
                if (incidencia.direccionIncidencia && incidencia.direccionIncidencia.comunidad) {
                    comunidad = incidencia.direccionIncidencia.comunidad?.nombre || '<em>Sin comunidad</em>';
                } else if (incidencia.direccion_incidencia && incidencia.direccion_incidencia.comunidad) {
                    comunidad = incidencia.direccion_incidencia.comunidad?.nombre || '<em>Sin comunidad</em>';
                }
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${codigo}</td>
                    <td>${tipo}</td>
                    <td>${estado}</td>
                    <td>${nivel}</td>
                    <td>${comunidad}</td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            console.error('Error al cargar incidencias recientes:', error);
            document.getElementById('tbody-incidencias-recientes').innerHTML = 
                '<tr><td colspan="5" class="text-center">Error al cargar datos</td></tr>';
        }
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', async () => {
        // Cargar selectores
        await cargarTiposIncidencia();
        await cargarNivelesIncidencia();
        cargarAnios();
        cargarMeses();
        
        // Cargar datos iniciales
        await cargarGrafica();
        await cargarIncidenciasRecientes();
        
        // Event listeners para filtros
        document.getElementById('tipo_incidencia_id').addEventListener('change', cargarGrafica);
        document.getElementById('nivel_incidencia_id').addEventListener('change', cargarGrafica);
        document.getElementById('mes').addEventListener('change', cargarGrafica);
        document.getElementById('anio').addEventListener('change', cargarGrafica);
    });
    </script>
@endsection