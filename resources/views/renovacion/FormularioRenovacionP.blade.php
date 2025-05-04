@extends('layouts.registrar')

@section('content')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        margin-top: 2rem;
    }

    .card-header {
        background-color: #2c3e50;
        color: white;
        font-weight: bold;
        border-radius: 10px 10px 0 0 !important;
        padding: 1.2rem;
        font-size: 1.2rem;
    }

    .card-body {
        padding: 2rem;
    }

    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
        padding: 0.5rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: translateY(-2px);
    }

    .form-control {
        border-radius: 5px;
        padding: 0.8rem;
        border: 1px solid #ddd;
        transition: border 0.3s ease;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 5px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .container {
        max-width: 900px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        font-weight: 600;
        color: #2c3e50;
    }
</style>

    <div class="container">
            <div class="card">
                <div class="card-header text-center">
                    <i class="bi bi-arrow-repeat" style="margin-right: 8px;"></i>
                    Renovar Solicitud Rechazada
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill" style="margin-right: 8px;"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('renovacion.procesar') }}">
                        @csrf

                        <div class="form-group">
                            <label for="correo" class="col-md-4 col-form-label text-md-right">
                                <i class="bi bi-envelope" style="margin-right: 8px;"></i>
                                Correo Electrónico
                            </label>

                            <div class="col-md-6">
                                <input id="correo" type="email" class="form-control @error('correo') is-invalid @enderror" 
                                       name="correo" value="{{ old('correo') }}" required autocomplete="email" autofocus
                                       placeholder="Ingresa tu correo registrado">

                                @error('correo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong><i class="bi bi-exclamation-circle" style="margin-right: 5px;"></i>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send" style="margin-right: 8px;"></i>
                                    Renovar Solicitud
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="text-muted">
                        <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                        Puedes renovar tu solicitud hasta 3 veces si ha sido rechazada.
                    </p>
                </div>
            </div>
    </div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

@endsection