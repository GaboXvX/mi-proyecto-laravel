@extends('layouts.registrar')

@section('content')
<div class="registration-container">
    <div class="form-header">
        <h2>Registro de Usuario</h2>
        <p>Complete su información para registrarse en el sistema</p>
    </div>
    
    <div id="toastMessage" class="toast">
        <span id="toastText"></span>
    </div>

    <!-- Barra de progreso -->
    <div class="progress-steps">
        <div class="step active" id="step1-indicator">
            <div class="step-number">1</div>
            <div class="step-label">Datos Básicos</div>
        </div>
        <div class="step" id="step2-indicator">
            <div class="step-number">2</div>
            <div class="step-label">Preguntas de Seguridad</div>
        </div>
    </div>

    <form id="registroForm" action="{{ route('peticiones.store') }}" method="POST">
        @csrf
        
        <!-- Paso 1: Datos Básicos -->
        <div class="form-step active" id="step1">
            <div class="password-container">
                <input type="text" id="cedula" name="cedula" class="form-control" maxlength="10" placeholder="Ingrese su cédula" value="{{ old('cedula') }}" required onblur="buscarEmpleado()">
                <input type="text" id="cedula" name="cedula" class="form-control" maxlength="10" placeholder="Ingrese su cédula" value="{{ old('cedula') }}" required onblur="buscarEmpleado()">
                <span id="cedula_error" class="error-message"></span>
            </div>

            <div class="row">
                <div class="form-group">
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" value="{{ old('nombre') }}" required readonly>
                    <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Apellido" value="{{ old('apellido') }}" required readonly>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <input type="text" id="genero" name="genero" placeholder="Género" class="form-control" value="{{ old('genero') }}" required readonly>
                    <input type="text" id="nacionalidad" name="nacionalidad" class="form-control" placeholder="Nacionalidad" value="{{ old('nacionalidad') }}" required readonly>
                </div>
            </div>

            <div class="password-container">
                <input type="text" name="cargo" id="cargo" class="form-control" placeholder="Cargo" value="{{ old('cargo') }}" required readonly>
            </div>

            <div class="row">
                <div class="form-group">
                    <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" placeholder="Nombre de usuario" value="{{ old('nombre_usuario') }}" required oninput="limpiarNombreUsuario(this)">
                    <span id="nombre_usuario_error" class="error-message"></span>

                    <input type="email" id="email" name="email" class="form-control" placeholder="Correo electrónico" value="{{ old('email') }}" required>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Correo electrónico" value="{{ old('email') }}" required>
                    <span id="email_error" class="error-message"></span>
                </div>
            </div>

            <div class="password-container">
                <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña segura" required oninput="mostrarIconoOjo()">
                <i id="toggleIcon" class="bi bi-eye-slash toggle-password" onclick="togglePassword()" style="display: none;"></i>
                <span id="password_error" class="error-message"></span>
            </div>
            
            <input type="hidden" name="estado" value="activo">

            <div class="password-rules" id="passwordRules">
                <p class="rule" id="rule-length">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                    </svg> 
                    Mínimo 8 caracteres
                </p>
                <p class="rule" id="rule-uppercase">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                    </svg>
                    Al menos una letra mayúscula
                </p>
                <p class="rule" id="rule-lowercase">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                    </svg>
                    Al menos una letra minúscula
                </p>
                <p class="rule" id="rule-number">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                    </svg>
                    Al menos un número
                </p>
                <p class="rule" id="rule-special">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                    </svg>
                    Al menos un carácter especial
                </p>
            </div>

            <div class="btn-solo">
                <button type="button" class="btn-next" id="btnSiguiente" onclick="validarYAvanzar()">
                    Siguiente
                </button>
            </div>
        </div>

        <!-- Paso 2: Preguntas de Seguridad -->
        <div class="form-step" id="step2">
            <p>Seleccione 3 preguntas de seguridad y proporcione sus respuestas.</p>
            
            @for ($i = 1; $i <= 3; $i++)
                <div class="question-group">
                    <div class="form-group">
                        <label for="pregunta_{{ $i }}" class="step-label">Pregunta {{ $i }}</label>
                        <select name="pregunta_{{ $i }}" id="pregunta_{{ $i }}" class="form-control" 
                                required onchange="actualizarSelects()">
                            <option value="">Seleccione una pregunta</option>
                            @foreach($preguntas as $pregunta)
                                <option value="{{ $pregunta->id_pregunta }}" {{ old('pregunta_' . $i) == $pregunta->id_pregunta ? 'selected' : '' }}>
                                    {{ $pregunta->pregunta }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="respuesta_{{ $i }}" class="step-label">Respuesta</label>
                        <input type="text" name="respuesta_{{ $i }}" id="respuesta_{{ $i }}" class="form-control" placeholder="Su respuesta" required disabled>
                    </div>
                </div>
            @endfor

            <div class="btn-cont">
                <button type="button" class="btn-prev" onclick="retrocederPaso()">
                    <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-circle"></i> Registrar Usuario
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Función para mostrar notificación
    function mostrarNotificacion(mensaje, tipo) {
        const toast = document.getElementById("toastMessage");
        const toastText = document.getElementById("toastText");
    function mostrarNotificacion(mensaje, tipo) {
        const toast = document.getElementById("toastMessage");
        const toastText = document.getElementById("toastText");

        toastText.innerHTML = mensaje;
        toast.className = "toast " + (tipo === "success" ? "toast-success" : "toast-error");
        toast.style.display = "block";
        toastText.innerHTML = mensaje;
        toast.className = "toast " + (tipo === "success" ? "toast-success" : "toast-error");
        toast.style.display = "block";

        setTimeout(() => {
            toast.style.display = "none";
        }, 3000);
    }
        setTimeout(() => {
            toast.style.display = "none";
        }, 3000);
    }

    // Mostrar/ocultar contraseña
    function mostrarIconoOjo() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        toggleIcon.style.display = passwordInput.value ? 'block' : 'none';
    }

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }

    // Limpiar nombre de usuario
    function limpiarNombreUsuario(input) {
        input.value = input.value.replace(/[^a-zA-Z0-9_]/g, '');
    }

    // Buscar empleado por cédula
    function buscarEmpleado() {
        const cedula = document.getElementById('cedula').value.trim();
        const cedulaError = document.getElementById('cedula_error');
        const btnSiguiente = document.querySelector('.btn-next');
        const cedula = document.getElementById('cedula').value.trim();
        const cedulaError = document.getElementById('cedula_error');
        const btnSiguiente = document.querySelector('.btn-next');

        // Limpiar campos
        ['nombre', 'apellido', 'genero', 'nacionalidad', 'cargo'].forEach(id => {
            document.getElementById(id).value = '';
        });
        // Limpiar campos
        ['nombre', 'apellido', 'genero', 'nacionalidad', 'cargo'].forEach(id => {
            document.getElementById(id).value = '';
        });

        cedulaError.textContent = '';
        cedulaError.classList.remove('active');
        btnSiguiente.disabled = false;
        cedulaError.textContent = '';
        cedulaError.classList.remove('active');
        btnSiguiente.disabled = false;

        if (!cedula) return;
        if (!cedula) return;

        cedulaError.textContent = 'Buscando...';
        cedulaError.style.color = '#3498db';
        cedulaError.classList.add('active');
        btnSiguiente.disabled = true;
        cedulaError.textContent = 'Buscando...';
        cedulaError.style.color = '#3498db';
        cedulaError.classList.add('active');
        btnSiguiente.disabled = true;

        fetch(`/buscar-empleado?cedula=${encodeURIComponent(cedula)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 403) {
                    return response.json().then(data => Promise.reject(data.error));
                }
                if (response.status === 404) throw new Error('Empleado no encontrado');
                throw new Error('Error en el servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            if (data.ya_registrado) {
                cedulaError.textContent = 'Este empleado ya tiene un usuario registrado.';
                cedulaError.style.color = '#e74c3c';
                cedulaError.classList.add('active');
                btnSiguiente.disabled = true;
                // Limpiar campos
                ['nombre', 'apellido', 'genero', 'nacionalidad', 'cargo'].forEach(id => {
                    document.getElementById(id).value = '';
                });
                return;
            }
            
            // Asignar valores a los campos
            document.getElementById('nombre').value = data.nombre || '';
            document.getElementById('apellido').value = data.apellido || '';
            document.getElementById('genero').value = data.genero || '';
            document.getElementById('nacionalidad').value = data.nacionalidad || '';
            document.getElementById('cargo').value = data.cargo || '';
        fetch(`/buscar-empleado?cedula=${encodeURIComponent(cedula)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 403) {
                    return response.json().then(data => Promise.reject(data.error));
                }
                if (response.status === 404) throw new Error('Empleado no encontrado');
                throw new Error('Error en el servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            if (data.ya_registrado) {
                cedulaError.textContent = 'Este empleado ya tiene un usuario registrado.';
                cedulaError.style.color = '#e74c3c';
                cedulaError.classList.add('active');
                btnSiguiente.disabled = true;
                // Limpiar campos
                ['nombre', 'apellido', 'genero', 'nacionalidad', 'cargo'].forEach(id => {
                    document.getElementById(id).value = '';
                });
                return;
            }
            
            // Asignar valores a los campos
            document.getElementById('nombre').value = data.nombre || '';
            document.getElementById('apellido').value = data.apellido || '';
            document.getElementById('genero').value = data.genero || '';
            document.getElementById('nacionalidad').value = data.nacionalidad || '';
            document.getElementById('cargo').value = data.cargo || '';

            cedulaError.textContent = '';
            cedulaError.classList.remove('active');
            btnSiguiente.disabled = false;
        })
       // En tu JavaScript
.catch(error => {
    console.error("Error:", error);
    if (error.tipo_error === 'inactivo') {
        // Mostrar datos del empleado inactivo
        document.getElementById('nombre').value = error.datos_empleado.nombre;
        document.getElementById('apellido').value = error.datos_empleado.apellido;
        document.getElementById('nacionalidad').value = error.datos_empleado.nacionalidad;
        document.getElementById('cargo').value = error.datos_empleado.cargo_anterior;
        
        // Deshabilitar todos los campos
        document.querySelectorAll('#step1 input:not([readonly])').forEach(input => {
            input.disabled = true;
        });
    }
    
    cedulaError.textContent = error.error;
    cedulaError.style.color = '#e74c3c';
    cedulaError.classList.add('active');
    btnSiguiente.disabled = true;
});
    }
            cedulaError.textContent = '';
            cedulaError.classList.remove('active');
            btnSiguiente.disabled = false;
        })
       // En tu JavaScript
.catch(error => {
    console.error("Error:", error);
    if (error.tipo_error === 'inactivo') {
        // Mostrar datos del empleado inactivo
        document.getElementById('nombre').value = error.datos_empleado.nombre;
        document.getElementById('apellido').value = error.datos_empleado.apellido;
        document.getElementById('nacionalidad').value = error.datos_empleado.nacionalidad;
        document.getElementById('cargo').value = error.datos_empleado.cargo_anterior;
        
        // Deshabilitar todos los campos
        document.querySelectorAll('#step1 input:not([readonly])').forEach(input => {
            input.disabled = true;
        });
    }
    
    cedulaError.textContent = error.error;
    cedulaError.style.color = '#e74c3c';
    cedulaError.classList.add('active');
    btnSiguiente.disabled = true;
});
    }

    // Manejo de selects de preguntas
    function actualizarSelects() {
        const selects = document.querySelectorAll('select[id^="pregunta_"]');
        let selectedValues = [];

        // Obtener valores seleccionados
        selects.forEach(select => {
            if (select.value) {
                selectedValues.push(select.value);
            }
        });

        // Actualizar cada select
        selects.forEach(select => {
            const respuestaInput = select.closest('.question-group').querySelector('input[type="text"]');
            respuestaInput.disabled = !select.value;

            // Habilitar/deshabilitar opciones
            select.querySelectorAll('option').forEach(option => {
                if (option.value) {
                    option.disabled = selectedValues.includes(option.value) && option.value !== select.value;
                }
            });
        });
    }

    // Navegación entre pasos
    function validarYAvanzar() {
        const camposRequeridos = ['cedula', 'nombre', 'apellido', 'nombre_usuario', 'email', 'password'];
        let valido = true;

        // Validar campos
        camposRequeridos.forEach(campo => {
            const elemento = document.getElementById(campo);
            const errorElement = document.getElementById(`${campo}_error`);
            
            if (!elemento.value.trim()) {
                if (errorElement) {
                    errorElement.textContent = 'Este campo es obligatorio';
                    errorElement.classList.add('active');
                }
                elemento.classList.add('input-error');
                valido = false;
            } else {
                if (errorElement) errorElement.classList.remove('active');
                elemento.classList.remove('input-error');
            }
        });

        // Validar cédula y datos del empleado
        // Validar cédula y datos del empleado
        const cedula = document.getElementById('cedula').value.trim();
        const nombre = document.getElementById('nombre').value.trim();
        const cedulaError = document.getElementById('cedula_error');
        
        const cedulaError = document.getElementById('cedula_error');
        
        if (!nombre && cedula) {
            mostrarNotificacion("Debe buscar y cargar los datos del empleado primero", "error");
            valido = false;
        } else if (nombre && cedulaError.classList.contains('active')) {
            mostrarNotificacion(cedulaError.textContent, "error");
            valido = false;
        } else if (nombre && cedulaError.classList.contains('active')) {
            mostrarNotificacion(cedulaError.textContent, "error");
            valido = false;
        }

        if (valido) {
            avanzarPaso();
        } else {
            mostrarNotificacion("Por favor complete todos los campos requeridos", "error");
        }
    }

    function avanzarPaso() {
        document.getElementById('step1').classList.remove('active');
        document.getElementById('step2').classList.add('active');
        document.getElementById('step1-indicator').classList.remove('active');
        document.getElementById('step2-indicator').classList.add('active');
    }

    function retrocederPaso() {
        document.getElementById('step2').classList.remove('active');
        document.getElementById('step1').classList.add('active');
        document.getElementById('step2-indicator').classList.remove('active');
        document.getElementById('step1-indicator').classList.add('active');
    }

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        actualizarSelects();
        
        // Limpiar errores al enfocar campos
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('focus', function() {
                this.classList.remove('input-error');
                const errorElement = document.getElementById(`${this.id}_error`);
                if (errorElement) errorElement.classList.remove('active');
            });
        });
    });

    // Envío del formulario
    document.getElementById('registroForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        event.preventDefault();

        // Limpiar mensajes de error previos
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.remove('active');
        });
        document.querySelectorAll('.form-control').forEach(el => {
            el.classList.remove('input-error');
        });
        // Limpiar mensajes de error previos
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.remove('active');
        });
        document.querySelectorAll('.form-control').forEach(el => {
            el.classList.remove('input-error');
        });

        // Mostrar loader
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Procesando...';
        // Mostrar loader
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Procesando...';

        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                }
            });
        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                }
            });

            const data = await response.json();
            const data = await response.json();

            // Manejar respuesta exitosa
            if (data.success) {
                mostrarNotificacion(data.message, "success");
                setTimeout(() => {
                    window.location.href = data.redirect || "{{ route('login') }}";
                }, 1500);
                return;
            }
            // Manejar respuesta exitosa
            if (data.success) {
                mostrarNotificacion(data.message, "success");
                setTimeout(() => {
                    window.location.href = data.redirect || "{{ route('login') }}";
                }, 1500);
                return;
            }

            // Manejar errores de validación
            if (response.status === 422 && data.errors) {
                for (const field in data.errors) {
                    const errorElement = document.getElementById(`${field}_error`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.add('active');
                        
                        const inputElement = document.getElementById(field);
                        if (inputElement) inputElement.classList.add('input-error');
                    }
                }
                mostrarNotificacion(data.message || "⚠️ Por favor corrige los errores en el formulario", "error");
                return;
            }
            // Manejar errores de validación
            if (response.status === 422 && data.errors) {
                for (const field in data.errors) {
                    const errorElement = document.getElementById(`${field}_error`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.add('active');
                        
                        const inputElement = document.getElementById(field);
                        if (inputElement) inputElement.classList.add('input-error');
                    }
                }
                mostrarNotificacion(data.message || "⚠️ Por favor corrige los errores en el formulario", "error");
                return;
            }

            // Manejar otros errores
            throw new Error(data.message || "Error al procesar la solicitud");
            // Manejar otros errores
            throw new Error(data.message || "Error al procesar la solicitud");

        } catch (error) {
            console.error("Error:", error);
            mostrarNotificacion(`❌ ${error.message}`, "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
        } catch (error) {
            console.error("Error:", error);
            mostrarNotificacion(`❌ ${error.message}`, "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
</script>
@endsection