@extends('layouts.registrar')

@section('content')
<style>
    /* Estilos mejorados */
    .registration-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .form-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .form-header h2 {
        color: #2c3e50;
        font-weight: 700;
    }
    
    .form-step {
        display: none;
        animation: fadeIn 0.5s ease;
    }
    
    .form-step.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .progress-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }
    
    .progress-steps:before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e0e0e0;
        z-index: 1;
    }
    
    .step {
        text-align: center;
        position: relative;
        z-index: 2;
    }
    
    .step-number {
        width: 30px;
        height: 30px;
        line-height: 30px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #777;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    
    .step.active .step-number {
        background: #3498db;
        color: white;
    }
    
    .step-label {
        font-size: 0.9rem;
        color: #777;
    }
    
    .step.active .step-label {
        color: #3498db;
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-control {
        border-radius: 5px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    .password-container {
        position: relative;
    }
    
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #777;
    }
    
    .btn-action {
        padding: 10px 25px;
        border-radius: 5px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-next {
        background-color: #3498db;
        border-color: #3498db;
    }
    
    .btn-next:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }
    
    .btn-prev {
        background-color: #95a5a6;
        border-color: #95a5a6;
    }
    
    .btn-prev:hover {
        background-color: #7f8c8d;
    }
    
    .btn-submit {
        background-color: #2ecc71;
        border-color: #2ecc71;
    }
    
    .btn-submit:hover {
        background-color: #27ae60;
    }
    
    .question-group {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 1px solid #eee;
    }
    
    .error-message {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 0.3rem;
        display: none;
    }
    
    .error-message.active {
        display: block;
    }
    
    .input-error {
        border-color: #e74c3c !important;
    }
    
    .toast {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        z-index: 1000;
        display: none;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from { top: -50px; opacity: 0; }
        to { top: 20px; opacity: 1; }
    }
    
    .toast-success { background-color: #27ae60; }
    .toast-error { background-color: #e74c3c; }
</style>

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
            <div class="form-group">
                <label for="cedula">Cédula</label>
                <input type="text" id="cedula" name="cedula" class="form-control" 
                       placeholder="Ingrese su cédula" value="{{ old('cedula') }}" 
                       required onblur="buscarEmpleado()">
                <span id="cedula_error" class="error-message"></span>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" 
                           placeholder="Nombre" value="{{ old('nombre') }}" required readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" 
                           placeholder="Apellido" value="{{ old('apellido') }}" required readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nombre_usuario">Nombre de Usuario</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" 
                           placeholder="Nombre de usuario" value="{{ old('nombre_usuario') }}" required
                           oninput="limpiarNombreUsuario(this)">
                    <span id="nombre_usuario_error" class="error-message"></span>
                </div>
                <div class="col-md-6 form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="correo@ejemplo.com" value="{{ old('email') }}" required>
                    <span id="email_error" class="error-message"></span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Contraseña segura" required oninput="mostrarIconoOjo()">
                        <i id="toggleIcon" class="bi bi-eye-slash toggle-password" onclick="togglePassword()" style="display: none;"></i>
                        <span id="password_error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="genero">Género</label>
                    <input type="text" id="genero" name="genero" class="form-control" 
                           value="{{ old('genero') }}" required readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" 
                           value="{{ old('fecha_nacimiento') }}" required max="{{ date('Y-m-d') }}" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label for="altura">Altura (cm)</label>
                    <input type="text" id="altura" name="altura" class="form-control" 
                           placeholder="Altura" value="{{ old('altura') }}" 
                           required readonly>
                </div>
            </div>

            <input type="hidden" name="estado" value="activo">

            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-action btn-next" onclick="validarYAvanzar()">
                    Siguiente <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Paso 2: Preguntas de Seguridad -->
        <div class="form-step" id="step2">
            <h4 class="mb-4">Preguntas de Seguridad</h4>
            <p class="text-muted mb-4">Seleccione 3 preguntas de seguridad y proporcione sus respuestas.</p>
            
            @for ($i = 1; $i <= 3; $i++)
                <div class="question-group">
                    <div class="form-group">
                        <label for="pregunta_{{ $i }}">Pregunta {{ $i }}</label>
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
                        <label for="respuesta_{{ $i }}">Respuesta</label>
                        <input type="text" name="respuesta_{{ $i }}" id="respuesta_{{ $i }}" 
                               class="form-control" placeholder="Su respuesta" required disabled>
                    </div>
                </div>
            @endfor

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-action btn-prev" onclick="retrocederPaso()">
                    <i class="bi bi-arrow-left"></i> Anterior
                </button>
                <button type="submit" class="btn btn-action btn-submit">
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

    toastText.innerHTML = mensaje;
    toast.className = "toast " + (tipo === "success" ? "toast-success" : "toast-error");
    toast.style.display = "block";

    setTimeout(() => {
        toast.style.display = "none";
    }, 3000);
}

    // Validar altura
    function validarAltura() {
        let alturaInput = document.getElementById('altura');
        if (alturaInput.value < 0) {
            alturaInput.value = 0;
        }
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

    // Limpiar campos
    ['nombre', 'apellido', 'genero', 'fecha_nacimiento', 'altura'].forEach(id => {
        document.getElementById(id).value = '';
    });

    cedulaError.textContent = '';
    cedulaError.classList.remove('active');

    if (!cedula) return;

    cedulaError.textContent = 'Buscando...';
    cedulaError.style.color = '#3498db';
    cedulaError.classList.add('active');

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

        // Asignar valores a los campos
        document.getElementById('nombre').value = data.nombre || '';
        document.getElementById('apellido').value = data.apellido || '';
        document.getElementById('genero').value = data.genero || '';
        document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento || '';
        document.getElementById('altura').value = data.altura || ''; // Mantener el formato original de la altura

        cedulaError.textContent = '';
        cedulaError.classList.remove('active');
    })
    .catch(error => {
        console.error("Error:", error);
        cedulaError.textContent = error.message;
        cedulaError.style.color = '#e74c3c';
        cedulaError.classList.add('active');

        ['nombre', 'apellido', 'genero', 'fecha_nacimiento', 'altura'].forEach(id => {
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

        // Validar cédula
        const cedula = document.getElementById('cedula').value.trim();
        const nombre = document.getElementById('nombre').value.trim();
        if (!nombre && cedula) {
            mostrarNotificacion("Debe buscar y cargar los datos del empleado primero", "error");
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
</script>
@endsection