@extends('layouts.registrar')

@section('content')
    <div class="container">
        <div class="content">
            <h2>Cambiar Contraseña o Correo</h2>
            <p>Selecciona qué deseas cambiar y completa el formulario correspondiente.</p>
            <!-- Mensajes de éxito o error -->

            <!-- Selector para alternar entre formularios -->
            <div class="cambio-select">
                <label for="accionSelector">¿Qué deseas cambiar?</label>
                <select id="accionSelector">
                    <option value="password">Contraseña</option>
                    <option value="email">Correo Electrónico</option>
                </select>
            </div>
        </div>
        <hr>

        <!-- Formulario para cambiar contraseña -->
        <form id="cambiarPasswordForm" class="form-content">
            <h3>Cambiar Contraseña</h3>
            <div id="mensajePassword" style="padding: 8px; color: green;"></div>

            @csrf
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Nueva Contraseña" required oninput="showEye()">
                <i id="toggleIcon" class="bi bi-eye-slash toggle-password" onclick="togglePassword()" style="display: none;"></i>
            </div>
            
            <div class="password-container">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar Contraseña" required oninput="showEye()">
                <i id="toggleIcon" class="bi bi-eye-slash toggle-password" onclick="togglePassword()" style="display: none;"></i>
            </div>

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
            
            <button type="submit" id="cambiarPassword">Actualizar Contraseña</button>
        </form>

        <!-- Formulario para cambiar correo -->
        <form id="cambiarEmailForm" class="form-content" style="display: none;">
            <h3>Cambiar Correo Electrónico</h3>
            <div id="mensajeEmail" style="padding: 8px; color:green;"></div>

            @csrf
            <input type="email" name="email" id="email" placeholder="Nuevo Correo Electrónico" required>
            <input type="email" name="email_confirmation" id="email_confirmation" placeholder="Confirmar Correo Electrónico" required>
            <button type="submit">Actualizar Correo</button>
        </form>
    </div>

    <script src="{{ asset('js/home.js') }}"></script>

<script>
    const passwordInput = document.getElementById("password");
    const btncambio = document.getElementById("cambiarPassword");

    const rules = {
        length: document.getElementById("rule-length"),
        uppercase: document.getElementById("rule-uppercase"),
        lowercase: document.getElementById("rule-lowercase"),
        number: document.getElementById("rule-number"),
        special: document.getElementById("rule-special")
    };

    passwordInput.addEventListener("input", function () {
        const value = passwordInput.value;

        const hasUppercase = /[A-Z]/.test(value);
        const hasLowercase = /[a-z]/.test(value);
        const hasNumber = /[0-9]/.test(value);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(value);
        const hasMinLength = value.length >= 8;

        toggleClass(rules.uppercase, hasUppercase);
        toggleClass(rules.lowercase, hasLowercase);
        toggleClass(rules.number, hasNumber);
        toggleClass(rules.special, hasSpecial);
        toggleClass(rules.length, hasMinLength);

        // Solo si todas las condiciones se cumplen, habilita el botón
        const isValidPassword = hasUppercase && hasLowercase && hasNumber && hasSpecial && hasMinLength;
        btncambio.disabled = !isValidPassword;
    });

    function toggleClass(element, condition) {
        element.classList.toggle("valid", condition);
        element.classList.toggle("invalid", !condition);
    }
</script>

<script>
    // Alternar entre formularios según el selector
    document.getElementById('accionSelector').addEventListener('change', function () {
        const accion = this.value;
        document.getElementById('cambiarPasswordForm').style.display = accion === 'password' ? 'block' : 'none';
        document.getElementById('cambiarEmailForm').style.display = accion === 'email' ? 'block' : 'none';
    });

    // Manejo del envío del formulario de contraseña
    document.getElementById('cambiarPasswordForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        if (formData.get('password') !== formData.get('password_confirmation')) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden.'
            });
            return;
        }

        try {
            const response = await fetch("{{ route('cambiar.update', ['usuarioId' => $usuario->id_usuario]) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams(formData)
            });

            const data = await response.json();
            const mensajeDiv = document.getElementById('mensajePassword');
            mensajeDiv.innerHTML = "";

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = data.redirect_url || "{{ route('login') }}";
                });
            } else {
                let errores = "";
                if (data.errors) {
                    for (const [campo, mensajes] of Object.entries(data.errors)) {
                        errores += `${mensajes.join("<br>")}`;
                    }
                } else {
                    errores = data.message || 'Error desconocido';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errores
                });
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'Error en la conexión. Intente nuevamente.'
            });
        }
    });

    // Manejo del envío del formulario de correo
    document.getElementById('cambiarEmailForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        if (formData.get('email') !== formData.get('email_confirmation')) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Los correos electrónicos no coinciden.'
            });
            return;
        }

        try {
            const response = await fetch("{{ route('cambiar.email', ['usuarioId' => $usuario->id_usuario]) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams(formData)
            });

            const data = await response.json();
            const mensajeDiv = document.getElementById('mensajeEmail');
            mensajeDiv.innerHTML = "";

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = data.redirect_url || "{{ route('login') }}";
                });
            } else {
                let errores = "";
                if (data.errors) {
                    for (const [campo, mensajes] of Object.entries(data.errors)) {
                        errores += `${mensajes.join("<br>")}`;
                    }
                } else {
                    errores = data.message || 'Error desconocido';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errores
                });
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'Error en la conexión. Intente nuevamente.'
            });
        }
    });
</script>
@endsection