@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <div class="card shadow-lg p-4 mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <i class="bi bi-shield-lock text-danger" style="font-size: 4rem;"></i>
        </div>
        <h2 class="mb-3 text-danger">Acceso denegado</h2>
        <p class="mb-4">No tienes permisos para acceder a esta sección.<br>Si crees que esto es un error, contacta con un administrador.</p>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>
<script>
// Si el backend pasa el permiso requerido, lo comprobamos periódicamente
@php
    $permiso = $permissionRequired ?? request('permiso') ?? null;
@endphp
@if($permiso)
    document.addEventListener('DOMContentLoaded', function () {
        setInterval(function () {
            fetch('/usuario/permisos', { credentials: 'same-origin' })
                .then(res => res.json())
                .then(data => {
                    if (data && Array.isArray(data.permisos) && data.permisos.includes(@json($permiso))) {
                        window.location.href = document.referrer && document.referrer !== location.href ? document.referrer : '/';
                    }
                });
        }, 4000);
    });
@endif
</script>
@endsection
