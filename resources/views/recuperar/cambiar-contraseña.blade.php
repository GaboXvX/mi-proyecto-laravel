@extends('layouts.registrar')

@section('content')
    <div class="container">
        <div class="content">
            <h2>Cambiar Contraseña o Correo</h2>
            <p>Selecciona qué deseas cambiar y completa el formulario correspondiente.</p>
            <!-- Mensajes de éxito o error -->
        </div>
        <hr>

        <!-- Selector para alternar entre formularios -->
        <div style="margin-bottom: 20px;">
            <label for="accionSelector">¿Qué deseas cambiar?</label>
            <select id="accionSelector">
                <option value="password">Contraseña</option>
                <option value="email">Correo Electrónico</option>
            </select>
        </div>

        <!-- Formulario para cambiar contraseña -->
        <form id="cambiarPasswordForm" class="form-content" style="margin-top: 20px;">
            <h3>Cambiar Contraseña</h3>
            <div id="mensajePassword" style="margin-top: 20px; background-color: #e0e0e0; padding: 10px; border-radius: 5px; color:black;"></div>

            @csrf
            <input type="password" name="password" id="password" placeholder="Nueva Contraseña" required>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar Contraseña" required>
            <button type="submit">Actualizar Contraseña</button>
        </form>

        <!-- Formulario para cambiar correo -->
        <form id="cambiarEmailForm" class="form-content" style="margin-top: 20px; display: none;">
            <h3>Cambiar Correo Electrónico</h3>
            <div id="mensajeEmail" style="margin-top: 20px; background-color: #e0e0e0; padding: 10px; border-radius: 5px; color:black;"></div>

            @csrf
            <input type="email" name="email" id="email" placeholder="Nuevo Correo Electrónico" required>
            <input type="email" name="email_confirmation" id="email_confirmation" placeholder="Confirmar Correo Electrónico" required>
            <button type="submit">Actualizar Correo</button>
        </form>
    </div>

    <script>
        // Alternar entre formularios según el selector
        document.getElementById('accionSelector').addEventListener('change', function () {
            const accion = this.value;

            document.getElementById('cambiarPasswordForm').style.display = accion === 'password' ? 'block' : 'none';
            document.getElementById('cambiarEmailForm').style.display = accion === 'email' ? 'block' : 'none';
        });

        // Manejo del envío del formulario de contraseña
        document.getElementById('cambiarPasswordForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            if (formData.get('password') !== formData.get('password_confirmation')) {
                alert("Las contraseñas no coinciden.");
                return;
            }

            fetch("{{ route('cambiar.update', ['usuarioId' => $usuario->id_usuario]) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const mensajeDiv = document.getElementById('mensajePassword');
                mensajeDiv.innerHTML = "";

                if (data.success) {
                    mensajeDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    let errores = "";
                    for (const [campo, mensajes] of Object.entries(data.errors)) {
                        errores += `<div class="alert alert-danger">${mensajes.join("<br>")}</div>`;
                    }
                    mensajeDiv.innerHTML = errores;
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        });

        // Manejo del envío del formulario de correo
        document.getElementById('cambiarEmailForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            if (formData.get('email') !== formData.get('email_confirmation')) {
                alert("Los correos electrónicos no coinciden.");
                return;
            }

            fetch("{{ route('cambiar.email', ['usuarioId' => $usuario->id_usuario]) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const mensajeDiv = document.getElementById('mensajeEmail');
                mensajeDiv.innerHTML = "";

                if (data.success) {
                    mensajeDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    let errores = "";
                    for (const [campo, mensajes] of Object.entries(data.errors)) {
                        errores += `<div class="alert alert-danger">${mensajes.join("<br>")}</div>`;
                    }
                    mensajeDiv.innerHTML = errores;
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        });
    </script>
@endsection
