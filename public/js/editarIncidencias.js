document.addEventListener('DOMContentLoaded', function () {
    // Navegación entre pasos
    const steps = document.querySelectorAll('.step');
    const nextToStep2 = document.getElementById('next-to-step-2');
    const nextToStep3 = document.getElementById('next-to-step-3');
    const backToStep1 = document.getElementById('back-to-step-1');
    const backToStep2 = document.getElementById('back-to-step-2');

    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('d-none', index !== stepIndex);
        });
        
        // Actualizar indicador de pasos
        document.querySelectorAll('#stepIndicator .nav-link').forEach((link, index) => {
            if (index < stepIndex + 1) {
                link.classList.remove('disabled');
                link.classList.add('active');
            } else {
                link.classList.add('disabled');
                link.classList.remove('active');
            }
        });
    }

    nextToStep2.addEventListener('click', () => showStep(1));
    nextToStep3.addEventListener('click', () => showStep(2));
    backToStep1.addEventListener('click', () => showStep(0));
    backToStep2.addEventListener('click', () => showStep(1));

    // --- SCRIPTS PARA INSTITUCIONES Y ESTACIONES DE APOYO (igual que en registrar) ---
    // Mostrar/ocultar sección de apoyo
    const btnAgregarApoyo = document.getElementById('btn-agregar-apoyo');
    const contenedorApoyo = document.getElementById('contenedor-apoyo');
    if (document.getElementById('lista-apoyo').children.length > 0) {
        btnAgregarApoyo.innerHTML = '<i class="bi bi-dash-circle"></i> Ocultar';
    }
    btnAgregarApoyo.addEventListener('click', function() {
        contenedorApoyo.classList.toggle('d-none');
        if (contenedorApoyo.classList.contains('d-none')) {
            btnAgregarApoyo.innerHTML = '<i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo';
            institucionApoyoSelect.value = '';
            estacionApoyoSelect.innerHTML = '<option value="" selected>--Seleccione una estación--</option>';
            estacionApoyoSelect.disabled = true;
        } else {
            btnAgregarApoyo.innerHTML = '<i class="bi bi-dash-circle"></i> Ocultar';
        }
    });
    // Selects de apoyo
    const institucionApoyoSelect = document.getElementById('institucion_apoyo');
    const estacionApoyoSelect = document.getElementById('estacion_apoyo');
    let estacionPrincipalId = document.getElementById('estacion').value;
    function cargarEstacionesApoyo(institucionId) {
        if (!institucionId) {
            estacionApoyoSelect.innerHTML = '<option value="">Primero seleccione una institución</option>';
            estacionApoyoSelect.disabled = true;
            return;
        }
        estacionApoyoSelect.disabled = true;
        estacionApoyoSelect.innerHTML = '<option value="">Cargando estaciones...</option>';
        const url = `/personal-reparacion/estaciones/${institucionId}`;
        fetch(url)
            .then(response => response.json())
            .then(({ success, data }) => {
                estacionApoyoSelect.innerHTML = '<option value="">Seleccione una estación</option>';
                if (success && data.length > 0) {
                    const estacionesYaSeleccionadas = Array.from(document.querySelectorAll('#lista-apoyo .list-group-item'))
                        .map(item => item.getAttribute('data-estacion-id'));
                    data.forEach(estacion => {
                        if (estacion.id != estacionPrincipalId && !estacionesYaSeleccionadas.includes(estacion.id.toString())) {
                            const nombre = estacion.codigo ? `${estacion.nombre} (${estacion.codigo})` : estacion.nombre;
                            const option = new Option(nombre, estacion.id);
                            estacionApoyoSelect.add(option);
                        }
                    });
                    estacionApoyoSelect.disabled = false;
                    if (estacionApoyoSelect.options.length === 1) {
                        estacionApoyoSelect.innerHTML = '<option value="">No hay estaciones disponibles</option>';
                        estacionApoyoSelect.disabled = true;
                    }
                } else {
                    estacionApoyoSelect.innerHTML = '<option value="">No hay estaciones disponibles</option>';
                    estacionApoyoSelect.disabled = true;
                }
            })
            .catch(() => {
                estacionApoyoSelect.innerHTML = '<option value="">Error al cargar estaciones</option>';
                estacionApoyoSelect.disabled = true;
            });
    }
    document.getElementById('estacion').addEventListener('change', function() {
        estacionPrincipalId = this.value;
        const institucionId = institucionApoyoSelect.value;
        if (institucionId) cargarEstacionesApoyo(institucionId);
    });
    institucionApoyoSelect.addEventListener('change', function() {
        cargarEstacionesApoyo(this.value);
    });
    // Agregar institución de apoyo a la lista
    const btnAgregarItem = document.getElementById('btn-agregar-item');
    const listaApoyo = document.getElementById('lista-apoyo');
    const camposOcultosApoyo = document.getElementById('campos-ocultos-apoyo');
    btnAgregarItem.addEventListener('click', function() {
        const institucionId = institucionApoyoSelect.value;
        const estacionId = estacionApoyoSelect.value;
        if (!institucionId) {
            Swal.fire({ icon: 'warning', title: 'Seleccione una institución', text: 'Debe seleccionar una institución antes de agregar', confirmButtonText: 'Entendido' });
            return;
        }
        if (!estacionId) {
            Swal.fire({ icon: 'warning', title: 'Seleccione una estación', text: 'Debe seleccionar una estación antes de agregar', confirmButtonText: 'Entendido' });
            return;
        }
        const institucionNombre = institucionApoyoSelect.options[institucionApoyoSelect.selectedIndex].text;
        const estacionNombre = estacionApoyoSelect.options[estacionApoyoSelect.selectedIndex].text;
        const existe = Array.from(listaApoyo.children).some(item => item.getAttribute('data-institucion-id') === institucionId && item.getAttribute('data-estacion-id') === estacionId);
        if (existe) {
            Swal.fire({ icon: 'warning', title: 'Institución ya agregada', text: 'Esta combinación de institución y estación ya fue agregada', confirmButtonText: 'Entendido' });
            return;
        }
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.setAttribute('data-institucion-id', institucionId);
        listItem.setAttribute('data-estacion-id', estacionId);
        listItem.setAttribute('data-nueva', 'true');
        listItem.innerHTML = `<div><strong>${institucionNombre}</strong> - ${estacionNombre}</div><button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-apoyo"><i class="bi bi-trash"></i></button>`;
        const inputInstitucion = document.createElement('input');
        inputInstitucion.type = 'hidden';
        inputInstitucion.name = 'instituciones_apoyo[]';
        inputInstitucion.value = institucionId;
        const inputEstacion = document.createElement('input');
        inputEstacion.type = 'hidden';
        inputEstacion.name = 'estaciones_apoyo[]';
        inputEstacion.value = estacionId || '';
        listaApoyo.appendChild(listItem);
        camposOcultosApoyo.appendChild(inputInstitucion);
        camposOcultosApoyo.appendChild(inputEstacion);
        if (institucionApoyoSelect.value) cargarEstacionesApoyo(institucionApoyoSelect.value);
        listItem.querySelector('.btn-eliminar-apoyo').addEventListener('click', function() {
            if (listItem.getAttribute('data-existente') === 'true') {
                Swal.fire({ icon: 'warning', title: 'Acción no permitida', text: 'No se pueden eliminar instituciones de apoyo existentes', confirmButtonText: 'Entendido' });
                return;
            }
            listItem.remove();
            const inputs = Array.from(camposOcultosApoyo.querySelectorAll('input'));
            const index = inputs.findIndex(input => input.name === 'instituciones_apoyo[]' && input.value === institucionId);
            if (index !== -1) {
                camposOcultosApoyo.removeChild(inputs[index]);
                camposOcultosApoyo.removeChild(inputs[index + 1]);
            }
            if (institucionApoyoSelect.value) cargarEstacionesApoyo(institucionApoyoSelect.value);
        });
    });
    // --- DESHABILITAR OPCIONES DE ESTACIÓN PRINCIPAL QUE YA ESTÁN COMO APOYO (MISMA INSTITUCIÓN) ---
    function deshabilitarEstacionesPrincipalesDeApoyo() {
        const estacionSelect = document.getElementById('estacion');
        const institucionSelect = document.getElementById('institucion');
        const institucionPrincipalId = institucionSelect.value;
        const estacionesApoyo = Array.from(document.querySelectorAll('#lista-apoyo .list-group-item'))
            .map(item => ({ institucionId: item.getAttribute('data-institucion-id'), estacionId: item.getAttribute('data-estacion-id') }))
            .filter(item => item.estacionId && item.estacionId !== '');
        Array.from(estacionSelect.options).forEach(opt => { opt.disabled = false; });
        estacionesApoyo.forEach(item => {
            if (item.institucionId === institucionPrincipalId) {
                const opt = Array.from(estacionSelect.options).find(o => o.value === item.estacionId);
                if (opt) opt.disabled = true;
            }
        });
        const selectedOption = estacionSelect.options[estacionSelect.selectedIndex];
        if (selectedOption && selectedOption.disabled) {
            const firstEnabled = Array.from(estacionSelect.options).find(opt => !opt.disabled && opt.value !== '');
            if (firstEnabled) estacionSelect.value = firstEnabled.value;
            else estacionSelect.value = '';
        }
    }
    deshabilitarEstacionesPrincipalesDeApoyo();
    const observer = new MutationObserver(deshabilitarEstacionesPrincipalesDeApoyo);
    observer.observe(document.getElementById('lista-apoyo'), { childList: true });
    document.getElementById('institucion').addEventListener('change', deshabilitarEstacionesPrincipalesDeApoyo);
    // --- SINCRONIZAR ESTACIONES AL CAMBIAR INSTITUCIÓN PRINCIPAL ---
    document.getElementById('institucion').addEventListener('change', function() {
        const institucionId = this.value;
        const estacionSelect = document.getElementById('estacion');
        estacionSelect.innerHTML = '<option value="" disabled selected>--Seleccione una estación--</option>';
        estacionSelect.value = '';
        estacionSelect.disabled = true;
        if (!institucionId) {
            estacionSelect.innerHTML = '<option value="" disabled selected>--Seleccione una institución primero--</option>';
            return;
        }
        fetch(`/personal-reparacion/estaciones/${institucionId}`)
            .then(response => response.json())
            .then(({ success, data }) => {
                estacionSelect.innerHTML = '<option value="" disabled selected>--Seleccione una estación--</option>';
                if (success && data.length > 0) {
                    data.forEach(estacion => {
                        const nombre = estacion.codigo ? `${estacion.nombre} (${estacion.codigo})` : estacion.nombre;
                        const option = new Option(nombre, estacion.id);
                        estacionSelect.add(option);
                    });
                    estacionSelect.disabled = false;
                    deshabilitarEstacionesPrincipalesDeApoyo();
                } else {
                    estacionSelect.innerHTML = '<option value="" disabled selected>--No hay estaciones disponibles--</option>';
                }
            })
            .catch(() => {
                estacionSelect.innerHTML = '<option value="" disabled selected>--Error al cargar estaciones--</option>';
            });
    });
    // --- LIMPIEZA DE APOYOS SI CAMBIA LA PRINCIPAL (FRONTEND) ---
    document.getElementById('institucion').addEventListener('change', function() {
        const institucionId = this.value;
        const listaApoyo = document.getElementById('lista-apoyo');
        const camposOcultosApoyo = document.getElementById('campos-ocultos-apoyo');
        Array.from(listaApoyo.children).forEach(item => {
            if (item.getAttribute('data-institucion-id') === institucionId) {
                const institucionApoyo = item.getAttribute('data-institucion-id');
                const estacionApoyo = item.getAttribute('data-estacion-id');
                const inputs = Array.from(camposOcultosApoyo.querySelectorAll('input'));
                for (let i = inputs.length - 2; i >= 0; i -= 2) {
                    if (inputs[i].value === institucionApoyo && inputs[i+1].value === estacionApoyo) {
                        camposOcultosApoyo.removeChild(inputs[i]);
                        camposOcultosApoyo.removeChild(inputs[i+1]);
                    }
                }
                item.remove();
            }
        });
        const estacionSelect = document.getElementById('estacion');
        const estacionesApoyo = Array.from(document.querySelectorAll('#lista-apoyo .list-group-item'))
            .filter(item => item.getAttribute('data-institucion-id') === institucionId)
            .map(item => item.getAttribute('data-estacion-id'));
        if (estacionesApoyo.includes(estacionSelect.value)) estacionSelect.value = '';
        const institucionApoyoSelect = document.getElementById('institucion_apoyo');
        const estacionApoyoSelect = document.getElementById('estacion_apoyo');
        if (institucionApoyoSelect.value === institucionId) {
            institucionApoyoSelect.value = '';
            estacionApoyoSelect.innerHTML = '<option value="" selected>--Seleccione una estación--</option>';
        }
    });
    // --- REFORZAR DESHABILITACIÓN Y LIMPIEZA DE CAMPOS OCULTOS ---
    function deshabilitarEstacionesPrincipalesDeApoyo() {
        const estacionSelect = document.getElementById('estacion');
        const institucionPrincipalId = document.getElementById('institucion').value;
        const listaApoyo = document.getElementById('lista-apoyo');
        const camposOcultosApoyo = document.getElementById('campos-ocultos-apoyo');
        const estacionesApoyo = Array.from(document.querySelectorAll('#lista-apoyo .list-group-item'))
            .map(item => ({ institucionId: item.getAttribute('data-institucion-id'), estacionId: item.getAttribute('data-estacion-id') }))
            .filter(item => item.estacionId && item.estacionId !== '');
        Array.from(estacionSelect.options).forEach(opt => { opt.disabled = false; });
        estacionesApoyo.forEach(item => {
            if (item.institucionId === institucionPrincipalId) {
                const opt = Array.from(estacionSelect.options).find(o => o.value === item.estacionId);
                if (opt) opt.disabled = true;
            }
        });
        const selectedOption = estacionSelect.options[estacionSelect.selectedIndex];
        if (!estacionSelect.value || (selectedOption && selectedOption.disabled)) estacionSelect.value = '';
        const inputs = Array.from(camposOcultosApoyo.querySelectorAll('input'));
        for (let i = inputs.length - 2; i >= 0; i -= 2) {
            const inst = inputs[i].value;
            const est = inputs[i+1].value;
            const existeVisual = Array.from(listaApoyo.children).some(item => item.getAttribute('data-institucion-id') === inst && item.getAttribute('data-estacion-id') === est);
            if (!existeVisual) {
                camposOcultosApoyo.removeChild(inputs[i]);
                camposOcultosApoyo.removeChild(inputs[i+1]);
            }
        }
    }

    // --- VALIDACIÓN FINAL Y SINCRONIZACIÓN ANTES DE ENVIAR EL FORMULARIO ---
    const form = document.getElementById('form-editar-incidencia');
    form.onsubmit = async function(event) {
        // Sincronizar campos ocultos con la lista visual antes de enviar
        const listaApoyo = document.getElementById('lista-apoyo');
        const camposOcultosApoyo = document.getElementById('campos-ocultos-apoyo');
        // Eliminar todos los campos ocultos
        while (camposOcultosApoyo.firstChild) {
            camposOcultosApoyo.removeChild(camposOcultosApoyo.firstChild);
        }
        // Volver a crear los campos ocultos según la lista visual
        Array.from(listaApoyo.children).forEach(item => {
            const inst = item.getAttribute('data-institucion-id');
            const est = item.getAttribute('data-estacion-id');
            const inputInst = document.createElement('input');
            inputInst.type = 'hidden';
            inputInst.name = 'instituciones_apoyo[]';
            inputInst.value = inst;
            const inputEst = document.createElement('input');
            inputEst.type = 'hidden';
            inputEst.name = 'estaciones_apoyo[]';
            inputEst.value = est || '';
            camposOcultosApoyo.appendChild(inputInst);
            camposOcultosApoyo.appendChild(inputEst);
        });
        // Validar conflicto: estación principal no puede ser apoyo para la misma institución
        const estacionPrincipalId = document.getElementById('estacion').value;
        const institucionPrincipalId = document.getElementById('institucion').value;
        const conflicto = Array.from(document.querySelectorAll('#lista-apoyo .list-group-item'))
            .some(item => {
                const itemInstitucionId = item.getAttribute('data-institucion-id');
                const itemEstacionId = item.getAttribute('data-estacion-id');
                return itemEstacionId === estacionPrincipalId && itemInstitucionId === institucionPrincipalId;
            });
        if (conflicto) {
            event.preventDefault();
            await Swal.fire({
                icon: 'warning',
                title: 'Conflicto de estación',
                text: 'No puedes seleccionar como estación principal una estación que ya está asignada como apoyo para la misma institución.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        // --- ENVÍO AJAX Y ALERTA DE ÉXITO ---
        event.preventDefault();
        const swalInstance = Swal.fire({
            title: 'Procesando',
            html: 'Actualizando la incidencia...',
            allowOutsideClick: false,
            showConfirmButton: false,
            allowEscapeKey: false,
            didOpen: () => { Swal.showLoading(); }
        });
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if (!response.ok) {
                if (result.is_duplicate) {
                    await swalInstance.close();
                    const { value: accept } = await Swal.fire({
                        title: 'Incidencia Duplicada',
                        html: `<div class="text-start"><p>${result.message}</p><div class="card mt-3"><div class="card-body"><h6 class="card-title">Detalles de la incidencia existente:</h6><p><strong>Código:</strong> ${result.duplicate_data.codigo}</p><p><strong>Descripción:</strong> ${result.duplicate_data.descripcion}</p><p><strong>Fecha:</strong> ${result.duplicate_data.fecha_creacion}</p><p><strong>Estado:</strong> ${result.duplicate_data.estado}</p><p><strong>Prioridad:</strong> ${result.duplicate_data.prioridad}</p><a href="${result.ver_url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">Ver incidencia existente</a></div></div></div>`,
                        confirmButtonText: 'OK',
                        focusConfirm: false,
                        allowOutsideClick: false
                    });
                    return;
                }
                throw new Error(result.message || `Error en la operación: ${response.statusText}`);
            }
            await swalInstance.close();
            await Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: result.message,
                showConfirmButton: true,
                confirmButtonText: 'Aceptar',
                timer: 3000,
                timerProgressBar: true
            });
            if (result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                window.location.reload();
            }
        } catch (error) {
            if (swalInstance.isActive()) swalInstance.close();
            let errorMessage = 'Ocurrió un error al procesar la solicitud';
            if (error instanceof SyntaxError) errorMessage = 'Error al interpretar la respuesta del servidor';
            else if (error.message) errorMessage = error.message;
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonText: 'Entendido',
                allowOutsideClick: false
            });
        }
        return false;
    };
});