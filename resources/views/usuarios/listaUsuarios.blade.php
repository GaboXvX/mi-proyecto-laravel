@extends('layouts.app')
@section('content')

    <!-- Contenido -->
    <div class="table-container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Lista de Empleados</h2>
            <a href="{{ route('empleados.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Empleado
            </a>
            <button class="btn btn-success" onclick="window.location.href='{{ route('usuarios.download.pdf') }}'">
                <i class="bi bi-file-earmark-arrow-down"></i> Descargar
            </button>
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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