@extends('layouts.app')

@section('content')
<div class="table-container">
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <h2 class="mb-4">Niveles de Incidencia</h2>
        @can('agregar niveles incidencias')
            @if (count($niveles) < 10)
            <a href="{{ route('niveles-incidencia.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
</svg>
                Nuevo
            </a>
            @endif
        @endcan
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: @json(session('success')),
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Nivel</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Vencimiento</th>
                    <th>Color</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($niveles as $nivel)
                <tr>
                    <td>{{ $nivel->nivel }}</td>
                    <td>{{ $nivel->nombre }}</td>
                    <td>{{ $nivel->descripcion }}</td>
                    <td>
                        @php
                            $horas = $nivel->horas_vencimiento;
                            $dias = floor($horas / 24);
                            $horasRestantes = $horas % 24;
                            
                            if ($dias > 0) {
                                echo $dias . ' día' . ($dias > 1 ? 's' : '');
                                if ($horasRestantes > 0) {
                                    echo ' y ' . $horasRestantes . ' hora' . ($horasRestantes > 1 ? 's' : '');
                                }
                            } else {
                                echo $horas . ' hora' . ($horas != 1 ? 's' : '');
                            }
                        @endphp
                    </td>
                    <td>
                        <span class="badge" style="background-color: {{ $nivel->color }}; color: white;">
                            {{ $nivel->color }}
                        </span>
                    </td>
                    <td>
                        @can('editar niveles incidencias')
                        <form action="{{ route('niveles-incidencia.toggle-status', $nivel) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm {{ $nivel->activo ? 'btn-success' : 'btn-secondary' }}">
                                {{ $nivel->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </form>
                        @else
                        <span class="badge {{ $nivel->activo ? 'bg-success' : 'bg-secondary' }}">
                            {{ $nivel->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        @endcan
                    </td>
                    <td>
                        @can('editar niveles incidencias')
                        <a href="{{ route('niveles-incidencia.edit', $nivel) }}" class="btn btn-sm btn-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg>
                        </a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection