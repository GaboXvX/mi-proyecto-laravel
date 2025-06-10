class FiltroIncidencias {
    constructor(codigoInputId, fechaInicioId, fechaFinId, estadoId, prioridadId, tipoId, tbodyId, url) {
        this.codigoInput = document.getElementById(codigoInputId);
        this.fechaInicio = document.getElementById(fechaInicioId);
        this.fechaFin = document.getElementById(fechaFinId);
        this.estado = document.getElementById(estadoId);
        this.prioridad = document.getElementById(prioridadId);
        this.tipo = document.getElementById(tipoId);
        this.tbody = document.getElementById(tbodyId);
        this.url = url;
        this.ultimaActualizacion = document.getElementById('ultima-actualizacion');
        this.intervaloActualizacion = null;

        // Verificar que los elementos existan antes de agregar event listeners
        if (this.codigoInput) {
            this.codigoInput.addEventListener('input', () => this.filtrarIncidencias());
        }
        if (this.fechaInicio) {
            this.fechaInicio.addEventListener('change', () => this.filtrarIncidencias());
        }
        if (this.fechaFin) {
            this.fechaFin.addEventListener('change', () => this.filtrarIncidencias());
        }
        if (this.estado) {
            this.estado.addEventListener('change', () => this.filtrarIncidencias());
        }
        if (this.prioridad) {
            this.prioridad.addEventListener('change', () => this.filtrarIncidencias());
        }
        if (this.tipo) {
            this.tipo.addEventListener('change', () => this.filtrarIncidencias());
        }

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
            prioridad: this.prioridad.value,
            tipo: this.tipo.value
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
const fechaFormateada = fecha.toLocaleString('es-VE', {
    timeZone: 'America/Caracas', // Zona horaria de Venezuela
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true // Formato 12h (AM/PM)
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

            // Información de la comunidad asociada
            let comunidadInfo = '<em>Sin comunidad</em>';
            // Unificar lógica: preferir direccionIncidencia.comunidad.nombre, luego direccion_incidencia.comunidad.nombre
            const direccionData = incidencia.direccionIncidencia || incidencia.direccion_incidencia;
            if (direccionData && direccionData.comunidad && direccionData.comunidad.nombre) {
                comunidadInfo = direccionData.comunidad.nombre;
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
                } else if ((fechaVencimiento - ahora) < 86400000) // Menos de 24 horas
                {
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
                <td>${tipoIncidenciaNombre || 'Sin Tipo'}</td>
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
                <td>${comunidadInfo}</td>
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

document.addEventListener('DOMContentLoaded', () => {
    // Verificar que los elementos requeridos existan antes de crear la instancia
    const requiredElements = [
        'codigo-busqueda',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'prioridad',
        'tipo',
        'incidencias-tbody'
    ];

    const allElementsExist = requiredElements.every(id => document.getElementById(id) !== null);

    if (allElementsExist) {
        const filtro = new FiltroIncidencias(
            'codigo-busqueda',
            'fecha_inicio',
            'fecha_fin',
            'estado',
            'prioridad',
            'tipo',
            'incidencias-tbody',
            '/filtrar-incidencia'
        );

        // Configurar el formulario de PDF
        const pdfForm = document.getElementById('generar-pdf-form');
        if (pdfForm) {
            pdfForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const fechaInicio = document.getElementById('fecha_inicio').value || '';
                const fechaFin = document.getElementById('fecha_fin').value || '';
                const estado = document.getElementById('estado').value || 'Todos';
                const prioridad = document.getElementById('prioridad').value || 'Todos';
                const tipo = document.getElementById('tipo').value || 'Todos';
                const codigo = document.getElementById('codigo-busqueda').value || '';

                document.getElementById('pdf-fecha-inicio').value = fechaInicio;
                document.getElementById('pdf-fecha-fin').value = fechaFin;
                document.getElementById('pdf-estado').value = estado;
                document.getElementById('pdf-prioridad').value = prioridad;
                document.getElementById('pdf-tipo').value = tipo;
                document.getElementById('pdf-codigo').value = codigo;

                this.submit();
            });
        }
    } else {
        console.error('No se encontraron todos los elementos requeridos en el DOM.');
    }
});

// Función para el seguimiento del step en las incidencias
document.addEventListener('DOMContentLoaded', function () {
    const step1Inputs = ['calle','punto_de_referencia'];
    const selectsPaso1 = ['parroquia', 'urbanizacion', 'sector', 'comunidad'];
    const nextStep1 = document.getElementById('next-to-step-2');

    const getValue = id => {
        const el = document.getElementById(id);
        return el ? el.value.trim() : '';
    };

    const validateStep1 = () => {
        const allFilled = [...step1Inputs, ...selectsPaso1].every(id => getValue(id) !== '');
        nextStep1.disabled = !allFilled;
    };

    // Asigna eventos a inputs
    step1Inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', validateStep1);
    });

    // Asigna eventos a selects
    selectsPaso1.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', validateStep1);
    });

    // Validación inicial con retraso (por si Livewire está llenando)
    setTimeout(validateStep1, 300);

    // Revalidar después de cualquier actualización Livewire
    if (window.Livewire) {
        Livewire.hook('message.processed', () => {
            validateStep1();
        });
    }

    // Paso 2 validación
    const nextStep2 = document.getElementById('next-to-step-3');
    const institucion = document.getElementById('institucion');
    const estacion = document.getElementById('estacion');

    const validateStep2 = () => {
        const valid = institucion.value !== '' && estacion.value !== '';
        nextStep2.disabled = !valid;
    };

    [institucion, estacion].forEach(select => select.addEventListener('change', validateStep2));
    setTimeout(validateStep2, 300);
    if (window.Livewire) {
        Livewire.hook('message.processed', () => {
            validateStep2();
        });
    }

    // Navegación entre pasos
    nextStep1.addEventListener('click', () => {
        document.getElementById('step-1').classList.add('d-none');
        document.getElementById('step-2').classList.remove('d-none');
        document.getElementById('stepProgressBar').style.width = '66%';
        document.getElementById('stepProgressBar').innerText = 'Paso 2 de 3';
    });

    document.getElementById('back-to-step-1').addEventListener('click', () => {
        document.getElementById('step-2').classList.add('d-none');
        document.getElementById('step-1').classList.remove('d-none');
        document.getElementById('stepProgressBar').style.width = '33%';
        document.getElementById('stepProgressBar').innerText = 'Paso 1 de 3';
    });

    nextStep2.addEventListener('click', () => {
        document.getElementById('step-2').classList.add('d-none');
        document.getElementById('step-3').classList.remove('d-none');
        document.getElementById('stepProgressBar').style.width = '100%';
        document.getElementById('stepProgressBar').innerText = 'Paso 3 de 3';
    });

    document.getElementById('back-to-step-2').addEventListener('click', () => {
        document.getElementById('step-3').classList.add('d-none');
        document.getElementById('step-2').classList.remove('d-none');
        document.getElementById('stepProgressBar').style.width = '66%';
        document.getElementById('stepProgressBar').innerText = 'Paso 2 de 3';
    });

    // Botones desactivados por defecto
    nextStep1.disabled = true;
    nextStep2.disabled = true;
});
