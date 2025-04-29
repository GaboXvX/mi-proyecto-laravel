class FiltroIncidencias {
    constructor(codigoInputId, fechaInicioId, fechaFinId, estadoId, tbodyId, url) {
        this.codigoInput = document.getElementById(codigoInputId);
        this.fechaInicio = document.getElementById(fechaInicioId);
        this.fechaFin = document.getElementById(fechaFinId);
        this.estado = document.getElementById(estadoId);
        this.tbody = document.getElementById(tbodyId);
        this.url = url;

        // Event listeners
        this.codigoInput.addEventListener('input', () => this.buscarPorCodigo());
        this.fechaInicio.addEventListener('change', () => this.filtrarIncidencias());
        this.fechaFin.addEventListener('change', () => this.filtrarIncidencias());
        this.estado.addEventListener('change', () => this.filtrarIncidencias());
    }

    async buscarPorCodigo() {
        const codigo = this.codigoInput.value;

        if (codigo.length === 0) {
            await this.filtrarIncidencias();
            return;
        }

        try {
            const response = await fetch(this.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ codigo })
            });

            if (!response.ok) {
                throw new Error('Error al buscar por código');
            }

            const data = await response.json();
            this.mostrarResultados(data.incidencias);
        } catch (error) {
            console.error('Error al buscar por código:', error);
        }
    }

    async filtrarIncidencias() {
        const fechaInicio = this.fechaInicio.value;
        const fechaFin = this.fechaFin.value;
        const estado = this.estado.value; // Obtener el valor del filtro de estado

        try {
            const response = await fetch(this.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ fecha_inicio: fechaInicio, fecha_fin: fechaFin, estado })
            });

            if (!response.ok) {
                throw new Error('Error al filtrar las incidencias');
            }

            const data = await response.json();
            this.mostrarResultados(data.incidencias);
        } catch (error) {
            console.error('Error al filtrar incidencias:', error);
        }
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

                let representanteInfo = '';
                if (incidencia.tipo === 'persona') {
                    const categoriaExclusiva = incidencia.categoria_exclusiva || {};
                    const representante = categoriaExclusiva.persona || {};
                    
                    if (representante.nombre) {
                        representanteInfo = `${representante.nombre} ${representante.apellido} <strong>V-</strong>${representante.cedula}`;
                        if (categoriaExclusiva.categoria) {
                            representanteInfo += `<br><strong>Categoría:</strong> ${categoriaExclusiva.categoria.nombre_categoria}`;
                        }
                    } else {
                        representanteInfo = '<em>No tiene un representante asignado</em>';
                    }
                } else {
                    representanteInfo = '<em>Incidencia General</em>';
                }

                const verButton = `<a href="/incidencias/${incidencia.slug}/ver" class="btn btn-info btn-sm">
                    <i class="bi bi-eye"></i> Ver
                </a>`;

                const atenderButton = incidencia.estado === 'Por atender' 
                    ? `<a href="/incidencias/${incidencia.slug}/atender" class="btn btn-atender btn-sm">
                         <i class="bi bi-check-circle"></i> Atender
                       </a>` 
                    : `<button class="btn btn-atender btn-sm" disabled>
                         <i class="bi bi-check-circle"></i> Atendido
                       </button>`;

                const modificarButton = `<a href="/modificarincidencialider/${incidencia.slug}" class="btn btn-warning btn-sm">
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
                    <td>${representanteInfo}</td>
                    <td>
                        <div class="d-flex gap-2">
                            ${verButton}
                            <a href="/incidencias/descargar/${incidencia.slug}" class="btn btn-primary btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
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
}

// Función para atender incidencia
async function atenderIncidencia(slug) {
    if (!confirm('¿Está seguro que desea marcar esta incidencia como atendida?')) {
        return;
    }

    try {
        const response = await fetch(`/incidencias/${slug}/atender`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error al atender incidencia:', error);
        alert('Ocurrió un error al atender la incidencia.');
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(tipo, mensaje) {
    const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
    const notificacion = document.createElement('div');
    notificacion.className = `alert ${alertClass} fixed-top mx-auto mt-3`;
    notificacion.style.width = 'fit-content';
    notificacion.style.maxWidth = '80%';
    notificacion.style.zIndex = '9999';
    notificacion.textContent = mensaje;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 5000);
}

// Inicializar la clase cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new FiltroIncidencias(
        'codigo-busqueda',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'incidencias-tbody',
        '/filtrar-incidencia'
    );

    document.getElementById('generar-pdf-form').addEventListener('submit', function (e) {
        e.preventDefault(); // Evitar el reinicio de la página

        const fechaInicio = document.getElementById('fecha_inicio').value || '';
        const fechaFin = document.getElementById('fecha_fin').value || '';
        const estado = document.getElementById('estado').value || 'Todos';
        const codigo = document.getElementById('codigo-busqueda').value || '';

        // Asignar los valores de los filtros a los campos ocultos del formulario
        document.getElementById('pdf-fecha-inicio').value = fechaInicio;
        document.getElementById('pdf-fecha-fin').value = fechaFin;
        document.getElementById('pdf-estado').value = estado;
        document.getElementById('pdf-codigo').value = codigo;

        // Enviar el formulario manualmente
        this.submit();
    });
});