<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="{{asset('css/home.css')}}">
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Recuperar Contraseña</h2>
            <p>Para iniciar la recuperación de su contraseña es necesario que responda a las siguientes preguntas.</p>
            <p>Una vez completada la verificación con éxito se restablecera su contraseña a <strong>12345678</strong>.</p>
        </div>
        <hr>
        <div class="form-content">
            <div class="quest">
            <form action="{{route('comprobar.preguntas')}}" method="post">
                @csrf
                <div >
                    <label for="cedula">¿Cuál es tu cédula?</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="mascota">¿Cuál es el nombre de tu primera mascota?</label>
                    <input type="text" class="form-control" id="mascota" name="mascota" required>
                </div>
                <div class="form-group">
                    <label for="ciudad">¿En qué ciudad naciste?</label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                </div>
                <div class="form-group">
                    <div class="row">
                    <label for="amigo">¿Cuál es el nombre de tu mejor amigo de la infancia?</label></div>
                    <input type="text" class="form-control" id="amigo" name="amigo" required>
                </div>
                <div class="form-group text-center"> <br>
                    <button type="submit" class="btn btn-primary">Enviar Respuestas</button>
                </div>
            </form>
            <div class="text-center">
                </div>
                <a href="{{ route('login') }}">Volver al inicio de sesión</a>
            </div>
        </div>
    </div>
</div>
    </div>
    <footer>
        <p>&copy; 2024 Ministerio del Poder Popular para la Atención de las Aguas</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>