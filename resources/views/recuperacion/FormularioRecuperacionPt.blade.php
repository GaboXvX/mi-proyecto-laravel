@extends('layouts.registrar')

@section('content')
<style>
    .card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        margin-top: 20px;
    }

    .card-header {
        background-color: #2c3e50;
        color: #fff;
        font-weight: bold;
        border-radius: 10px 10px 0 0 !important;
        padding: 20px;
        font-size: 20px;
    }

    .card-body {
        padding: 25px;
    }

    .btn-primary {
        background-color: #3498db;
        border: none;
        padding: 0.5rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        color: #fff;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }

    .form-control {
        border-radius: 5px;
        padding: 0.8rem;
        border: 1px solid #ddd;
        transition: border 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #5c6ef8;
        box-shadow: 0 0 5px rgba(92, 110, 248, 0.5);
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

    .form-group {
        margin-bottom: 18px;
    }

    form{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .notice{
        color: #fff;
        padding: 10px;
        border-radius: 8px;
        background: #a0bacc;
    }

    label {
        font-weight: 600;
        color: #2c3e50;
    }
</style>

   
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
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

                    <div class="notice">
                        <p>
                            <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                            Puedes renovar tu solicitud hasta 3 veces si ha sido rechazada.
                        </p>
                    </div>
                </div>
            </div>
        </div>

<!-- Asegúrate de tener Bootstrap Icons en tu layout -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

@endsection