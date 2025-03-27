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
                <input type="text" id="cedula" name="cedula" placeholder="Cédula" value="{{ old('cedula') }}" required>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" value="{{ old('email') }}" required>
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
        // Función para validar que la altura no sea negativa
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

            // Obtener los valores seleccionados
            selects.forEach(select => {
                if (select.value) {
                    selectedValues.push(select.value);
                }
            });

            // Deshabilitar las opciones ya seleccionadas en otros selects
            selects.forEach(select => {
                select.querySelectorAll('option').forEach(option => {
                    if (selectedValues.includes(option.value) && option.value !== select.value) {
                        option.disabled = true;  // Deshabilitar si ya está seleccionado
                    } else {
                        option.disabled = false; // Habilitar si no está seleccionado
                    }
                });
            });
        }

        // Inicializar la función al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            updateSelects();
        });
    </script>

   
@endsection
