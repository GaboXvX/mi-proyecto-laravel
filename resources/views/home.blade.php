@extends('layouts.app')
@section('content')
    
        <div class="container-fluid mt-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Personas</h5>
                            <p class="card-text fs-3">{{ $totalPersonas }}</p>
                            <a href="{{ route('personas.index') }}" class="text-decoration-none">Ver más</a>
                         </div>
                    </div>
                </div>
                @can('ver empleados')
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Empleados</h5>
                            <p class="card-text fs-3">{{ $totalUsuarios }}</p>
                            <a href="{{ route('usuarios.index') }}" class="text-decoration-none">Ver más</a>
                        </div>
                    </div>
                </div>
                @endcan
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Incidencias</h5>
                            <p class="card-text fs-3">{{ $totalIncidencias }}</p>
                            <a href="{{ route('incidencias.index') }}" class="text-decoration-none">Ver más</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Peticiones</h5>
                            <p class="card-text fs-3" id="totalPeticiones">{{ $totalPeticiones }}</p>
                            <a href="{{ route('peticiones.index') }}" class="text-decoration-none">Ver más</a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </main>

   
    <script>
        async function actualizarTotalPeticiones() {
            try {
                const response = await fetch("{{ route('home.totalPeticiones') }}");
                const data = await response.json();
                document.getElementById("totalPeticiones").textContent = data.totalPeticiones;
            } catch (error) {
                console.error("Error al actualizar el total de peticiones:", error);
            }
        }

        // Actualizar el total de peticiones cada 30 segundos
        setInterval(actualizarTotalPeticiones, 30000);
    </script>
@endsection
