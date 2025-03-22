@extends('layouts.registrar')

@section('content')
    <div class="container">
        <div class="content">
            <h2>Cambiar Contraseña</h2>
            <p>Ingresa una nueva contraseña para tu cuenta.</p>
        </div>
        <hr>
        <div class="form-content">
            <!-- Mensajes de éxito o error -->
            @if(session('success'))
                <div class="alert alert-success" style="color:green">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" >
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario de cambio de contraseña -->
            <form action="{{ route('cambiar.update', ['usuarioId' => $usuario->id_usuario]) }}" method="POST">
                @csrf

                <!-- Nueva contraseña -->
                <div class="row">
                    <input type="password" name="password" placeholder="Nueva Contraseña" required>
                </div>

                <!-- Confirmar nueva contraseña -->
                <div class="row">
                    <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                </div>

                <button type="submit">Cambiar Contraseña</button>
            </form>
        </div>
    </div>
@endsection
