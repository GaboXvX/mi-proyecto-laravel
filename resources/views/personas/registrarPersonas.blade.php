@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="table-container shadow" style="width: 100%; max-width: 600px;">
        <h2 class="text-center mb-4">Registrar Persona</h2>

        <div class="card-body px-4">
            <form id="registroPersonaForm" action="{{ route('personas.store') }}" method="POST">
                @csrf

                <div class="progress mb-3" style="height: 20px;">
                    <div id="stepProgressBar" class="progress-bar" role="progressbar" style="width: 50%;">
                        Paso 1 de 2
                    </div>
                </div>

                <!-- Sección de Datos Personales -->
                <div class="mb-4" id="datosPersonalesSection">
                    <h5 class="mb-3">Datos Personales</h5>
                    
                    <div class="row g-2 mb-2">
                        <label for="cedula" class="form-label">Cédula:</label>
                        <input type="text" id="cedula" name="cedula" class="form-control" maxlength="8" required>
                        <div id="cedulaStatus" style="display: none; color: green;">
                            <small>✔️ Cédula disponible</small>
                        </div>
                        <div id="cedulaError" style="color: red; display: none;">
                            <small>❌ Esta cédula ya está registrada</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control form-control-sm solo-letras" maxlength="12" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <input type="text" id="apellido" name="apellido" class="form-control form-control-sm solo-letras" maxlength="12" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo:</label>
                            <input type="email" id="correo" name="correo" class="form-control" maxlength="350" required>
                            <div id="correoStatus" style="display: none; color: green;">
                                <small>✔️ Correo disponible</small>
                            </div>
                            <div id="correoError" style="color: red; display: none;">
                                <small>❌ Este correo ya está registrado</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="genero" class="form-label">Género:</label>
                            <select name="genero" id="genero" class="form-select" required>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control" maxlength="11" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('personas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="button" id="continuarDireccionBtn" class="btn btn-primary" disabled>Continuar a Dirección</button>
                    </div>
                </div>

                <!-- Sección de Dirección (inicialmente oculta) -->
                <div class="mb-4" id="direccionSection" style="display: none;">
                    <h5 class="mb-3">Datos de Dirección</h5>

                    <livewire:dropdown-persona 
                        :estadoId="$domicilio->id_estado ?? null"
                        :municipioId="$domicilio->id_municipio ?? null"
                        :parroquiaId="$domicilio->id_parroquia ?? null"
                        :urbanizacionId="$domicilio->id_urbanizacion ?? null"
                        :sectorId="$domicilio->id_sector ?? null"
                        :comunidadId="$domicilio->id_comunidad ?? null"
                    />

                    <div class="row g-2 mb-2 mt-2">
                        <div class="col-md-6">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" id="calle" name="calle" class="form-control form-control-sm" required maxlength="16">
                        </div>
                        <div class="col-md-6">
                            <label for="manzana" class="form-label">Manzana:</label>
                            <input type="text" id="manzana" name="manzana" class="form-control form-control-sm" maxlength="10">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="bloque" class="form-label">Bloque:</label>
                            <input type="text" id="bloque" name="bloque" class="form-control form-control-sm" maxlength="3">
                        </div>
                        <div class="col-md-6">
                            <label for="num_vivienda" class="form-label">Número de Vivienda:</label>
                            <input type="text" id="num_vivienda" name="num_vivienda" class="form-control form-control-sm" maxlength="5" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col">
                            <label for="es_principal" class="form-label">¿Dirección principal?</label>
                            <select name="es_principal" id="es_principal" class="form-select" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" id="volverDatosBtn" class="btn btn-secondary me-2">Volver</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary">Registrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const cedulaInput = document.getElementById('cedula');
    const submitBtn = document.getElementById('submitBtn');
    const cedulaStatus = document.getElementById('cedulaStatus');
    const cedulaError = document.getElementById('cedulaError');
    const continuarBtn = document.getElementById('continuarDireccionBtn');
    const volverBtn = document.getElementById('volverDatosBtn');
    const datosPersonalesSection = document.getElementById('datosPersonalesSection');
    const direccionSection = document.getElementById('direccionSection');
    const correoInput = document.getElementById('correo');
    const correoStatus = document.getElementById('correoStatus');
    const correoError = document.getElementById('correoError');

    const requiredFields = ['cedula', 'nombre', 'apellido', 'correo', 'genero', 'telefono'];

    let datosAutocompletados = false; // Flag para saber si los campos fueron llenados automáticamente

    function limpiarCamposPersonales() {
        document.getElementById('nombre').value = '';
        document.getElementById('apellido').value = '';
        document.getElementById('correo').value = '';
        document.getElementById('telefono').value = '';
        document.getElementById('genero').value = 'M';
    }

    function disablePersonalFields() {
        ['nombre', 'apellido', 'correo', 'genero', 'telefono'].forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.disabled = true;
                field.classList.add('bg-light');
            }
        });
    }

    function enablePersonalFields() {
        ['nombre', 'apellido', 'correo', 'genero', 'telefono'].forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.disabled = false;
                field.classList.remove('bg-light');
            }
        });
    }

    function checkRequiredFields() {
        let allFilled = requiredFields.every(fieldId => {
            const field = document.getElementById(fieldId);
            return field.value.trim();
        });

        const cedulaValid = cedulaError.style.display === 'none';
        const correoValid = correoError.style.display === 'none';

        continuarBtn.disabled = !(allFilled && cedulaValid && correoValid);
    }

    requiredFields.forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('input', checkRequiredFields);
    });

    cedulaInput.addEventListener('input', function() {
        const cedula = cedulaInput.value;

        if (cedula.length === 0) {
            if (datosAutocompletados) {
                limpiarCamposPersonales();
                enablePersonalFields();
                datosAutocompletados = false;
            }
            cedulaError.style.display = 'none';
            cedulaStatus.style.display = 'none';
            submitBtn.disabled = false;
            checkRequiredFields();
            return;
        }

        if (cedula.length === 7 || cedula.length === 8) {
            fetch("{{ route('verificarCedula') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cedula: cedula })
            })
            .then(response => response.json())
            .then(data => {
                if (data.existe) {
                    cedulaError.style.display = 'inline';
                    cedulaStatus.style.display = 'none';
                    submitBtn.disabled = true;
                    continuarBtn.disabled = true;

                    document.getElementById('nombre').value = data.persona.nombre;
                    document.getElementById('apellido').value = data.persona.apellido;
                    document.getElementById('correo').value = data.persona.correo;
                    document.getElementById('telefono').value = data.persona.telefono;
                    document.getElementById('genero').value = data.persona.genero;

                    disablePersonalFields();
                    datosAutocompletados = true;
                } else {
                    // Solo limpiar si venía de datos autocompletados
                    if (datosAutocompletados) {
                        limpiarCamposPersonales();
                        enablePersonalFields();
                        datosAutocompletados = false;
                    }

                    cedulaError.style.display = 'none';
                    cedulaStatus.style.display = 'inline';
                    submitBtn.disabled = false;
                }

                checkRequiredFields();
            })
            .catch(error => {
                console.error('Error al verificar la cédula:', error);
                cedulaError.textContent = 'Error al verificar la cédula';
            });
        } else {
            cedulaError.style.display = 'none';
            cedulaStatus.style.display = 'none';
            submitBtn.disabled = false;
            checkRequiredFields();
        }
    });

    correoInput.addEventListener('input', function() {
        const correo = correoInput.value.trim();

        if (correo.length === 0) {
            correoError.style.display = 'none';
            correoStatus.style.display = 'none';
            submitBtn.disabled = false;
            checkRequiredFields();
            return;
        }

        fetch("{{ route('verificarCorreo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ correo: correo })
        })
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                correoError.style.display = 'inline';
                correoStatus.style.display = 'none';
                submitBtn.disabled = true;
                continuarBtn.disabled = true;
            } else {
                correoError.style.display = 'none';
                correoStatus.style.display = 'inline';
                submitBtn.disabled = false;
                checkRequiredFields();
            }
        })
        .catch(error => {
            console.error('Error al verificar el correo:', error);
            correoError.textContent = 'Error al verificar el correo';
        });
    });

    continuarBtn.addEventListener('click', function() {
        datosPersonalesSection.style.display = 'none';
        direccionSection.style.display = 'block';
    });

    volverBtn.addEventListener('click', function() {
        direccionSection.style.display = 'none';
        datosPersonalesSection.style.display = 'block';
    });
});
</script>



<script>
    document.getElementById('registroPersonaForm').addEventListener('submit', async function(event) {
        event.preventDefault();
    
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
    
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            const feedback = el.nextElementSibling;
            if (feedback) feedback.textContent = '';
        });
    
        submitBtn.disabled = true;
        submitBtn.innerHTML = ` 
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Procesando...
        `;
    
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });
    
            let data;
            try {
                data = await response.json();
            } catch (jsonError) {
                console.error('Error al analizar JSON:', jsonError);
                await Swal.fire({
                    icon: 'error',
                    title: 'Error Inesperado',
                    text: 'Ocurrió un error al procesar la respuesta del servidor.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
    
            if (!response.ok) {
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, messages]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        const errorElement = input?.nextElementSibling;
    
                        if (input && errorElement) {
                            input.classList.add('is-invalid');
                            errorElement.textContent = messages[0];
                        }
                    });
    
                    let errorMessage = data.message || 'Por favor corrige los siguientes errores:';
                    errorMessage += '<ul>';
                    for (const field in data.errors) {
                        errorMessage += `<li>${data.errors[field][0]}</li>`;
                    }
                    errorMessage += '</ul>';
    
                    await Swal.fire({
                        icon: 'warning',
                        title: data.title || 'Error de validación',
                        html: errorMessage,
                        confirmButtonText: 'Entendido'
                    });
    
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.focus();
                    }
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: data.title || 'Error del Servidor',
                        text: data.message || 'Ocurrió un error al procesar la solicitud.',
                        confirmButtonText: 'Entendido'
                    });
                }
            } else {
                await Swal.fire({
                    icon: 'success',
                    title: data.title || '¡Registro exitoso!',
                    text: data.message,
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = data.redirect_url || "{{ route('personas.index') }}";
                });
            }
    
        } catch (error) {
            console.error('Error de Fetch:', error);
            await Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo completar la operación. Por favor verifica tu conexión e intenta nuevamente.',
                confirmButtonText: 'Entendido'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Registrar';
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const continuarBtn = document.getElementById('continuarDireccionBtn');
        const volverBtn = document.getElementById('volverDatosBtn');
        const datosPersonalesSection = document.getElementById('datosPersonalesSection');
        const direccionSection = document.getElementById('direccionSection');
        const progressBar = document.getElementById('stepProgressBar');

        const camposRequeridos = [
            'cedula', 'nombre', 'apellido', 'correo', 'genero', 'telefono'
        ];

        function validarDatosPersonalesCompletos() {
            return camposRequeridos.every(id => {
                const campo = document.getElementById(id);
                return campo && campo.value.trim() !== '';
            });
        }

        function actualizarBarraPaso(paso) {
            if (paso === 1) {
                progressBar.style.width = '50%';
                progressBar.textContent = 'Paso 1 de 2';
            } else if (paso === 2) {
                progressBar.style.width = '100%';
                progressBar.textContent = 'Paso 2 de 2';
            }
        }

        camposRequeridos.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', () => {
                    continuarBtn.disabled = !validarDatosPersonalesCompletos();
                });
            }
        });

        continuarBtn.addEventListener('click', function () {
            datosPersonalesSection.style.display = 'none';
            direccionSection.style.display = 'block';
            actualizarBarraPaso(2);
        });

        volverBtn.addEventListener('click', function () {
            direccionSection.style.display = 'none';
            datosPersonalesSection.style.display = 'block';
            actualizarBarraPaso(1);
        });

        actualizarBarraPaso(1); // Inicializa en Paso 1
    });
</script>
@endsection