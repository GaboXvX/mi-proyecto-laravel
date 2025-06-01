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
                <input type="text" id="cedula" name="cedula" class="form-control" maxlength="8" placeholder="Ingrese su cédula" value="{{ old('cedula') }}" required onblur="buscarEmpleado()">
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
                
                <input type="text" name="cargo" id="cargo" class="form-control" placeholder="Cargo" value="{{ old('cargo') }}" required readonly>
            </div>
            </div>

           <div class="row">
                <div class="form-group">
                    <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" placeholder="Nombre de usuario" value="{{ old('nombre_usuario') }}" required oninput="limpiarNombreUsuario(this)">
                    <span id="nombre_usuario_error" class="error-message"></span>

                    <input type="email" id="email" name="email" class="form-control" placeholder="correo electrónico" value="{{ old('email') }}" required>
                    <span id="email_error" class="error-message"></span>
                </div>
            </div>

            <div class="password-container" style="position: relative; width: 100%;">
                <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña segura" required oninput="mostrarIconoOjo(); checkPasswordRequirements(this.value);" style="padding-right: 38px;">
                <div id="error" class="mensaje-error"></div>

                <i id="toggleIcon" class="bi bi-eye-slash toggle-password" onclick="togglePassword()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; display: none; font-size: 1.2em;"></i>

                <div class="password-requirements">
                    <ul id="passwordRules" style="margin-bottom: 0;">
                        <li id="ruleUpper">Debe contener al menos una letra mayúscula.</li>
                        <li id="ruleLower">Debe contener al menos una letra minúscula.</li>
                        <li id="ruleNumber">Debe contener al menos un número.</li>
                        <li id="ruleSpecial">Debe contener al menos un carácter especial.</li>
                        <li id="ruleLength">Debe tener mínimo 8 caracteres.</li>
                    </ul>
                </div>
            </div>

            
            <input type="hidden" name="estado" value="activo">

            <button type="button" class="btn-next" onclick="validarYAvanzar()">
                Siguiente
            </button>
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

<script src="{{ asset('js/home.js') }}"></script>
<script>
    // Función para mostrar notificación
   function mostrarNotificacion(mensaje, tipo) {
    const toast = document.getElementById("toastMessage");
    const toastText = document.getElementById("toastText");

    toastText.innerHTML = mensaje;
    toast.className = "toast " + (tipo === "success" ? "toast-success" : "toast-error");
    toast.style.display = "block";

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

    // Limpiar campos
    ['nombre', 'apellido', 'genero', 'cargo'].forEach(id => {
        document.getElementById(id).value = '';
    });

    cedulaError.textContent = '';
    cedulaError.classList.remove('active');
    btnSiguiente.disabled = false;

    if (!cedula) return;

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
            ['nombre', 'apellido', 'genero', 'cargo'].forEach(id => {
                document.getElementById(id).value = '';
            });
            return;
        }
        // Asignar valores a los campos
        document.getElementById('nombre').value = data.nombre || '';
        document.getElementById('apellido').value = data.apellido || '';
        document.getElementById('genero').value = data.genero || '';
        document.getElementById('cargo').value = data.cargo || '';

        cedulaError.textContent = '';
        cedulaError.classList.remove('active');
        btnSiguiente.disabled = false;
    })
    .catch(error => {
        console.error("Error:", error);
        cedulaError.textContent = error.message;
        cedulaError.style.color = '#e74c3c';
        cedulaError.classList.add('active');
        btnSiguiente.disabled = true;
        ['nombre', 'apellido', 'genero', 'cargo'].forEach(id => {
            document.getElementById(id).value = '';
        });
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
        // Validar campos obligatorios
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
        const cedula = document.getElementById('cedula').value.trim();
        const nombre = document.getElementById('nombre').value.trim();
        if (!nombre && cedula) {
            mostrarNotificacion("Debe buscar y cargar los datos del empleado primero", "error");
            valido = false;
        }
        // Validación específica para la contraseña
        const password = document.getElementById('password').value;
        const passwordError = document.getElementById('password_error');
        const btnSiguiente = document.querySelector('.btn-next');
        if (password) {
            // Expresión regular para validar contraseña (mayúscula, minúscula, número, especial, 8+)
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(password)) {
                passwordError.textContent = 'La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y caracteres especiales';
                passwordError.classList.add('active');
                document.getElementById('password').classList.add('input-error');
                mostrarNotificacion("La contraseña no cumple con los requisitos de seguridad", "error");
                btnSiguiente.disabled = true;
                return false;
            } else {
                passwordError.textContent = '';
                passwordError.classList.remove('active');
                document.getElementById('password').classList.remove('input-error');
                btnSiguiente.disabled = false;
            }
        }
        // Solo avanzar si todo es válido
        if (valido) {
            avanzarPaso();
        } else {
            mostrarNotificacion("Por favor complete todos los campos requeridos correctamente", "error");
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

function checkPasswordRequirements(password) {
    const rules = {
        upper: /[A-Z]/.test(password),
        lower: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[@$!%*?&]/.test(password),
        length: password.length >= 8
    };
    Object.keys(rules).forEach(rule => {
        const element = document.getElementById(`rule${rule.charAt(0).toUpperCase() + rule.slice(1)}`);
        if (element) {
            element.classList.toggle('valid', rules[rule]);
        }
    });
    return Object.values(rules).every(Boolean);
}
</script>

<style>
.password-container {
    position: relative;
    width: 100%;
}
.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    font-size: 1.2em;
    z-index: 2;
}
.toggle-password:hover {
    color: #333;
}
.mensaje-error {
    color: #e74c3c;
    font-size: 0.8rem;
    margin-top: 5px;
}
.password-requirements {
    margin-top: 5px;
    font-size: 0.8rem;
    color: #666;
}
.password-requirements ul {
    list-style-type: none;
    padding-left: 0;
    margin: 5px 0 0 0;
}
.password-requirements li {
    position: relative;
    padding-left: 20px;
}
.password-requirements li:before {
    content: "✗";
    position: absolute;
    left: 0;
    color: #e74c3c;
}
.password-requirements li.valid:before {
    content: "✓";
    color: #2ecc71;
}
</style>
@endsection