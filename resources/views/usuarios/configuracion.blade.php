@extends('layouts.app')
@section('content')
    <main class="form-container">
        <div class="container py-4">
            <div >
                <div class="card-body">
                    <h2 class="text-center text-primary mb-3">Configuración de Cuenta</h2>
                    
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
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Mostrar datos del usuario -->
                    <table class="profile-table table table-bordered">
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Apellido:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->apellido }}</td>
                        </tr>
                        <tr>
                            <th>Cédula:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->cedula }}</td>
                        </tr>
                        <tr>
                            <th>Género:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Nacimiento:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->fecha_nacimiento }}</td>
                        </tr>
                        <tr>
                            <th>Altura:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->altura }} cm</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->telefono }}</td>
                        </tr>
                        <tr>
                            <th>Cargo:</th>
                            <td>{{ auth()->user()->empleadoAutorizado->cargo->nombre_cargo }}</td>
                        </tr>
                    </table>

                    <hr>

                    <form action="{{ route('usuarios.cambiar', $usuario->id_usuario) }}" method="POST" id="configuracionForm">
                        @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputCorreo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-sm" id="inputCorreo"
                                    name="email" value="{{ old('email', $usuario->email) }}"
                                    placeholder="Ingrese su correo electrónico" required>
                                <small id="correoFeedback" class="text-muted"></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inputUsuario" class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control form-control-sm" id="inputUsuario"
                                        name="nombre_usuario"
                                        value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}"
                                        placeholder="Ingrese su nombre de usuario" required>
                                    <small id="usuarioFeedback" class="text-muted"></small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputContraseña" class="form-label">Contraseña</label>
                                <input type="password" class="form-control form-control-sm" id="inputContraseña"
                                    name="contraseña" placeholder="Ingrese su nueva contraseña">
                            </div>
                        </div>
                    </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-success btn-sm px-4" id="submitButton" disabled>Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>


<script>
    const correoInput = document.getElementById('inputCorreo');
const usuarioInput = document.getElementById('inputUsuario');
const passwordInput = document.getElementById('inputContraseña');
const submitButton = document.getElementById('submitButton');

let correoOriginal = correoInput.value;
let usuarioOriginal = usuarioInput.value;

let correoDisponible = true;
let usuarioDisponible = true;

function hayCambios() {
    const correoCambio = correoInput.value !== correoOriginal;
    const usuarioCambio = usuarioInput.value !== usuarioOriginal;
    const passwordIngresado = passwordInput.value.trim() !== '';
    return correoCambio || usuarioCambio || passwordIngresado;
}

function validarFormulario() {
    const cambios = hayCambios();
    const validacionesOk = correoDisponible && usuarioDisponible;
    submitButton.disabled = !(cambios && validacionesOk);
}

correoInput.addEventListener('input', function () {
    const correo = this.value;
    const feedback = document.getElementById('correoFeedback');

    if (correo.trim() === '') {
        feedback.textContent = '';
        correoDisponible = true;
        validarFormulario();
        return;
    }

    fetch(`/validar-correo/${correo}?excluir={{ $usuario->id_usuario }}`)
        .then(response => response.json())
        .then(data => {
            if (data.disponible) {
                feedback.textContent = '¡Correo disponible!';
                feedback.classList.remove('text-danger');
                feedback.classList.add('text-success');
                correoDisponible = true;
            } else {
                feedback.textContent = 'El correo ya está en uso.';
                feedback.classList.remove('text-success');
                feedback.classList.add('text-danger');
                correoDisponible = false;
            }
            validarFormulario();
        })
        .catch(() => {
            feedback.textContent = 'Error al validar el correo.';
            feedback.classList.remove('text-success');
            feedback.classList.add('text-danger');
            correoDisponible = false;
            validarFormulario();
        });
});

usuarioInput.addEventListener('input', function () {
    const nombreUsuario = this.value;
    const feedback = document.getElementById('usuarioFeedback');

    if (nombreUsuario.trim() === '') {
        feedback.textContent = '';
        usuarioDisponible = true;
        validarFormulario();
        return;
    }

    fetch(`/validar-usuario/${nombreUsuario}?excluir={{ $usuario->id_usuario }}`)
        .then(response => response.json())
        .then(data => {
            if (data.disponible) {
                feedback.textContent = '¡Nombre de usuario disponible!';
                feedback.classList.remove('text-danger');
                feedback.classList.add('text-success');
                usuarioDisponible = true;
            } else {
                feedback.textContent = 'El nombre de usuario ya está en uso.';
                feedback.classList.remove('text-success');
                feedback.classList.add('text-danger');
                usuarioDisponible = false;
            }
            validarFormulario();
        })
        .catch(() => {
            feedback.textContent = 'Error al validar el nombre de usuario.';
            feedback.classList.remove('text-success');
            feedback.classList.add('text-danger');
            usuarioDisponible = false;
            validarFormulario();
        });
});

passwordInput.addEventListener('input', validarFormulario);

</script>
@endsection