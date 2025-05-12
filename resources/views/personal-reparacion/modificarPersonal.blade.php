@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2>Editar Personal de Reparación</h2>
    
    <form action="{{ route('personal-reparacion.update', $personalReparacion->id_personal_reparacion) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="id_usuario">Usuario Asociado</label>
            <select name="id_usuario" id="id_usuario" class="form-control" required>
                <option value="">Seleccione un usuario</option>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id_usuario }}" {{ $personalReparacion->id_usuario == $usuario->id_usuario ? 'selected' : '' }}>{{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="id_institucion">Institución</label>
            <select name="id_institucion" id="id_institucion" class="form-control" required>
                <option value="">Seleccione una institución</option>
                @foreach($instituciones as $institucion)
                    <option value="{{ $institucion->id_institucion }}" {{ $personalReparacion->id_institucion == $institucion->id_institucion ? 'selected' : '' }}>{{ $institucion->nombre }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="id_institucion_estacion">Estación (Opcional)</label>
            <select name="id_institucion_estacion" id="id_institucion_estacion" class="form-control">
                <option value="">Seleccione una estación</option>
                @foreach($estaciones as $estacion)
                    <option value="{{ $estacion->id_institucion_estacion }}" {{ $personalReparacion->id_institucion_estacion == $estacion->id_institucion_estacion ? 'selected' : '' }}>{{ $estacion->nombre }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control solo-letras" maxlength="12" value="{{ $personalReparacion->nombre }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" id="apellido" class="form-control solo-letras" maxlength="12" value="{{ $personalReparacion->apellido }}" required>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="nacionalidad">Nacionalidad</label>
            <input type="text" name="nacionalidad" id="nacionalidad" class="form-control solo-letras" value="{{ $personalReparacion->nacionalidad }}" required>
        </div>
        
        <div class="form-group">
            <label for="cedula">Cédula</label>
            <input type="text" name="cedula" id="cedula" class="form-control solo-letras" maxlength="8" value="{{ $personalReparacion->cedula }}" required>
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" class="form-control" value="{{ $personalReparacion->telefono }}" required>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('personal-reparacion.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const institucionSelect = document.getElementById('id_institucion');
    const estacionSelect = document.getElementById('id_institucion_estacion');
    const currentEstacionId = "{{ $personalReparacion->id_institucion_estacion }}";

    // Función para cargar estaciones
    function cargarEstaciones(institucionId) {
        if (!institucionId) {
            estacionSelect.innerHTML = '<option value="">Seleccione una estación</option>';
            return;
        }

        // Mostrar estado de carga
        estacionSelect.disabled = true;
        const loadingOption = document.createElement('option');
        loadingOption.value = '';
        loadingOption.textContent = 'Cargando estaciones...';
        estacionSelect.innerHTML = '';
        estacionSelect.appendChild(loadingOption);

        // Hacer la petición AJAX
        fetch(`/personal-reparacion/estaciones/${institucionId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar estaciones');
                }
                return response.json();
            })
            .then(data => {
                // Limpiar y cargar nuevas opciones
                estacionSelect.innerHTML = '<option value="">Seleccione una estación</option>';
                
                data.forEach(estacion => {
                    const option = new Option(estacion.nombre, estacion.id_institucion_estacion);
                    estacionSelect.add(option);
                });

                // Restaurar selección previa si existe
                if (currentEstacionId && data.some(e => e.id_institucion_estacion == currentEstacionId)) {
                    estacionSelect.value = currentEstacionId;
                }

                estacionSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                estacionSelect.innerHTML = '<option value="">Error al cargar estaciones</option>';
            });
    }

    // Event listener para cambio de institución
    institucionSelect.addEventListener('change', function() {
        cargarEstaciones(this.value);
    });

    // Cargar estaciones al inicio si ya hay una institución seleccionada
    if (institucionSelect.value) {
        cargarEstaciones(institucionSelect.value);
    }
});
</script>
@endsection