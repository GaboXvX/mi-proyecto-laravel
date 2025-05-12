<style>
    .security-questions {
        border-radius: 5px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .question-group {
        margin-bottom: 1.5rem;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 5px;
    }
    
    .question-group label {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .btn-change-questions {
        background-color: #3498db;
        border-color: #3498db;
        margin-bottom: 1rem;
    }
    
    .btn-change-questions:hover {
        background-color: #2980b9;
        border-color: #2980b9;
    }
</style>

<div class="table-container">

<!-- Formulario principal de configuración -->
            <form action="{{ route('usuarios.cambiar', $usuario->id_usuario) }}" method="POST" id="configuracionForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="inputCorreo" class="form-label">
                                <i class="bi bi-envelope me-2"></i>
                                Correo Electrónico
                            </label>
                            <input type="email" class="form-control form-control-sm" id="inputCorreo"
                                name="email" value="{{ old('email', $usuario->email) }}"
                                placeholder="Ingrese su correo electrónico" required>
                            <small id="correoFeedback" class="form-text"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="inputUsuario" class="form-label">
                                <i class="bi bi-person-badge me-2"></i>
                                Nombre de Usuario
                            </label>
                            <input type="text" class="form-control form-control-sm" id="inputUsuario"
                                name="nombre_usuario"
                                value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}"
                                placeholder="Ingrese su nombre de usuario" required>
                            <small id="usuarioFeedback" class="form-text"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="inputContraseña" class="form-label">
                                <i class="bi bi-lock me-2"></i>
                                Nueva Contraseña
                            </label>
                            <input type="password" class="form-control form-control-sm" id="inputContraseña"
                                name="contraseña" placeholder="Ingrese su nueva contraseña">
                            <small class="form-text text-muted">Dejar en blanco para no cambiar</small>
                        </div>
                    </div>
                </div>
                    
                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-success px-4" id="submitButton" disabled>
                        <i class="bi bi-save me-2"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>

    <!-- Sección de Preguntas de Seguridad -->
    <div class="security-questions">
                <h4 class="mb-4">
                    <i class="bi bi-shield-lock me-2"></i>
                    Preguntas de Seguridad
                </h4>
                    
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
                <form action="{{ route('usuarios.cambiar-preguntas', auth()->user()->id_usuario) }}" method="POST">
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
                                    <select class="form-select" id="pregunta_{{ $i }}" name="pregunta_{{ $i }}" required
                                            onchange="habilitarRespuesta(this)">
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
                                </div>
                            </div>
                        @endfor
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
<script>
// 1. DEFINIR PRIMERO LA FUNCIÓN QUE GENERABA EL ERROR
function habilitarRespuesta(selectElement) {
    const preguntaNum = selectElement.id.split('_')[1];
    const respuestaInput = document.getElementById(`respuesta_${preguntaNum}`);
    respuestaInput.disabled = selectElement.value === '';
}

// 2. LUEGO EL RESTO DEL CÓDIGO
document.addEventListener('DOMContentLoaded', function () {
    // Manejo del modal para accesibilidad
    const changeQuestionsModal = document.getElementById('changeQuestionsModal');
    
    changeQuestionsModal.addEventListener('shown.bs.modal', function() {
        this.removeAttribute('aria-hidden');
        const firstSelect = this.querySelector('select');
        if (firstSelect) {
            firstSelect.focus();
        }
    });
    
    changeQuestionsModal.addEventListener('hidden.bs.modal', function() {
        this.setAttribute('aria-hidden', 'true');
    });

    // Validación del formulario principal
    const correoInput = document.getElementById('inputCorreo');
    const usuarioInput = document.getElementById('inputUsuario');
    const passwordInput = document.getElementById('inputContraseña');
    const submitButton = document.getElementById('submitButton');
    const correoFeedback = document.getElementById('correoFeedback');
    const usuarioFeedback = document.getElementById('usuarioFeedback');

    let correoOriginal = correoInput.value;
    let usuarioOriginal = usuarioInput.value;

    let correoDisponible = true;
    let usuarioDisponible = true;

    function hayCambios() {
        const correoCambio = correoInput.value !== correoOriginal;
        const usuarioCambio = usuarioInput.value !== usuarioOriginal;
        const passwordIngresado = passwordInput.value.trim() !== '';
        return correoCambio || usuarioCambio || passwordIngresado;
    }

    function validarFormulario() {
        const cambios = hayCambios();
        const validacionesOk = correoDisponible && usuarioDisponible;
        submitButton.disabled = !(cambios && validacionesOk);
    }

    correoInput.addEventListener('input', function () {
        const correo = this.value;

        if (correo.trim() === '') {
            correoFeedback.textContent = '';
            correoDisponible = true;
            validarFormulario();
            return;
        }

        fetch(`/validar-correo/${correo}?excluir={{ $usuario->id_usuario }}`)
            .then(response => response.json())
            .then(data => {
                if (data.disponible) {
                    correoFeedback.innerHTML = '<i class="bi bi-check-circle-fill text-success me-2"></i>¡Correo disponible!';
                    correoDisponible = true;
                } else {
                    correoFeedback.innerHTML = '<i class="bi bi-x-circle-fill text-danger me-2"></i>El correo ya está en uso.';
                    correoDisponible = false;
                }
                validarFormulario();
            })
            .catch(() => {
                correoFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Error al validar.';
                correoDisponible = false;
                validarFormulario();
            });
    });

    usuarioInput.addEventListener('input', function () {
        const nombreUsuario = this.value;

        if (nombreUsuario.trim() === '') {
            usuarioFeedback.textContent = '';
            usuarioDisponible = true;
            validarFormulario();
            return;
        }

        fetch(`/validar-usuario/${nombreUsuario}?excluir={{ $usuario->id_usuario }}`)
            .then(response => response.json())
            .then(data => {
                if (data.disponible) {
                    usuarioFeedback.innerHTML = '<i class="bi bi-check-circle-fill text-success me-2"></i>¡Usuario disponible!';
                    usuarioDisponible = true;
                } else {
                    usuarioFeedback.innerHTML = '<i class="bi bi-x-circle-fill text-danger me-2"></i>El usuario ya está en uso.';
                    usuarioDisponible = false;
                }
                validarFormulario();
            })
            .catch(() => {
                usuarioFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Error al validar.';
                usuarioDisponible = false;
                validarFormulario();
            });
    });

    passwordInput.addEventListener('input', function () {
        validarFormulario();
    });
});
</script>