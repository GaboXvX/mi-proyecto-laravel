<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Configuración de Cuenta</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">

  <style>
    body {
      background-color: #f1f5f9; 
      font-family: 'Arial', sans-serif; 
      height: 100vh; 
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      max-width: 500px;
      width: 100%; 
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      background-color: white;
      padding: 30px;
    }

    h2 {
      color: #007bff; 
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
    }

    .form-label {
      color: #495057; 
      font-weight: 500;
      font-size: 16px;
    }

    .form-control {
      border-radius: 8px; 
      padding: 10px;
      border: 1px solid #ced4da;
      background-color: #f8f9fa; 
      font-size: 14px; 
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
      border-color: #007bff; 
      box-shadow: 0 0 0 0.25rem rgba(38, 143, 255, 0.5); 
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
      border-radius: 50px;
      padding: 12px 25px;
      font-weight: 600;
      width: 100%;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
      border-radius: 50px;
      padding: 12px 25px;
      font-weight: 600;
      width: 100%;
      transition: background-color 0.3s ease;
    }

    .btn-success:hover {
      background-color: #218838; 
      border-color: #1e7e34;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .save-changes {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

  </style>
</head>
<body>
   
  <div class="container">
    <div class="card">
      <h2>Configuración de Cuenta</h2>
   @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
      <div class="alert alert-danger mb-3">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
  @endif
  <form action="{{ route('usuarios.cambiar', $usuario->id_usuario) }}" method="POST">
    @csrf
   

   
    <div class="form-group">
        <label for="inputNombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="inputNombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" placeholder="Ingrese su nombre">
    </div>

    
    <div class="form-group">
        <label for="inputApellido" class="form-label">Apellido</label>
        <input type="text" class="form-control" id="inputApellido" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" placeholder="Ingrese su apellido">
    </div>

    
    <div class="form-group">
        <label for="inputCedula" class="form-label">Cédula</label>
        <input type="text" class="form-control" id="inputCedula" name="cedula" value="{{ $usuario->cedula }}" placeholder="Ingrese su cédula" readonly>
    </div>

  
    <div class="form-group">
        <label for="inputCorreo" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="inputCorreo" name="email" value="{{ old('email', $usuario->email) }}" placeholder="Ingrese su correo electrónico">
    </div>

    
    <div class="form-group">
        <label for="inputUsuario" class="form-label">Nombre de Usuario</label>
        <input type="text" class="form-control" id="inputUsuario" name="nombre_usuario" value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" placeholder="Ingrese su nombre de usuario">
    </div>

    
    <div class="form-group">
        <label for="inputContraseña" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="inputContraseña" name="contraseña" placeholder="Ingrese su nueva contraseña">
    </div>

    
    <div class="save-changes">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </div>
</form>


    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
