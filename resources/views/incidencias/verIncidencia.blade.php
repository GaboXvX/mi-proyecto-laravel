@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Detalles de la Incidencia</h2>
    <p><strong>Código:</strong> {{ $incidencia->cod_incidencia }}</p>
    <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
    <p><strong>Estado:</strong> {{ $incidencia->estado }}</p>
    <p><strong>Fecha de creación:</strong> {{ $incidencia->created_at->format('d-m-Y H:i:s') }}</p>

    @if($incidencia->estado === 'Atendido' && $reparacion)
        <h4 class="mt-4">Detalles de la Reparación</h4>
        <p><strong>Descripción de la atención:</strong> {{ $reparacion->descripcion }}</p>
        <p><strong>Atendido por:</strong> {{ $reparacion->usuario->name }}</p>
        <p><strong>Fecha de atención:</strong> {{ $reparacion->created_at->format('d-m-Y H:i:s') }}</p>
        <div class="mt-3">
            <strong>Prueba fotográfica:</strong>
            <img src="{{ asset('storage/' . $reparacion->prueba_fotografica) }}" alt="Prueba fotográfica" class="img-fluid mt-2" style="max-width: 400px;">
        </div>
    @else
        <p class="text-danger mt-4">Esta incidencia aún no ha sido atendida.</p>
    @endif

    <a href="{{ route('incidencias.index') }}" class="btn btn-secondary mt-3">Volver a la lista</a>
</div>
@endsection