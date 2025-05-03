class FiltroIncidencias {
    constructor(codigoInputId, fechaInicioId, fechaFinId, estadoId, tbodyId, url) {
        this.codigoInput = document.getElementById(codigoInputId);
        this.fechaInicio = document.getElementById(fechaInicioId);
        this.fechaFin = document.getElementById(fechaFinId);
        this.estado = document.getElementById(estadoId);
        this.tbody = document.getElementById(tbodyId);
        this.url = url;
        this.ultimaActualizacion = document.getElementById('ultima-actualizacion');
        this.intervaloActualizacion = null;

        // Event listeners
        this.codigoInput.addEventListener('input', () => this.filtrarIncidencias());
        this.fechaInicio.addEventListener('change', () => this.filtrarIncidencias());
        this.fechaFin.addEventListener('change', () => this.filtrarIncidencias());
        this.estado.addEventListener('change', () => this.filtrarIncidencias());

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
            estado: this.estado.value
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
        }, 300000);
    }

    mostrarResultados(incidencias) {
        this.tbody.innerHTML = '';

        if (incidencias && incidencias.length > 0) {
            incidencias.forEach(incidencia => {
                const fecha = new Date(incidencia.created_at);
                const fechaFormateada = `${fecha.getDate().toString().padStart(2, '0')}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}-${fecha.getFullYear()} ${fecha.getHours().toString().padStart(2, '0')}:${fecha.getMinutes().toString().padStart(2, '0')}:${fecha.getSeconds().toString().padStart(2, '0')}`;

                const usuario = incidencia.usuario || {};
                const empleado = usuario.empleado_autorizado || {};
                
                let registradoPor = '<em>No registrado</em>';
                if (empleado.nombre) {
                    registradoPor = `${empleado.nombre} ${empleado.apellido} <strong>V-</strong>${empleado.cedula}`;
                }

                let personaInfo = '<em>Incidencia General</em>';
                if (incidencia.persona) {
                    personaInfo = `${incidencia.persona.nombre} ${incidencia.persona.apellido} <strong>V-</strong>${incidencia.persona.cedula}`;
                }

                const verButton = `<a href="/incidencias/${incidencia.slug}/ver" class="btn btn-info btn-sm">
                    <i class="bi bi-eye"></i> Ver
                </a>`;

                const descargarButton = `<a href="/incidencias/descargar/${incidencia.slug}" class="btn btn-primary btn-sm">
                    <i class="bi bi-download"></i>
                </a>`;

                const atenderButton = incidencia.estado === 'Por atender' 
                    ? `<a href="/incidencias/${incidencia.slug}/atender" class="btn btn-atender btn-sm">
                         <i class="bi bi-check-circle"></i> Atender
                       </a>` 
                    : `<button class="btn btn-atender btn-sm" disabled>
                         <i class="bi bi-check-circle"></i> Atendido
                       </button>`;

                const modificarButton = `<a href="/incidencias/${incidencia.slug}/edit" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> Modificar
                </a>`;

                const tr = document.createElement('tr');
                tr.setAttribute('data-incidencia-id', incidencia.slug);
                tr.innerHTML = `
                    <td>${incidencia.cod_incidencia}</td>
                    <td>${incidencia.tipo_incidencia}</td>
                    <td>${incidencia.descripcion}</td>
                    <td>${incidencia.nivel_prioridad}</td>
                    <td class="incidencia-status ${this.getStatusClass(incidencia.estado)}">${incidencia.estado}</td>
                    <td>${fechaFormateada}</td>
                    <td>${registradoPor}</td>
                    <td>${personaInfo}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            ${verButton}
                            ${descargarButton}
                            ${atenderButton}
                            ${modificarButton}
                        </div>
                    </td>
                `;

                this.tbody.appendChild(tr);
            });
        } else {
            this.tbody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron incidencias para los filtros seleccionados.</td></tr>';
        }
    }

    getStatusClass(estado) {
        switch (estado) {
            case 'Atendido': return 'status-resolved';
            case 'Por atender': return 'status-pending';
            default: return '';
        }
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
        'incidencias-tbody',
        '/filtrar-incidencia'
    );

    // Configurar el formulario de PDF
    document.getElementById('generar-pdf-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const fechaInicio = document.getElementById('fecha_inicio').value || '';
        const fechaFin = document.getElementById('fecha_fin').value || '';
        const estado = document.getElementById('estado').value || 'Todos';
        const codigo = document.getElementById('codigo-busqueda').value || '';

        document.getElementById('pdf-fecha-inicio').value = fechaInicio;
        document.getElementById('pdf-fecha-fin').value = fechaFin;
        document.getElementById('pdf-estado').value = estado;
        document.getElementById('pdf-codigo').value = codigo;

        this.submit();
    });
});

// funcion para el seguimiento del step en las incidencias
document.addEventListener("DOMContentLoaded", function () {
    const steps = document.querySelectorAll(".step");
    const indicators = document.querySelectorAll("#stepIndicator .nav-link");
    
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
        for (let input of inputs) {
            if (!input.checkValidity()) {
                input.classList.add("is-invalid");
                return false;
            } else {
                input.classList.remove("is-invalid");
            }
        }
        return true;
    }

    document.getElementById("next-to-step-2").addEventListener("click", function () {
        if (validateStep(1)) showStep(2);
    });

    document.getElementById("back-to-step-1").addEventListener("click", function () {
        showStep(1);
    });

    document.getElementById("next-to-step-3").addEventListener("click", function () {
        if (validateStep(2)) showStep(3);
    });

    document.getElementById("back-to-step-2").addEventListener("click", function () {
        showStep(2);
    });

    showStep(1); // inicializar
});