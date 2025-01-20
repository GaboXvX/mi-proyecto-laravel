<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 5%;
        }
        .card {
            border-radius: 10px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            border-radius: 10px 10px 0 0;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .container {
            max-width: 500px;
        }
        .form-group label {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">    @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
<div class="alert alert-danger" role="alert">
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif</div>
<div class="container">
    <div class="card">
        <div class="card-header">
            Recuperar Contraseña
        
        </div>
        <div class="card-body">
            <h5 class="card-title text-center">Por favor, responde a las siguientes preguntas de seguridad</h5>
            <p>una vez completada la verificacion con éxito se reestablecera su contraseña a 12345678</p>
            <form action="{{route('comprobar.preguntas')}}" method="post">
               @csrf
                <div class="form-group">
                    <label for="pregunta1">¿Cuál es tu cédula?</label>
                    <input type="text" class="form-control" id="pregunta1" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="pregunta1">¿Cuál es el nombre de tu primera mascota?</label>
                    <input type="text" class="form-control" id="pregunta1" name="mascota" required>
                </div>
                <div class="form-group">
                    <label for="pregunta2">¿En qué ciudad naciste?</label>
                    <input type="text" class="form-control" id="pregunta2" name="ciudad" required>
                </div>
                <div class="form-group">
                    <label for="pregunta3">¿Cuál es el nombre de tu mejor amigo de la infancia?</label>
                    <input type="text" class="form-control" id="pregunta3" name="amigo" required>
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">Enviar Respuestas</button>
                </div>
            </form>
            <a href="login">volver</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
