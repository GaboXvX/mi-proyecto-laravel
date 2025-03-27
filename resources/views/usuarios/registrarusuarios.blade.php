@extends('layouts.registrar')

@section('content')

<style>
    label {
        color: black;
    }

    h2, h3, h4, label {
        color: black; /* Apply red color to headers and labels */
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 10px;
        border-radius: 5px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 10px;
        border-radius: 5px;
    }

    .input-error {
        border-color: red;
    }

    .input-success {
        border-color: green;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }
</style>
    <div class="container">
        <div class="content">
            <h2>Bienvenido al sistema de MinAguas!</h2>
            <p>Tu plataforma confiable para el manejo de recursos hídricos.</p>
        </div>
        <hr>
        <div class="form-content">
            <h3>Registrarse</h3>

            <!-- Mensajes de éxito o error -->
          

            <!-- Formulario de registro -->
            <form action="{{ route('peticiones.store') }}" method="POST">
                @csrf
                <div class="row">
                    <select name="rol" id="rol" required>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id_rol }}" {{ old('rol') == $rol->id_rol ? 'selected' : '' }}>
                                {{ $rol->rol }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required>
                    <input type="text" id="apellido" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}" required>
                </div>
                <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Nombre de Usuario" value="{{ old('nombre_usuario') }}" required>
                <span id="nombre_usuario_error" class="error-message"></span>
                <input type="text" id="cedula" name="cedula" placeholder="Cédula" value="{{ old('cedula') }}" required>
                <span id="cedula_error" class="error-message"></span>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" value="{{ old('email') }}" required>
                <span id="email_error" class="error-message"></span>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>

                <!-- Nuevos campos adicionales -->
                <div class="row">
                    <label for="genero">Género:</label>
                    <select name="genero" id="genero" required>
                        <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
                <div class="row">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required max="{{ date('Y-m-d') }}">
                </div>
                <div class="row">
                    <label for="altura">Altura:</label>
                    <input type="number" id="altura" name="altura" placeholder="Altura" value="{{ old('altura') }}" required min="0" step="0.01" oninput="validarAltura()">
                </div>

                <!-- Campo de estado (activo o inactivo) -->
                <input type="hidden" name="estado" value="activo">

                <!-- Selección de Preguntas de Seguridad -->
                <div class="row">
                    <h4>Selecciona 3 Preguntas de Seguridad</h4>

                    @for ($i = 1; $i <= 3; $i++)
                        <div class="form-group">
                            <label for="pregunta_{{ $i }}">Pregunta {{ $i }}:</label>
                            <select name="pregunta_{{ $i }}" id="pregunta_{{ $i }}" required onchange="updateSelects()">
                                <option value="">Selecciona una pregunta</option>
                                @foreach($preguntas as $pregunta)
                                    <option value="{{ $pregunta->id_pregunta }}" {{ old('pregunta_' . $i) == $pregunta->id_pregunta ? 'selected' : '' }}>
                                        {{ $pregunta->pregunta }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="respuesta_{{ $i }}" placeholder="Respuesta" required>
                        </div>
                    @endfor
                </div>

                <button type="submit">Registrar Usuario</button>
            </form>
            <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar Sesión</a></p>
              @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="text">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="social-icons">
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-whatsapp"></i></a>
        <a href="#"><i class="bi bi-envelope"></i></a>
    </div>

    <footer>
        <p>&copy; 2024 Ministerio del Poder Popular para la Atención de las Aguas</p>
    </footer>

    <script>
       let isSubmitting = false; // Variable para evitar comprobaciones después de enviar el formulario

// Función para validar la altura
function validarAltura() {
    let alturaInput = document.getElementById('altura');
    if (alturaInput.value < 0) {
        alturaInput.value = 0;
    }
}

// Función para actualizar las opciones de los selects
function updateSelects() {
    const selects = document.querySelectorAll('select[id^="pregunta_"]');
    let selectedValues = [];

    selects.forEach(select => {
        if (select.value) {
            selectedValues.push(select.value);
        }
    });

    selects.forEach(select => {
        select.querySelectorAll('option').forEach(option => {
            if (selectedValues.includes(option.value) && option.value !== select.value) {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    });
}

// Función para limpiar caracteres especiales del nombre de usuario
function limpiarNombreUsuario() {
    const nombreUsuarioInput = document.getElementById('nombre_usuario');
    nombreUsuarioInput.value = nombreUsuarioInput.value.replace(/[^a-zA-Z0-9_]/g, '');
}

// Validación del nombre de usuario
async function validarCampo(campo, valor) {
    if (isSubmitting) return; // No realizar validaciones si el formulario ya se está enviando

    try {
        const response = await fetch('{{ route("validar.cedula.usuario") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ [campo]: valor })
        });

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error al validar:', error);
    }
}

// Mostrar mensajes de error
function mostrarError(inputId, errorId, mensaje) {
    const input = document.getElementById(inputId);
    const errorSpan = document.getElementById(errorId);

    if (mensaje) {
        input.classList.add('input-error');
        input.classList.remove('input-success');
        errorSpan.textContent = mensaje;
    } else {
        input.classList.remove('input-error');
        input.classList.add('input-success');
        errorSpan.textContent = '';
    }
}

// Validar si el nombre de usuario es un correo
function validarFormatoNombreUsuario(nombreUsuario) {
    const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return !regexCorreo.test(nombreUsuario);
}

// Eventos de blur para las validaciones
document.getElementById('cedula').addEventListener('blur', async function () {
    if (isSubmitting) return; // Evitar validaciones si el formulario ya se está enviando

    const cedula = this.value;
    const data = await validarCampo('cedula', cedula);
    mostrarError('cedula', 'cedula_error', data?.error_cedula);
});

document.getElementById('nombre_usuario').addEventListener('blur', async function () {
    if (isSubmitting) return; // Evitar validaciones si el formulario ya se está enviando

    const nombreUsuario = this.value;

    if (!validarFormatoNombreUsuario(nombreUsuario)) {
        mostrarError('nombre_usuario', 'nombre_usuario_error', 'El nombre de usuario no puede ser un correo electrónico');
        return;
    }

    const data = await validarCampo('nombre_usuario', nombreUsuario);
    mostrarError('nombre_usuario', 'nombre_usuario_error', data?.error_nombre_usuario);
});

document.getElementById('email').addEventListener('blur', async function () {
    if (isSubmitting) return; // Evitar validaciones si el formulario ya se está enviando

    const email = this.value;
    const data = await validarCampo('email', email);
    mostrarError('email', 'email_error', data?.error_email);
});

// Función que se ejecuta cuando se envía el formulario
document.querySelector('form').addEventListener('submit', async function (event) {
    if (isSubmitting) return; // Evitar múltiples envíos del formulario

    const cedula = document.getElementById('cedula').value;
    const nombreUsuario = document.getElementById('nombre_usuario').value;
    const email = document.getElementById('email').value;

    let valid = true;

    // Validar cédula
    const dataCedula = await validarCampo('cedula', cedula);
    if (dataCedula?.error_cedula) {
        mostrarError('cedula', 'cedula_error', dataCedula.error_cedula);
        valid = false;
    }

    // Validar nombre de usuario
    if (!validarFormatoNombreUsuario(nombreUsuario)) {
        mostrarError('nombre_usuario', 'nombre_usuario_error', 'El nombre de usuario no puede ser un correo electrónico');
        valid = false;
    } else {
        const dataNombreUsuario = await validarCampo('nombre_usuario', nombreUsuario);
        if (dataNombreUsuario?.error_nombre_usuario) {
            mostrarError('nombre_usuario', 'nombre_usuario_error', dataNombreUsuario.error_nombre_usuario);
            valid = false;
        }
    }

    // Validar correo electrónico
    const dataEmail = await validarCampo('email', email);
    if (dataEmail?.error_email) {
        mostrarError('email', 'email_error', dataEmail.error_email);
        valid = false;
    }

    // Si no hay errores, enviamos el formulario
    if (!valid) {
        event.preventDefault(); // Bloquear el envío del formulario si hay errores
    } else {
        isSubmitting = true; // Marcar que el formulario se está enviando
        desactivarValidaciones(); // Desactivar validaciones asincrónicas
    }
});

function desactivarValidaciones() {
    // Eliminar los eventos de validación asincrónica
    document.getElementById('cedula').removeEventListener('blur', validarCedula);
    document.getElementById('nombre_usuario').removeEventListener('blur', validarNombreUsuario);
    document.getElementById('email').removeEventListener('blur', validarEmail);
}

async function validarCedula() {
    if (isSubmitting) return;
    const cedula = document.getElementById('cedula').value;
    const data = await validarCampo('cedula', cedula);
    mostrarError('cedula', 'cedula_error', data?.error_cedula);
}

async function validarNombreUsuario() {
    if (isSubmitting) return;
    const nombreUsuario = document.getElementById('nombre_usuario').value;
    const data = await validarCampo('nombre_usuario', nombreUsuario);
    mostrarError('nombre_usuario', 'nombre_usuario_error', data?.error_nombre_usuario);
}

async function validarEmail() {
    if (isSubmitting) return;
    const email = document.getElementById('email').value;
    const data = await validarCampo('email', email);
    mostrarError('email', 'email_error', data?.error_email);
}

// Agregar eventos de validación
document.getElementById('cedula').addEventListener('blur', validarCedula);
document.getElementById('nombre_usuario').addEventListener('blur', validarNombreUsuario);
document.getElementById('email').addEventListener('blur', validarEmail);

    </script>

   
@endsection
