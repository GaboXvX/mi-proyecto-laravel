@extends('layouts.app')
@section('content')

    <!-- Contenido -->
    <div class="table-container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Lista de Empleados</h2>
            <div>
                <a href="{{ route('empleados.create') }}" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                    Nuevo Empleado
                </a>
                <button class="btn btn-primary" onclick="window.location.href='{{ route('usuarios.download.pdf') }}'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-arrow-down" viewBox="0 0 16 16">
                    <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293z"/>
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                    </svg>
                    Descargar
                </button>
            </div>
        </div>

        

        <!-- Filtro asíncrono -->
        <div class="mb-3">
            <label for="filtro-empleados" class="form-label">Filtrar por:</label>
            <select id="filtro-empleados" class="form-select" style="width:auto;display:inline-block">
                <option value="todos" {{ $filtro == 'todos' ? 'selected' : '' }}>Todos</option>
                <option value="registrados" {{ $filtro == 'registrados' ? 'selected' : '' }}>Registrados</option>
                <option value="no_registrados" {{ $filtro == 'no_registrados' ? 'selected' : '' }}>No registrados</option>
            </select>
        </div>

        <div class="table-responsive" id="tabla-usuarios-container">
            @include('usuarios.partials.tabla_usuarios', [
                'usuarios' => $usuarios,
                'empleadosSinUsuario' => $empleadosSinUsuario
            ])
        </div>
    </div>
        <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#filtro-empleados').on('change', function() {
                var filtro = $(this).val();
                $.ajax({
                    url: '{{ route('usuarios.index') }}',
                    type: 'GET',
                    data: { filtro: filtro },
                    dataType: 'html',
                    success: function(data) {
                        $('#tabla-usuarios-container').html(data);
                    },
                    error: function() {
                        alert('Error al filtrar los empleados.');
                    }
                });
            });
        });
    </script>

    @if(session('sweet_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Acción no permitida',
                text: @json(session('sweet_error')),
                confirmButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>
@endif
@endsection