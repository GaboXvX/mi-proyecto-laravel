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
            <button type="submit">Actualizar Contraseña</button>
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
            alert("Las contraseñas no coinciden.");
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
                mensajeDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                setTimeout(() => {
                    window.location.href = data.redirect_url || "{{ route('login') }}";
                }, 2000);
            } else {
                let errores = "";
                if (data.errors) {
                    for (const [campo, mensajes] of Object.entries(data.errors)) {
                        errores += `<div class="alert alert-danger">${mensajes.join("<br>")}</div>`;
                    }
                } else {
                    errores = `<div class="alert alert-danger">${data.message || 'Error desconocido'}</div>`;
                }
                mensajeDiv.innerHTML = errores;
            }
        } catch (error) {
            console.error("Error:", error);
            document.getElementById('mensajePassword').innerHTML = 
                `<div class="alert alert-danger">Error en la conexión. Intente nuevamente.</div>`;
        }
    });

    // Manejo del envío del formulario de correo
    document.getElementById('cambiarEmailForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        if (formData.get('email') !== formData.get('email_confirmation')) {
            alert("Los correos electrónicos no coinciden.");
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
                mensajeDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                setTimeout(() => {
                    window.location.href = data.redirect_url || "{{ route('login') }}";
                }, 2000);
            } else {
                let errores = "";
                if (data.errors) {
                    for (const [campo, mensajes] of Object.entries(data.errors)) {
                        errores += `<div class="alert alert-danger">${mensajes.join("<br>")}</div>`;
                    }
                } else {
                    errores = `<div class="alert alert-danger">${data.message || 'Error desconocido'}</div>`;
                }
                mensajeDiv.innerHTML = errores;
            }
        } catch (error) {
            console.error("Error:", error);
            document.getElementById('mensajeEmail').innerHTML = 
                `<div class="alert alert-danger">Error en la conexión. Intente nuevamente.</div>`;
        }
    });
</script>
@endsection