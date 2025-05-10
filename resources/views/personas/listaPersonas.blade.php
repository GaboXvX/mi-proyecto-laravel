@extends('layouts.app')
@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Lista de Personas</h2>
        <div>
            <a href="{{ route('personas.create') }}" class="btn btn-primary" title="Agregar persona">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-plus" viewBox="0 0 16 16">
                    <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                    <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="mb-4">
        <input type="search" id="buscar" placeholder="Buscar por cédula" class="form-control d-inline-block" style="width: auto;">
    </div>

    <div id="personas-lista">
        @if (!empty($personas) && count($personas) > 0)
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cédula</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="personas-tbody">
                        @foreach ($personas as $persona)
                            <tr>
                                <td>{{ $persona->nombre }}</td>
                                <td>{{ $persona->apellido }}</td>
                                <td>{{ $persona->cedula }}</td>
                                <td>{{ $persona->correo }}</td>
                                <td>{{ $persona->telefono }}</td>
                                <td>
                                    <div class="btn-group gap-1">
                                        <a href="{{ route('personas.show', $persona->slug) }}" class="btn btn-info btn-sm rounded" title="Ver persona">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                            </svg>
                                        </a>
                                        <a href="{{route('incidencias.crear',  $persona->slug )}}" class="btn btn-success btn-sm rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                                                                                        <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0"/>
                                                                                    </svg>
                                                                                </a> 
                                        <a href="{{ route('personas.incidencias', $persona->slug) }}" class="btn btn-warning btn-sm rounded">Ver Incidencias</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="alert alert-warning">No se encontró ninguna persona con esa cédula.</p>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del buscador de personas
        const originalData = @json($personas->items());
        new BuscadorPersonas('buscar', 'personas-tbody', '{{ route('personas.buscar') }}', originalData);

        // Mostrar mensaje de éxito si está presente en la sesión
        const successMessage = "{{ session('success') }}";
        if (successMessage) {
            const alertContainer = document.createElement('div');
            alertContainer.className = 'alert alert-success';
            alertContainer.textContent = successMessage;
            document.querySelector('.table-container').prepend(alertContainer);
            setTimeout(() => alertContainer.remove(), 5000);
        }
    });

    class BuscadorPersonas {
        constructor(inputId, tbodyId, url, originalData) {
            this.input = document.getElementById(inputId);
            this.tbody = document.getElementById(tbodyId);
            this.url = url;
            this.originalData = originalData;
            this.input.addEventListener('input', () => this.buscarPersonas());
        }

        async buscarPersonas() {
            const query = this.input.value.trim();
            if (!query) {
                this.mostrarResultados(this.originalData);
                return;
            }

            try {
                const response = await fetch(this.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ query })
                });
                const personas = await response.json();
                this.mostrarResultados(personas);
            } catch (error) {
                console.error('Error al buscar personas:', error);
            }
        }

        mostrarResultados(personas) {
            this.tbody.innerHTML = personas.map(persona => `
                <tr>
                    <td>${persona.nombre}</td>
                    <td>${persona.apellido}</td>
                    <td>${persona.cedula}</td>
                    <td>${persona.correo}</td>
                    <td>${persona.telefono}</td>
                    <td>
                        <div class="btn-group">
                            <a href="/persona/${persona.slug}" class="btn btn-info btn-sm">Ver</a>
                            <a href="/persona/${persona.slug}/incidencias" class="btn btn-warning btn-sm">Ver Incidencias</a>
                        </div>
                    </td>
                </tr>
            `).join('');
        }
    }
</script>
@endsection