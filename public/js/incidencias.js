class FiltroIncidencias {
    constructor(codigoInputId, fechaInicioId, fechaFinId, estadoId, prioridadId, tbodyId, url) {
        this.codigoInput = document.getElementById(codigoInputId);
        this.fechaInicio = document.getElementById(fechaInicioId);
        this.fechaFin = document.getElementById(fechaFinId);
        this.estado = document.getElementById(estadoId);
        this.prioridad = document.getElementById(prioridadId);
        this.tbody = document.getElementById(tbodyId);
        this.url = url;
        this.ultimaActualizacion = document.getElementById('ultima-actualizacion');
        this.intervaloActualizacion = null;

        // Event listeners
        this.codigoInput.addEventListener('input', () => this.filtrarIncidencias());
        this.fechaInicio.addEventListener('change', () => this.filtrarIncidencias());
        this.fechaFin.addEventListener('change', () => this.filtrarIncidencias());
        this.estado.addEventListener('change', () => this.filtrarIncidencias());
        this.prioridad.addEventListener('change', () => this.filtrarIncidencias());

        // Iniciar actualización automática cada 5 minutos (300000 ms)
        this.iniciarActualizacionAutomatica();

        // Cargar datos iniciales
        this.filtrarIncidencias();
    }

    async filtrarIncidencias() {
        const filtros = {
            codigo: this.codigoInput.value,
            fecha_inicio: this.fechaInicio.value,
            fecha_fin: this.fechaFin.value,
            estado: this.estado.value,
            prioridad: this.prioridad.value
        };

        try {
            const response = await fetch(this.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(filtros)
            });

            if (!response.ok) {
                throw new Error('Error al filtrar las incidencias');
            }

            const data = await response.json();

            if (data.success) {
                this.mostrarResultados(data.incidencias);
                if (data.fecha_actualizacion) {
                    this.ultimaActualizacion.textContent = `Última actualización: ${data.fecha_actualizacion}`;
                }
            } else {
                this.mostrarError(data.message || 'Error al obtener las incidencias');
            }
        } catch (error) {
            console.error('Error al filtrar incidencias:', error);
            this.mostrarError('Error al conectar con el servidor');
        }
    }

    iniciarActualizacionAutomatica() {
        // Limpiar intervalo existente si hay uno
        if (this.intervaloActualizacion) {
            clearInterval(this.intervaloActualizacion);
        }

        // Establecer nuevo intervalo (5 minutos = 300000 ms)
        this.intervaloActualizacion = setInterval(() => {
            this.filtrarIncidencias();
            this.mostrarNotificacion('info', 'Datos actualizados automáticamente');
        }, 120000);
    }

    mostrarResultados(incidencias) {
    this.tbody.innerHTML = '';

    if (incidencias && incidencias.length > 0) {
        incidencias.forEach(incidencia => {
            const fecha = new Date(incidencia.created_at);
            const fechaFormateada = fecha.toLocaleString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Información del usuario que registró
            let registradoPor = '<em>No registrado</em>';
            if (incidencia.usuario && incidencia.usuario.empleado_autorizado) {
                const emp = incidencia.usuario.empleado_autorizado;
                registradoPor = `${emp.nombre} ${emp.apellido} <strong>V-</strong>${emp.cedula}`;
            } else if (incidencia.usuario?.empleadoAutorizado) {
                const emp = incidencia.usuario.empleadoAutorizado;
                registradoPor = `${emp.nombre} ${emp.apellido} <strong>V-</strong>${emp.cedula}`;
            }

            // Información de la persona asociada
            let personaInfo = '<em>Incidencia General</em>';
            if (incidencia.persona) {
                personaInfo = `${incidencia.persona.nombre} ${incidencia.persona.apellido} <strong>V-</strong>${incidencia.persona.cedula}`;
            }

            // Tiempo restante
            let tiempoRestante = '<em>Sin fecha</em>';
            if (incidencia.fecha_vencimiento) {
                const fechaVencimiento = new Date(incidencia.fecha_vencimiento);
                const ahora = new Date();

                // Obtener el estado de la incidencia
                const estadoIncidencia = incidencia.estadoIncidencia || incidencia.estado_incidencia;
                const esAtendido = estadoIncidencia?.nombre?.toLowerCase() === 'atendido';

                if (esAtendido) {
                    tiempoRestante = '<span class="text-success">Resuelto</span>';
                } else if (ahora > fechaVencimiento) {
                    tiempoRestante = '<span class="time-critical">Vencido</span>';
                } else if ((fechaVencimiento - ahora) < 86400000) { // Menos de 24 horas
                    tiempoRestante = `<span class="time-warning">${this.formatTimeRemaining(fechaVencimiento)}</span>`;
                } else {
                    tiempoRestante = this.formatTimeRemaining(fechaVencimiento);
                }
            }

            // Get priority and status data
            const nivelIncidencia = incidencia.nivelIncidencia || incidencia.nivel_incidencia;
            const estadoIncidencia = incidencia.estadoIncidencia || incidencia.estado_incidencia;
            const tipoIncidencia = incidencia.tipoIncidencia || incidencia.tipo_incidencia;
            const tipoIncidenciaNombre = tipoIncidencia?.nombre || 'Sin Tipo';
            // Verificar si está atendido
            const esAtendido = estadoIncidencia?.nombre?.toLowerCase() === 'atendido';

            // Botones de acción (siempre disponibles: ver y descargar)
            const dropdownItems = `
                <li>
                    <a class="dropdown-item" href="/incidencias/${incidencia.slug}/ver">
                        <i class="bi bi-eye me-2"></i>Ver
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="/incidencias/descargar/${incidencia.slug}">
                        <i class="bi bi-download me-2"></i>Descargar
                    </a>
                </li>
                ${!esAtendido ? `
                <li>
                    <a class="dropdown-item" href="/incidencias/${incidencia.slug}/atender">
                        <i class="bi bi-check-circle me-2"></i>Atender
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="/incidencias/${incidencia.slug}/edit">
                        <i class="bi bi-pencil-square me-2"></i>Modificar
                    </a>
                </li>
                ` : ''}
            `;

            const tr = document.createElement('tr');
            tr.setAttribute('data-incidencia-id', incidencia.slug);
            tr.innerHTML = `
                <td>${incidencia.cod_incidencia}</td>
                <td>${tipoIncidenciaNombre || 'Sin Tipo'}</td> <!-- Tipo de incidencia -->
                <td>${incidencia.descripcion?.substring(0, 50) || ''}</td>
                <td>
                    <span class="priority-badge" style="background-color: ${nivelIncidencia?.color || '#6c757d'}">
                        ${nivelIncidencia?.nombre || 'N/A'}
                    </span>
                </td>
                <td>
                    <span class="status-badge" style="background-color: ${estadoIncidencia?.color || '#6c757d'}">
                        ${estadoIncidencia?.nombre || 'N/A'}
                    </span>
                </td>
                <td>${fechaFormateada}</td>
                <td>${registradoPor}</td>
                <td>${personaInfo}</td>
                <td>${tiempoRestante}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            ${dropdownItems}
                        </ul>
                    </div>
                </td>
            `;

            this.tbody.appendChild(tr);
        });
    } else {
        this.tbody.innerHTML = '<tr><td colspan="10" class="text-center">No se encontraron incidencias para los filtros seleccionados.</td></tr>';
    }
}


    formatTimeRemaining(fechaVencimiento) {
        const ahora = new Date();
        const diff = fechaVencimiento - ahora;

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        if (days > 0) return `${days}d ${hours}h`;
        if (hours > 0) return `${hours}h ${minutes}m`;
        return `${minutes}m`;
    }

    mostrarError(mensaje) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.textContent = mensaje;

        // Insertar antes de la tabla
        this.tbody.parentNode.parentNode.insertBefore(errorDiv, this.tbody.parentNode);

        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    mostrarNotificacion(tipo, mensaje) {
        const alertClass = tipo === 'info' ? 'alert-info' : (tipo === 'success' ? 'alert-success' : 'alert-danger');
        const notificacion = document.createElement('div');
        notificacion.className = `alert ${alertClass} fixed-top mx-auto mt-3`;
        notificacion.style.width = 'fit-content';
        notificacion.style.maxWidth = '80%';
        notificacion.style.zIndex = '9999';
        notificacion.textContent = mensaje;

        document.body.appendChild(notificacion);

        setTimeout(() => {
            notificacion.remove();
        }, 3000);
    }
}

// Inicializar la clase cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    const filtro = new FiltroIncidencias(
        'codigo-busqueda',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'prioridad',
        'incidencias-tbody',
        '/filtrar-incidencia'
    );

    // Configurar el formulario de PDF
    document.getElementById('generar-pdf-form')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const fechaInicio = document.getElementById('fecha_inicio').value || '';
        const fechaFin = document.getElementById('fecha_fin').value || '';
        const estado = document.getElementById('estado').value || 'Todos';
        const prioridad = document.getElementById('prioridad').value || 'Todos';
        const codigo = document.getElementById('codigo-busqueda').value || '';

        document.getElementById('pdf-fecha-inicio').value = fechaInicio;
        document.getElementById('pdf-fecha-fin').value = fechaFin;
        document.getElementById('pdf-estado').value = estado;
        document.getElementById('pdf-prioridad').value = prioridad;
        document.getElementById('pdf-codigo').value = codigo;


        this.submit();
    });
});

// Función para el seguimiento del step en las incidencias (si es necesario)
document.addEventListener("DOMContentLoaded", function () {
    const steps = document.querySelectorAll(".step");
    const indicators = document.querySelectorAll("#stepIndicator .nav-link");

    if (steps.length > 0) {
        let currentStep = 1;

        function showStep(step) {
            steps.forEach((s, i) => {
                s.classList.toggle("d-none", i !== step - 1);
            });

            indicators.forEach((ind, i) => {
                ind.classList.remove("active", "disabled");
                if (i + 1 === step) {
                    ind.classList.add("active");
                } else if (i + 1 < step) {
                    ind.classList.add("completed");
                } else {
                    ind.classList.add("disabled");
                }
            });

            currentStep = step;
        }

        function validateStep(step) {
            const form = document.getElementById("incidenciaGeneralForm");
            const inputs = steps[step - 1].querySelectorAll("input, select, textarea");
            let isValid = true;

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add("is-invalid");
                    isValid = false;
                } else {
                    input.classList.remove("is-invalid");
                }
            });

            return isValid;
        }

        // Asignar event listeners solo si los elementos existen
        document.getElementById("next-to-step-2")?.addEventListener("click", function () {
            if (validateStep(1)) showStep(2);
        });

        document.getElementById("back-to-step-1")?.addEventListener("click", function () {
            showStep(1);
        });

        document.getElementById("next-to-step-3")?.addEventListener("click", function () {
            if (validateStep(2)) showStep(3);
        });

        document.getElementById("back-to-step-2")?.addEventListener("click", function () {
            showStep(2);
        });

        showStep(1); // inicializar
    }
});