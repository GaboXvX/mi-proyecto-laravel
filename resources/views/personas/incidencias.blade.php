@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center">Incidencias de {{ $persona->nombre }} {{ $persona->apellido }}</h2>

    @if($incidencias->isEmpty())
        <p class="alert alert-warning">No hay incidencias registradas para esta persona.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Codigo Incidencia</th>
                    <th>Tipo de Incidencia</th>
                    <th>Descripción</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incidencias as $incidencia)
                    <tr>
                        <td>{{ $incidencia->cod_incidencia }}</td>
                        <td>{{ $incidencia->tipo_incidencia }}</td>
                        <td>{{ $incidencia->descripcion }}</td>
                        <td>{{ $incidencia->nivel_prioridad }}</td>
                        <td>{{ $incidencia->estado }}</td>
                        <td>{{ $incidencia->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($incidencia->estado !== 'Atendido')
                                <a href="{{ route('incidencias.edit', [$incidencia->slug, 'from' => 'persona']) }}" class="btn btn-primary">Modificar incidencia</a>
                                <form method="POST" action="{{ route('incidencias.update', $incidencia->slug) }}">
                                    @csrf
                                    @method('PUT')
                                    <!-- Otros campos del formulario -->
                                    <input type="hidden" name="redirect_source" value="personas.show">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </form>
                            @endif
                            <a href="{{ route('incidencias.descargar', ['slug' => $incidencia->slug]) }}" class="btn btn-success btn-sm" title="Descargar comprobante">
                                <i class="bi bi-download"></i> Descargar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $incidencias->links() }}
        </div>
    @endif
</div>
@endsection
