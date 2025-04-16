@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">Incidencias de {{ $persona->nombre }} {{ $persona->apellido }}</h2>

    @if($persona->incidencias->isEmpty())
        <p class="alert alert-warning">No hay incidencias registradas para esta persona.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tipo de Incidencia</th>
                    <th>Descripción</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($persona->incidencias as $incidencia)
                    <tr>
                        <td>{{ $incidencia->tipo_incidencia }}</td>
                        <td>{{ $incidencia->descripcion }}</td>
                        <td>{{ $incidencia->nivel_prioridad }}</td>
                        <td>{{ $incidencia->estado }}</td>
                        <td>{{ $incidencia->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($incidencia->estado !== 'atendido')
                                <a href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $persona->slug]) }}" class="btn btn-primary btn-sm">Modificar incidencia</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
