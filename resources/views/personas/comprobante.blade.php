@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Comprobante #{{ $comprobante->id }}
        </div>
        <div class="card-body">
            <h5 class="card-title">Detalles del Comprobante</h5>
            <p class="card-text">Fecha: {{ $comprobante->fecha }}</p>
            <p class="card-text">Monto: ${{ number_format($comprobante->monto, 2) }}</p>

            <!-- Aquí podrías mostrar otros detalles del comprobante -->

            <!-- Botón de Volver -->
            <a href="{{ route('home') }}" class="btn btn-secondary">Volver</a>

            <!-- Botón de Descargar -->
            <a href="{{ route('comprobantes.download', $comprobante->id) }}" class="btn btn-primary">Descargar Comprobante</a>
        </div>
    </div>
</div>
@endsection
