@extends('layouts.registrar')

@section('content')
    <div class="container">
        <div class="content">
            <h2>Cambiar Contraseña</h2>
            <p>Ingresa una nueva contraseña para tu cuenta.</p>
                     <!-- Mensajes de éxito o error -->
        </div>
        <hr>

            <!-- Formulario de cambio de contraseña -->
            <form id="cambiarClaveForm" class="form-content">
            <div id="mensaje" style="margin-top: 20px; background-color: #e0e0e0; padding: 10px; border-radius: 5px; color:black;"></div>

                @csrf
                <input type="password" name="password" id="password" placeholder="Nueva Contraseña" required>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar Contraseña" required>
                <button type="submit">Cambiar Contraseña</button>
            </form>
    </div>

    <script>
        document.getElementById('cambiarClaveForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Evitar el envío del formulario

            const form = e.target;
            const formData = new FormData(form);

            fetch("{{ route('cambiar.update', ['usuarioId' => $usuario->id_usuario]) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const mensajeDiv = document.getElementById('mensaje');
                mensajeDiv.innerHTML = ""; // Limpiar mensajes previos

                if (data.success) {
                    // Mensaje de éxito (verde)
                    mensajeDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(() => {
                        window.location.href = data.redirect_url; // Redirigir al inicio de sesión
                    }, 2000);
                } else {
                    // Mensaje de error (rojo)
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
