@extends('layouts.registrar')

@section('content')

<style>
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
}

.toast-success {
    background-color: #28a745;
}

.toast-error {
    background-color: #dc3545;
}

h2, h3, h4, label {
    color: black;
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
    border-color: red !important;
}

.input-success {
    border-color: green !important;
}

.error-message {
    color: red !important;
    font-size: 12px;
    margin-top: 5px;
    display: none;
}

.error-message.active {
    display: block;
}
</style>

<div class="container">
    <div class="content">
        <h2>Bienvenido al sistema de MinAguas!</h2>
        <p>Tu plataforma confiable para el manejo de recursos hídricos.</p>
    </div>
    <hr>

    <div id="toastMessage" class="toast">
        <span id="toastText"></span>
    </div>

    <form class="form-content" id="registroForm" action="{{ route('peticiones.store') }}" method="POST">
        @csrf

        <h3>Registrarse</h3>

        <input type="text" id="cedula" name="cedula" placeholder="Cédula" value="{{ old('cedula') }}" required onblur="buscarEmpleado()">
        <span id="cedula_error" class="error-message"></span>

        <div class="row">
            <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required readonly>
            <input type="text" id="apellido" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}" required readonly>
        </div>

        <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Nombre de Usuario" value="{{ old('nombre_usuario') }}" required>
        <span id="nombre_usuario_error" class="error-message"></span>

        <input type="email" id="email" name="email" placeholder="Correo Electrónico" value="{{ old('email') }}" required>
        <span id="email_error" class="error-message"></span>

        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Contraseña" required oninput="showEye()">
            <i id="toggleIcon" class="bi bi-eye-slash toggle-password" onclick="togglePassword()" style="display: none;"></i>
            <span id="password_error" class="error-message"></span>
        </div>

        <input type="text" id="genero" name="genero" placeholder="Género" value="{{ old('genero') }}" required readonly>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required max="{{ date('Y-m-d') }}" readonly>
        <input type="text" id="altura" name="altura" placeholder="Altura" value="{{ old('altura') }}" required min="0" step="0.01" oninput="validarAltura()" readonly>

        <input type="hidden" name="estado" value="activo">

        <span>Selecciona 3 Preguntas de Seguridad:</span>

        @for ($i = 1; $i <= 3; $i++)
            <label for="pregunta_{{ $i }}">Pregunta {{ $i }}:</label>
            <select name="pregunta_{{ $i }}" id="pregunta_{{ $i }}" required onchange="updateSelects()">
                <option value="">Selecciona una pregunta</option>
                @foreach($preguntas as $pregunta)
                    <option value="{{ $pregunta->id_pregunta }}" {{ old('pregunta_' . $i) == $pregunta->id_pregunta ? 'selected' : '' }}>
                        {{ $pregunta->pregunta }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="respuesta_{{ $i }}" placeholder="Respuesta" required disabled>
        @endfor

        <button type="submit">Registrar Usuario</button>
        <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar Sesión</a></p>
    </form>
</div>

<footer>
    <p>&copy; 2024 Ministerio del Poder Popular para la Atención de las Aguas</p>
</footer>

<script>
function showToast(message, type) {
    const toast = document.getElementById("toastMessage");
    const toastText = document.getElementById("toastText");

    toastText.innerHTML = message;
    toast.className = "toast " + (type === "success" ? "toast-success" : "toast-error");
    toast.style.display = "block";

    setTimeout(() => {
        toast.style.display = "none";
    }, 3000);
}

function validarAltura() {
    let alturaInput = document.getElementById('altura');
    if (alturaInput.value < 0) {
        alturaInput.value = 0;
    }
}

function updateSelects() {
    const selects = document.querySelectorAll('select[id^="pregunta_"]');
    let selectedValues = [];

    selects.forEach(select => {
        if (select.value) {
            selectedValues.push(select.value);
        }
    });

    selects.forEach(select => {
        const respuestaInput = select.nextElementSibling;
        respuestaInput.disabled = !select.value;

        select.querySelectorAll('option').forEach(option => {
            option.disabled = selectedValues.includes(option.value) && option.value !== select.value;
        });
    });
}

document.addEventListener('DOMContentLoaded', updateSelects);

function buscarEmpleado() {
    const cedula = document.getElementById('cedula').value.trim();
    const cedulaError = document.getElementById('cedula_error');

    ['nombre', 'apellido', 'genero', 'fecha_nacimiento', 'altura'].forEach(id => {
        document.getElementById(id).value = '';
    });

    cedulaError.textContent = '';
    cedulaError.classList.remove('active');
    cedulaError.style.display = 'none';

    if (!cedula) return;

    cedulaError.textContent = 'Buscando...';
    cedulaError.style.color = 'blue';
    cedulaError.classList.add('active');
    cedulaError.style.display = 'block';

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

        document.getElementById('nombre').value = data.nombre || '';
        document.getElementById('apellido').value = data.apellido || '';
        document.getElementById('genero').value = data.genero || '';
        document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento || '';
        document.getElementById('altura').value = data.altura || '';

        cedulaError.textContent = '';
        cedulaError.classList.remove('active');
        cedulaError.style.display = 'none';
    })
    .catch(error => {
        console.error("Error:", error);
        cedulaError.textContent = error.message;
        cedulaError.style.color = 'red';
        cedulaError.classList.add('active');
        cedulaError.style.display = 'block';

        ['nombre', 'apellido', 'genero', 'fecha_nacimiento', 'altura'].forEach(id => {
            document.getElementById(id).value = '';
        });
    });
}

function limpiarNombreUsuario() {
    const nombreUsuarioInput = document.getElementById('nombre_usuario');
    nombreUsuarioInput.value = nombreUsuarioInput.value.replace(/[^a-zA-Z0-9_]/g, '');
}

document.getElementById('nombre_usuario').addEventListener('input', limpiarNombreUsuario);

document.getElementById('registroForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    // Limpiar todos los mensajes de error antes de enviar
    document.querySelectorAll('.error-message').forEach(el => {
        el.textContent = '';
        el.classList.remove('active');
        el.style.display = 'none';
    });

    // Mostrar loader o indicador de carga si es necesario
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Procesando...';

    fetch(form.action, {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(async response => {
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Respuesta no válida del servidor");
        }

        const data = await response.json();

        if (!response.ok) {
            // Si la respuesta no es OK, lanzar error con los datos
            const error = new Error(data.message || 'Error en la solicitud');
            error.data = data;
            throw error;
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito y redirigir
            showToast(data.message || "✅ ¡Registro exitoso! Serás redirigido al login.", "success");
            setTimeout(() => {
                window.location.href = data.redirect || "{{ route('login') }}";
            }, 2000);
        } else {
            // Si no hay éxito pero tampoco hay errores específicos
            throw new Error(data.message || 'Error desconocido al procesar la solicitud');
        }
    })
    .catch(error => {
        console.error("Error en el registro:", error);
        
        // Restaurar botón
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;

        // Manejar diferentes tipos de errores
        if (error.data && error.data.errors) {
            // Mostrar errores de validación del servidor
            showToast("⚠️ Por favor corrige los errores en el formulario", "error");
            
            for (const field in error.data.errors) {
                const errorElement = document.getElementById(field + '_error');
                if (errorElement) {
                    let errorText = error.data.errors[field][0];
                    // Traducir mensajes de error comunes
                    if (errorText.includes("has already been taken")) {
                        if (field === "nombre_usuario") errorText = "El nombre de usuario ya está en uso.";
                        if (field === "cedula") errorText = "La cédula ya está registrada.";
                        if (field === "email") errorText = "El correo electrónico ya está registrado.";
                    }
                    errorElement.textContent = errorText;
                    errorElement.classList.add('active');
                    errorElement.style.display = 'block';
                }
            }
        } else {
            // Mostrar mensaje de error genérico
            showToast(error.message || "❌ Error al procesar la solicitud. Inténtalo nuevamente.", "error");
        }
    });
});
</script>

@endsection