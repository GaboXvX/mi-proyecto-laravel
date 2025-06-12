class FiltroIncidencias {
    constructor(codigoInputId, fechaInicioId, fechaFinId, estadoId, prioridadId, tipoId, tableId, url) { // Changed tbodyId to tableId
        this.codigoInput = document.getElementById(codigoInputId);
        this.fechaInicio = document.getElementById(fechaInicioId);
        this.fechaFin = document.getElementById(fechaFinId);
        this.estado = document.getElementById(estadoId);
        this.prioridad = document.getElementById(prioridadId);
        this.tipo = document.getElementById(tipoId);
        this.table = document.getElementById(tableId); // Reference to the table element
        this.url = url;
        this.ultimaActualizacion = document.getElementById('ultima-actualizacion');
        this.intervaloActualizacion = null;
        this.dataTable = null; // To store the DataTables instance

        // Initialize DataTables
        this.initializeDataTable();

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

        // Iniciar actualización automática cada 2 minutos (120000 ms)
        this.iniciarActualizacionAutomatica();

        // Cargar datos iniciales
        this.filtrarIncidencias();
    }

    initializeDataTable() {
        this.dataTable = $(this.table).DataTable({
            // DataTables options for pagination, search, etc.
            "language": {
                "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json" // Spanish language file
            },
            "paging": true,       
            "searching": false,   
            "info": true,         
            "ordering": true,     
            "autoWidth": false,   
            "columns": [          
                { "data": "cod_incidencia" },
                { "data": "tipo_incidencia_nombre" },
                { "data": "prioridad" },
                { "data": "estado" },
                { "data": "fecha_formateada" },
                { "data": "registrado_por" },
                { "data": "persona_info" },
                { "data": "comunidad_info" },
                { "data": "tiempo_restante" },
                { "data": "acciones", "orderable": false } // Actions column is not orderable
            ]
        });
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
        if (this.intervaloActualizacion) {
            clearInterval(this.intervaloActualizacion);
        }

        this.intervaloActualizacion = setInterval(() => {
            this.filtrarIncidencias();
            this.mostrarNotificacion('info', 'Datos actualizados automáticamente');
        }, 120000); // 2 minutes
    }

    mostrarResultados(incidencias) {
        // Clear existing DataTables data
        this.dataTable.clear();
        const rowsToAdd = [];

        if (incidencias && incidencias.length > 0) {
            incidencias.forEach(incidencia => {
                const fecha = new Date(incidencia.created_at);
                const fechaFormateada = fecha.toLocaleString('es-VE', {
                    timeZone: 'America/Caracas',
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

                let registradoPor = '<em>No registrado</em>';
                if (incidencia.usuario && incidencia.usuario.empleado_autorizado) {
                    const emp = incidencia.usuario.empleado_autorizado;
                    registradoPor = `${emp.nombre} ${emp.apellido} <strong>V-</strong>${emp.cedula}`;
                } else if (incidencia.usuario?.empleadoAutorizado) {
                    const emp = incidencia.usuario.empleadoAutorizado;
                    registradoPor = `${emp.nombre} ${emp.apellido} <strong>V-</strong>${emp.cedula}`;
                }

                let personaInfo = '<em>Incidencia General</em>';
                if (incidencia.persona) {
                    personaInfo = `${incidencia.persona.nombre} ${incidencia.persona.apellido} <strong>V-</strong>${incidencia.persona.cedula}`;
                }

                let comunidadInfo = '<em>Sin comunidad</em>';
                const direccionData = incidencia.direccionIncidencia || incidencia.direccion_incidencia;
                if (direccionData && direccionData.comunidad && direccionData.comunidad.nombre) {
                    comunidadInfo = direccionData.comunidad.nombre;
                }

                let tiempoRestante = '<em>Sin fecha</em>';
                const nivelIncidencia = incidencia.nivelIncidencia || incidencia.nivel_incidencia;
                const estadoIncidencia = incidencia.estadoIncidencia || incidencia.estado_incidencia;
                const tipoIncidencia = incidencia.tipoIncidencia || incidencia.tipo_incidencia;
                const tipoIncidenciaNombre = tipoIncidencia?.nombre || 'Sin Tipo';
                const esAtendido = estadoIncidencia?.nombre?.toLowerCase() === 'atendido';

                if (incidencia.fecha_vencimiento) {
                    const fechaVencimiento = new Date(incidencia.fecha_vencimiento);
                    const ahora = new Date();

                    if (esAtendido) {
                        tiempoRestante = '<span class="text-success">Resuelto</span>';
                    } else if (ahora > fechaVencimiento) {
                        tiempoRestante = '<span class="time-critical">Vencido</span>';
                    } else if ((fechaVencimiento - ahora) < 86400000) { // Less than 24 hours
                        tiempoRestante = `<span class="time-warning">${this.formatTimeRemaining(fechaVencimiento)}</span>`;
                    } else {
                        tiempoRestante = this.formatTimeRemaining(fechaVencimiento);
                    }
                }

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

                rowsToAdd.push({
                    "cod_incidencia": incidencia.cod_incidencia,
                    "tipo_incidencia_nombre": tipoIncidenciaNombre,
                    "prioridad": `<span class="priority-badge" style="background-color: ${nivelIncidencia?.color || '#6c757d'}">${nivelIncidencia?.nombre || 'N/A'}</span>`,
                    "estado": `<span class="status-badge" style="background-color: ${estadoIncidencia?.color || '#6c757d'}">${estadoIncidencia?.nombre || 'N/A'}</span>`,
                    "fecha_formateada": fechaFormateada,
                    "registrado_por": registradoPor,
                    "persona_info": personaInfo,
                    "comunidad_info": comunidadInfo,
                    "tiempo_restante": tiempoRestante,
                    "acciones": `
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                ${dropdownItems}
                            </ul>
                        </div>
                    `
                });
            });
        }

        // Add new rows to DataTables
        this.dataTable.rows.add(rowsToAdd).draw();

        // If no data, show the "No se encontraron incidencias" message in DataTables
        if (incidencias.length === 0) {
            this.dataTable.rows.add([]).draw(); // This will trigger DataTables' "No matching records found"
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
        // DataTables handles "No matching records found" internally.
        // This method can still be used for general errors, not related to no data found.
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.textContent = mensaje;

        // Insert before the table's parent (assuming table is inside a div)
        // Find the closest parent div that contains the table, and insert before it.
        const tableContainer = this.table.closest('.table-container');
        if (tableContainer) {
            tableContainer.insertBefore(errorDiv, tableContainer.firstChild);
        } else {
            document.body.appendChild(errorDiv); // Fallback if container not found
        }


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
    const requiredElements = [
        'codigo-busqueda',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'prioridad',
        'tipo',
        'tabla-incidencias' // Changed to tabla-incidencias
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
            'tabla-incidencias', // Pass the ID of the table element
            '/filtrar-incidencia'
        );

        // Configure the PDF form
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

// Function for step tracking in incidents (existing code, ensure it doesn't conflict)
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

    // Assign events to inputs
    step1Inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', validateStep1);
    });

    // Assign events to selects
    selectsPaso1.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', validateStep1);
    });

    // Initial validation with delay (in case Livewire is populating)
    setTimeout(validateStep1, 300);

    // Revalidate after any Livewire update
    if (window.Livewire) {
        Livewire.hook('message.processed', () => {
            validateStep1();
        });
    }

    // Step 2 validation
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

    // Navigation between steps
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

    // Buttons disabled by default
    nextStep1.disabled = true;
    nextStep2.disabled = true;
});