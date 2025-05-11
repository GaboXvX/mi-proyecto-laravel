@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Nuevo Personal de Reparación</h1>
    
    <form action="{{ route('personal-reparacion.store') }}" method="POST">
        @csrf
         
        <div class="form-group">
            <label for="id_institucion">Institución</label>
            <select name="id_institucion" id="id_institucion" class="form-control" required>
                <option value="">Seleccione una institución</option>
                @foreach($instituciones as $institucion)
                    <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="id_institucion_estacion">Estación </label>
            <select name="id_institucion_estacion" id="id_institucion_estacion" class="form-control" required>
                <option value="">Seleccione una estación</option>
                <!-- Se llenará con AJAX -->
            </select>
        </div>
        
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" class="form-control" required>
        </div>
        <div class="form-group">
                            <label for="nacionalidad" class="form-label small mb-0">Nacionalidad <span class="text-danger">*</span></label>
                        </div>
        <div class="form-group">
                            <select name="nacionalidad" id="nacionalidad" class="form-select form-select-sm py-2" required onchange="limpiarAlertaEmpleado()"> {{-- MODIFICADO --}}
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="V">Venezolano (V)</option>
                                <option value="E">Extranjero (E)</option>
                            </select>
                        </div>
        
        <div class="form-group">
            <label for="cedula">Cédula</label>
            <input type="text" name="cedula" id="cedula" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('personal-reparacion.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const institucionSelect = document.getElementById('id_institucion');
    const estacionSelect = document.getElementById('id_institucion_estacion');

    async function cargarEstaciones(institucionId) {
        try {
            // Resetear el select
            estacionSelect.innerHTML = '<option value="">Seleccione una estación</option>';
            
            if (!institucionId) return;
            
            // Mostrar estado de carga
            estacionSelect.disabled = true;
            const loadingOption = document.createElement('option');
            loadingOption.value = '';
            loadingOption.textContent = 'Cargando estaciones...';
            estacionSelect.appendChild(loadingOption);

            // Hacer la petición
            const response = await fetch(`/personal-reparacion/estaciones/${institucionId}`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Limpiar y agregar nuevas opciones
            estacionSelect.innerHTML = '<option value="">Seleccione una estación</option>';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(estacion => {
                    const option = new Option(
                        estacion.nombre + (estacion.codigo ? ` (${estacion.codigo})` : ''),
                        estacion.id
                    );
                    estacionSelect.add(option);
                });
            } else {
                estacionSelect.innerHTML = '<option value="">No se encontraron estaciones</option>';
            }
        } catch (error) {
            console.error('Error al cargar estaciones:', error);
            estacionSelect.innerHTML = '<option value="">Error al cargar estaciones</option>';
        } finally {
            estacionSelect.disabled = false;
        }
    }

    // Event listener
    institucionSelect.addEventListener('change', function() {
        cargarEstaciones(this.value);
    });
});
</script>
@endsection