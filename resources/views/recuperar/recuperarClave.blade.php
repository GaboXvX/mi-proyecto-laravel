<!-- RecuperarClave.blade.php -->
@extends('layouts.registrar')
<style>
    .alert-danger{
        margin-bottom: 8px;
        background-color: #f53b3b;
        color: white;
        padding: 10px 15px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px); /* Efecto de desenfoque */
    }

    .alert-success{
        margin-bottom: 8px;
        background-color: green;
        color: white;
        padding: 10px 15px;
        background-color: #00c066;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px); /* Efecto de desenfoque */
    }
</style>
@section('content')
    <form id="respuestaForm" class="form-forget">
            <h2>Recuperar Contraseña</h2>
            <p>Por favor, responda la siguiente pregunta de seguridad:</p>

            <!-- Formulario de respuesta -->
            
            <!-- Mostrar la pregunta -->
            <label for="respuesta" class="form-label" style="color: #000000;">{{ $pregunta->pregunta }}</label>
                @csrf
                <input type="hidden" name="usuario_id" value="{{ $usuario->id_usuario }}">
                <input type="hidden" name="pregunta_id" value="{{ $pregunta->id_pregunta }}">
                <input type="text" name="respuesta" id="respuesta" required>
                <button type="submit" class="btn btn-primary btn-block" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; font-size: 16px; border-radius: 5px;">Validar Respuesta</button>
            

            <!-- Mensajes de error o éxito -->
            <div id="mensaje" style="margin-top: 20px;"></div>
    </form>

    <script>
        document.getElementById('respuestaForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Evitar el envío del formulario

            const form = e.target;
            const formData = new FormData(form);

            fetch("{{ route('recuperar.validarRespuesta') }}", {
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
                    mensajeDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(() => {
                        window.location.href = data.redirect_url; // Redirigir a cambiar contraseña
                    }, 2000);
                } else {
                    mensajeDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        });
    </script>
@endsection