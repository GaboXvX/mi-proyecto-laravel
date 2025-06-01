@extends('layouts.app')
@section('content')
    <h3 class="separator">
        Panel
    </h3>

    <!-- Acceso directo -->
    <div class="card-access">
            <a href="{{ route('usuarios.index') }}" class="item text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11 14.0619V20H13V14.0619C16.9463 14.554 20 17.9204 20 22H4C4 17.9204 7.05369 14.554 11 14.0619ZM12 13C8.685 13 6 10.315 6 7C6 3.685 8.685 1 12 1C15.315 1 18 3.685 18 7C18 10.315 15.315 13 12 13Z"></path></svg>
                <h5>Empleados</h5>
                <p>{{ $totalUsuarios }}</p>
            </a>
            
            <a href="{{ route('incidencias.index') }}" class="item text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M15 4H5V20H19V8H15V4ZM3 2.9918C3 2.44405 3.44749 2 3.9985 2H16L20.9997 7L21 20.9925C21 21.5489 20.5551 22 20.0066 22H3.9934C3.44476 22 3 21.5447 3 21.0082V2.9918ZM11 15H13V17H11V15ZM11 7H13V13H11V7Z"></path></svg>
                <h5>Incidencias</h5>
                <p>{{ $totalIncidencias }}</p>
            </a>

            <a href="{{ route('personas.index') }}" class="item text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M2 22C2 17.5817 5.58172 14 10 14C14.4183 14 18 17.5817 18 22H16C16 18.6863 13.3137 16 10 16C6.68629 16 4 18.6863 4 22H2ZM10 13C6.685 13 4 10.315 4 7C4 3.685 6.685 1 10 1C13.315 1 16 3.685 16 7C16 10.315 13.315 13 10 13ZM10 11C12.21 11 14 9.21 14 7C14 4.79 12.21 3 10 3C7.79 3 6 4.79 6 7C6 9.21 7.79 11 10 11ZM18.2837 14.7028C21.0644 15.9561 23 18.752 23 22H21C21 19.564 19.5483 17.4671 17.4628 16.5271L18.2837 14.7028ZM17.5962 3.41321C19.5944 4.23703 21 6.20361 21 8.5C21 11.3702 18.8042 13.7252 16 13.9776V11.9646C17.6967 11.7222 19 10.264 19 8.5C19 7.11935 18.2016 5.92603 17.041 5.35635L17.5962 3.41321Z"></path></svg>
                <h5>Personas</h5>
                <p>{{ $totalPersonas }}</p>
            </a>

            <a href="{{ route('peticiones.index') }}" class="item text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 22H4C3.44772 22 3 21.5523 3 21V3C3 2.44772 3.44772 2 4 2H20C20.5523 2 21 2.44772 21 3V21C21 21.5523 20.5523 22 20 22ZM7 6V10H11V6H7ZM7 12V14H17V12H7ZM7 16V18H17V16H7ZM13 7V9H17V7H13Z"></path></svg>
                <h5>Peticiones</h5>
                <p>{{ $totalPeticiones }}</p>
            </a>
    </div>

    <div class="detalles-panel">
    <!-- Gráfica de Incidencias -->
    <div class="card-chart" style="flex: 2;">
        <div class="header-row justify-content-between">
            <h3>Crecimiento de Incidencias</h3>
            <a href="{{ route('graficos.incidencias') }}" class="btn btn-sm btn-success">Ver más</a>
            <button class="btn btn-sm btn-primary justify-content-end" id="btnDescargarGrafico">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                </svg>
            </button>
        </div>

        <form id="filtros-incidencias" class="filtros-panel" autocomplete="off">
            <label>Tipo:</label>
            <select name="tipo_incidencia_id" id="tipo_incidencia_id">
                <option value="">Todo</option>
            </select>
            <label>Nivel:</label>
            <select name="nivel_incidencia_id" id="nivel_incidencia_id">
                <option value="">Todo</option>
            </select>
            <label>Mes:</label>
            <select name="mes" id="mes">
                <option value="">Todo</option>
            </select>
            <label>Año:</label>
            <select name="anio" id="anio">
                <option value="2025">2025</option>
            </select>
        </form>

        <div class="chart-wrapper">
            <canvas id="temporalChart" height="220"></canvas>
        </div>
    </div>

    <!-- Tabla de Incidencias Recientes -->
    <div class="card-chart" style="flex: 1;">
        <div class="header-row">
            <h3>5 Incidencias más recientes</h3>
            <a href="{{ route('incidencias.index') }}" class="btn btn-sm btn-success" id="btnMasDetalles">Más detalles</a>
        </div>
        <div class="table-detalles">
            <table class="table table-striped align-middle">
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
                    <tr>
                        <td colspan="5" class="text-center">Cargando...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                // Soporte para ambos nombres de campo (codigo/cod_incidencia)
                const codigo = incidencia.codigo || incidencia.cod_incidencia || 'N/A';
                const tipo = incidencia.tipo_incidencia?.nombre || incidencia.tipoIncidencia?.nombre || 'N/A';
                const estado = incidencia.estado_incidencia?.nombre || incidencia.estadoIncidencia?.nombre || 'N/A';
                const nivel = incidencia.nivel_incidencia?.nombre || incidencia.nivelIncidencia?.nombre || 'N/A';
                // Comunidad: incidencia->direccionIncidencia->comunidad->nombre
                // Soporte para ambos nombres de campo (comunidad->nombre)
                const comunidad = (
                    incidencia.direccion_incidencia?.comunidad?.nombre ||
                    incidencia.direccionIncidencia?.comunidad?.nombre ||
                    'N/A'
                );
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${codigo}</td>
                    <td>${tipo}</td>
                    <td><span class="badge" style="background:${estadoColor};color:#fff;">${estado}</span></td>
                    <td><span class="badge" style="background:${nivelColor};color:#fff;">${nivel}</span></td>
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
    
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    document.getElementById('btnDescargarGrafico').addEventListener('click', function () {
    const canvas = document.getElementById('temporalChart');
    const imagenBase64 = canvas.toDataURL('image/png');

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("grafico.descargar") }}';

    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(token);

    const campos = ['tipo_incidencia_id', 'nivel_incidencia_id', 'mes', 'anio'];
    campos.forEach(campo => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = campo;
        input.value = document.getElementById(campo)?.value || '';
        form.appendChild(input);
    });

    const imagen = document.createElement('input');
    imagen.type = 'hidden';
    imagen.name = 'imagenGrafico';
    imagen.value = imagenBase64;
    form.appendChild(imagen);

    document.body.appendChild(form);
    form.submit();
    form.remove();
});
</script>

@endsection