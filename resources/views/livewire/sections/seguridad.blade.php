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

    .option-disabled {
        color: #6c757d;
        background-color: #e9ecef;
    }
</style>

<div class="table-container">
    <!-- Formulario principal de configuración -->
    <form wire:submit.prevent="saveChanges" id="configuracionForm">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="inputCorreo" class="form-label">
                        <i class="bi bi-envelope me-2"></i>
                        Correo Electrónico
                    </label>
                    <input type="email" wire:model.defer="email" class="form-control form-control-sm" id="inputCorreo"
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
                    <input type="text" wire:model.defer="nombre_usuario" class="form-control form-control-sm" id="inputUsuario"
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
                    <input type="password" wire:model.defer="contraseña" class="form-control form-control-sm" id="inputContraseña"
                        placeholder="Ingrese su nueva contraseña" maxlength="18">
                    <small class="form-text text-muted">Dejar en blanco para no cambiar</small>
                    <small id="passwordFeedback" class="form-text"></small>
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
                <form action="{{ route('usuarios.cambiar-preguntas', auth()->user()->id_usuario) }}" method="POST" id="questionsForm">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Notificaciones de Livewire
    Livewire.on('guardadoExitoso', () => {
        Swal.fire({
            icon: 'success',
            title: '¡Cambios guardados!',
            text: 'Tu información se actualizó correctamente.',
            confirmButtonColor: '#198754',
        });
    });

    Livewire.on('passwordChanged', (success) => {
        if (success) {
            Swal.fire({
                icon: 'success',
                title: '¡Contraseña actualizada!',
                text: 'Tu contraseña se ha cambiado correctamente.',
                confirmButtonColor: '#198754',
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cambiar la contraseña.',
                confirmButtonColor: '#dc3545',
            });
        }
    });

    Livewire.on('questionsChanged', (success) => {
        if (success) {
            Swal.fire({
                icon: 'success',
                title: '¡Preguntas actualizadas!',
                text: 'Tus preguntas de seguridad se han cambiado correctamente.',
                confirmButtonColor: '#198754',
            });
            // Cerrar el modal después de guardar
            bootstrap.Modal.getInstance(document.getElementById('changeQuestionsModal')).hide();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cambiar las preguntas de seguridad.',
                confirmButtonColor: '#dc3545',
            });
        }
    });

    // Inicializar los listeners cuando el DOM esté listo
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
    });

    // Volver a ejecutar después de cada actualización de Livewire
    Livewire.hook('message.processed', () => {
        const initialSelects = document.querySelectorAll('.question-select');
        initialSelects.forEach(select => {
            updateQuestionOptions(select);
        });
    });
</script>