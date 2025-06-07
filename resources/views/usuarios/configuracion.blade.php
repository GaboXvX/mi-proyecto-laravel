@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col">
        <div class="col-md-4 w-100">
            <!-- Perfil del usuario -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Perfil del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <table class="profile-table table table-bordered">
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $empleadoAutorizado->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Apellido:</th>
                            <td>{{ $empleadoAutorizado->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Cédula:</th>
                            <td>{{ $empleadoAutorizado->cedula }}</td>
                        </tr>
                        <tr>
                            <th>Género:</th>
                            <td>{{ $empleadoAutorizado->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $empleadoAutorizado->telefono }}</td>
                        </tr>
                        <tr>
                            <th>Cargo:</th>
                            <td>{{ $cargo->nombre_cargo }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8 w-100">
            <!-- Configuración de seguridad -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Configuración de Seguridad
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Formulario principal de configuración -->
                    <form method="POST" action="{{ route('usuarios.cambiar', $usuario->id_usuario) }}" id="configuracionForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="inputCorreo" class="form-label">
                                        <i class="bi bi-envelope me-2"></i>
                                        Correo Electrónico
                                    </label>
                                    <input type="email" name="email" value="{{ $usuario->email }}" 
                                           class="form-control form-control-sm" id="inputCorreo"
                                           placeholder="Ingrese su correo electrónico" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="inputUsuario" class="form-label">
                                        <i class="bi bi-person-badge me-2"></i>
                                        Nombre de Usuario
                                    </label>
                                    <input type="text" name="nombre_usuario" value="{{ $usuario->nombre_usuario }}" 
                                           class="form-control form-control-sm" id="inputUsuario"
                                           placeholder="Ingrese su nombre de usuario" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="inputContraseña" class="form-label">
                                        <i class="bi bi-lock me-2"></i>
                                        Nueva Contraseña
                                    </label>
                                    <input type="password" name="contraseña" 
                                           class="form-control form-control-sm" id="inputContraseña"
                                           placeholder="Ingrese su nueva contraseña" maxlength="18" oninput="validarPassword()">
                                    <small class="form-text text-muted">Dejar en blanco para no cambiar</small>
                                </div>

                                <!-- Lista de requisitos -->
                                <ul class="list-unstyled mt-2 small" id="passwordRequisitos">
                                    <li id="req-length" class="text-danger">• Mínimo 8 caracteres</li>
                                    <li id="req-mayus" class="text-danger">• Al menos una letra mayúscula</li>
                                    <li id="req-minus" class="text-danger">• Al menos una letra minúscula</li>
                                    <li id="req-num" class="text-danger">• Al menos un número</li>
                                    <li id="req-special" class="text-danger">• Al menos un carácter especial</li>
                                </ul>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="inputConfirmarContraseña" class="form-label">
                                        <i class="bi bi-lock-fill me-2"></i>
                                        Confirmar Contraseña
                                    </label>
                                    <input type="password" name="contraseña_confirmation" 
                                           class="form-control form-control-sm" id="inputConfirmarContraseña"
                                           placeholder="Confirme su nueva contraseña" maxlength="18">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-success px-4" id="submitButton">
                                <i class="bi bi-save me-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>

                    <!-- Sección de Preguntas de Seguridad -->
                    <div class="security-questions mt-4">
                        <h5 class="mb-4">
                            <i class="bi bi-shield-lock me-2"></i>
                            Preguntas de Seguridad
                        </h5>
                            
                        @if($preguntasUsuario->count() > 0)
                            @foreach($preguntasUsuario as $index => $respuesta)
                                <div class="question-group">
                                    <div class="mb-2">
                                        <label>Pregunta {{ $index + 1 }}:</label>
                                        <p class="fw-bold">{{ $respuesta->pregunta->pregunta }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <label>Respuesta actual:</label>
                                        <p>••••••••</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No has configurado preguntas de seguridad.
                            </div>
                        @endif

                        <!-- Botón para cambiar preguntas -->
                        <button type="button" class="btn btn-change-questions btn-primary" 
                                data-bs-toggle="modal" data-bs-target="#changeQuestionsModal">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Cambiar Preguntas de Seguridad
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar preguntas de seguridad -->
<div class="modal fade" id="changeQuestionsModal" tabindex="-1" aria-labelledby="changeQuestionsModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="changeQuestionsModalLabel">
                    <i class="bi bi-shield-lock me-2"></i>
                    Cambiar Preguntas de Seguridad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('usuarios.cambiar-preguntas', $usuario->id_usuario) }}" method="POST" id="questionsForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Selecciona 3 nuevas preguntas de seguridad y proporciona sus respuestas.
                    </div>
                    
                    @for($i = 1; $i <= 3; $i++)
                        <div class="question-group mb-4">
                            <div class="form-group mb-2">
                                <label for="pregunta_{{ $i }}" class="form-label">Pregunta {{ $i }}:</label>
                                <select class="form-select question-select" id="pregunta_{{ $i }}" name="pregunta_{{ $i }}" required
                                        onchange="updateQuestionOptions(this)">
                                    <option value="">Seleccione una pregunta</option>
                                    @foreach($preguntasDisponibles as $pregunta)
                                        <option value="{{ $pregunta->id_pregunta }}">{{ $pregunta->pregunta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="respuesta_{{ $i }}" class="form-label">Respuesta:</label>
                                <input type="text" class="form-control" id="respuesta_{{ $i }}" 
                                    name="respuesta_{{ $i }}" disabled required
                                    placeholder="Ingrese su respuesta">
                                <small id="respuestaFeedback_{{ $i }}" class="form-text text-danger"></small>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitQuestionsBtn">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .profile-table {
        width: 100%;
        margin-bottom: 1.5rem;
        border-radius: 10px;
        overflow: hidden;
    }

    .profile-table th {
        width: 35%;
        font-weight: 600;
        padding: 0.75rem;
        color: #24476c;
    }

    .profile-table td {
        padding: 0.75rem;
        color: #24476c;
    }

    .security-questions {
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .question-group {
        margin-bottom: 1.5rem;
        padding: 1rem 1.25rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }

    .question-group label {
        font-weight: 600;
        color: #495057;
    }

    .btn-change-questions {
        background-color: #0d6efd;
        border-color: #0d6efd;
        padding: 0.5rem 1.5rem;
        font-size: 0.95rem;
        transition: all 0.2s ease-in-out;
    }

    .btn-change-questions:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .modal-header {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .modal-content {
        border-radius: 0.75rem;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .form-label i {
        color: #0d6efd;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.1rem rgba(13, 110, 253, 0.25);
    }

    .btn-success {
        font-size: 1rem;
        padding: 0.5rem 2rem;
        border-radius: 0.5rem;
    }

    .alert-warning, .alert-info {
        border-radius: 0.5rem;
        font-size: 0.95rem;
    }

    .option-disabled {
        color: #adb5bd;
        background-color: #f1f1f1;
    }

    #passwordRequisitos li {
        transition: color 0.3s ease;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function validarPassword() {
    const input = document.getElementById("inputContraseña");
    const password = input.value;

    const reglas = {
        length: password.length >= 8,
        mayus: /[A-Z]/.test(password),
        minus: /[a-z]/.test(password),
        num: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };

    // Actualizar clases visuales
    document.getElementById("req-length").className = reglas.length ? "text-success" : "text-danger";
    document.getElementById("req-mayus").className = reglas.mayus ? "text-success" : "text-danger";
    document.getElementById("req-minus").className = reglas.minus ? "text-success" : "text-danger";
    document.getElementById("req-num").className = reglas.num ? "text-success" : "text-danger";
    document.getElementById("req-special").className = reglas.special ? "text-success" : "text-danger";

    const boton = document.getElementById("submitButton");

    // Si el campo está vacío, permitimos el envío
    if (password === "") {
        boton.disabled = false;
        return;
    }

    // Verifica si todos los requisitos se cumplen
    const valido = Object.values(reglas).every(v => v);
    boton.disabled = !valido;
}
</script>

<script>
    // Función para actualizar las opciones disponibles en los selects
    function updateQuestionOptions(selectedSelect) {
        const allSelects = document.querySelectorAll('.question-select');
        const selectedValues = Array.from(allSelects).map(select => select.value);
        
        // Habilitar todas las opciones primero
        allSelects.forEach(select => {
            Array.from(select.options).forEach(option => {
                option.disabled = false;
                option.classList.remove('option-disabled');
                if (option.value !== '' && selectedValues.includes(option.value) && option.value !== select.value) {
                    option.disabled = true;
                    option.classList.add('option-disabled');
                }
            });
        });
        
        // Habilitar/deshabilitar el campo de respuesta correspondiente
        const respuestaInput = document.getElementById(`respuesta_${selectedSelect.id.split('_')[1]}`);
        respuestaInput.disabled = selectedSelect.value === '';
        
        // Limpiar mensajes de error
        document.getElementById(`respuestaFeedback_${selectedSelect.id.split('_')[1]}`).textContent = '';
    }

    // Validación del formulario de preguntas de seguridad
    document.getElementById('questionsForm')?.addEventListener('submit', function(e) {
        let isValid = true;
        const allSelects = document.querySelectorAll('.question-select');
        const selectedValues = [];
        
        // Verificar que no haya preguntas duplicadas
        allSelects.forEach(select => {
            if (select.value && selectedValues.includes(select.value)) {
                isValid = false;
                const preguntaNum = select.id.split('_')[1];
                document.getElementById(`respuestaFeedback_${preguntaNum}`).textContent = 
                    'No puedes seleccionar la misma pregunta más de una vez';
            } else if (select.value) {
                selectedValues.push(select.value);
            }
            
            // Verificar que todas las respuestas tengan contenido
            const respuestaInput = document.getElementById(`respuesta_${select.id.split('_')[1]}`);
            if (select.value && !respuestaInput.value.trim()) {
                isValid = false;
                document.getElementById(`respuestaFeedback_${select.id.split('_')[1]}`).textContent = 
                    'Por favor ingresa una respuesta para esta pregunta';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                text: 'Por favor corrige los errores antes de enviar',
                confirmButtonColor: '#dc3545',
            });
        }
    });

    // Validación del formulario principal
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar la validación de los selects
        const initialSelects = document.querySelectorAll('.question-select');
        initialSelects.forEach(select => {
            updateQuestionOptions(select);
        });
        
        // Validación del formulario principal
        const correoInput = document.getElementById('inputCorreo');
        const usuarioInput = document.getElementById('inputUsuario');
        const passwordInput = document.getElementById('inputContraseña');
        const confirmPasswordInput = document.getElementById('inputConfirmarContraseña');
        const submitButton = document.getElementById('submitButton');

        if (correoInput && usuarioInput && passwordInput && submitButton) {
            const correoOriginal = correoInput.value.trim();
            const usuarioOriginal = usuarioInput.value.trim();

            function hayCambios() {
                return correoInput.value.trim() !== correoOriginal ||
                       usuarioInput.value.trim() !== usuarioOriginal ||
                       passwordInput.value.trim() !== '';
            }

            function actualizarBoton() {
                submitButton.disabled = !hayCambios();
            }

            correoInput.addEventListener('input', actualizarBoton);
            usuarioInput.addEventListener('input', actualizarBoton);
            passwordInput.addEventListener('input', actualizarBoton);
            
            // Validación inicial
            actualizarBoton();
        }

        // Mostrar mensajes de éxito/error después de redirección
        // Mostrar mensajes de éxito/error después de redirección
@if(session('success'))
    @if(session('password_changed'))
        Swal.fire({
            icon: 'success',
            title: '¡Contraseña cambiada!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#198754',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: true
        });
    @else
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#198754',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: true
        });
    @endif
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#dc3545',
        showConfirmButton: true
    });
@endif

@if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: 'Advertencia',
        text: '{{ session('warning') }}',
        confirmButtonColor: '#ffc107',
        showConfirmButton: true
    });
@endif

@if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Error en el formulario',
        html: `@foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach`,
        confirmButtonColor: '#dc3545',
        showConfirmButton: true
    });
@endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545',
                showConfirmButton: true
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: '{{ session('warning') }}',
                confirmButtonColor: '#ffc107',
                showConfirmButton: true
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                html: `@foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach`,
                confirmButtonColor: '#dc3545',
                showConfirmButton: true
            });
        @endif
    });
</script>
@endsection