@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="table-container p-4 shadow" style="width: 100%; max-width: 600px;">
        <h2 class="text-center mb-4">Editar Personal de Reparación</h2>
        
        <form action="{{ route('personal-reparacion.update', $personalReparacion) }}" method="POST">
            @csrf
            @method('PUT')
            
           
            
            <div class="form-group mb-2">
                <label for="id_institucion">Institución</label>
                <select name="id_institucion" id="id_institucion" class="form-control form-control-sm" required>
                    <option value="">Seleccione una institución</option>
                    @foreach($instituciones as $institucion)
                        <option value="{{ $institucion->id_institucion }}" {{ $personalReparacion->id_institucion == $institucion->id_institucion ? 'selected' : '' }}>
                            {{ $institucion->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mb-2">
                <label for="id_institucion_estacion">Estación (Opcional)</label>
                <select name="id_institucion_estacion" id="id_institucion_estacion" class="form-control form-control-sm">
                    <option value="">Seleccione una estación</option>
                    @foreach($estaciones as $estacion)
                        <option value="{{ $estacion->id_institucion_estacion }}" {{ $personalReparacion->id_institucion_estacion == $estacion->id_institucion_estacion ? 'selected' : '' }}>
                            {{ $estacion->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="row">
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control form-control-sm solo-letras" maxlength="12" value="{{ $personalReparacion->nombre }}" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group mb-2">
                        <label for="apellido">Apellido</label>
                        <input type="text" name="apellido" id="apellido" class="form-control form-control-sm solo-letras" maxlength="12" value="{{ $personalReparacion->apellido }}" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-2">
                <label for="nacionalidad">Nacionalidad</label>
                <input type="text" name="nacionalidad" id="nacionalidad" class="form-control form-control-sm solo-letras" value="{{ $personalReparacion->nacionalidad }}" required>
            </div>
            
            <div class="form-group mb-2">
                <label for="cedula">Cédula</label>
                <input type="text" name="cedula" id="cedula" class="form-control form-control-sm solo-letras" maxlength="8" value="{{ $personalReparacion->cedula }}" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control form-control-sm" value="{{ $personalReparacion->telefono }}" required>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('personal-reparacion.index') }}" class="btn btn-sm btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const institucionSelect = document.getElementById('id_institucion');
    const estacionSelect = document.getElementById('id_institucion_estacion');
    const currentEstacionId = "{{ $personalReparacion->id_institucion_estacion }}";

    // Función para cargar estaciones
     function cargarEstaciones(institucionId) {
        if (!institucionId) {
            estacionSelect.innerHTML = '<option value="">Primero seleccione una institución</option>';
            estacionSelect.disabled = true;
            return;
        }

        estacionSelect.disabled = true;
        estacionSelect.innerHTML = '<option value="">Cargando estaciones...</option>';

        const url = `/personal-reparacion/estaciones/${institucionId}`;
        console.log('Cargando estaciones desde:', url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(({ success, data }) => {
                estacionSelect.innerHTML = '<option value="">Seleccione una estación</option>';

                if (success && data.length > 0) {
                    data.forEach(estacion => {
                        const nombre = estacion.codigo ? `${estacion.nombre} (${estacion.codigo})` : estacion.nombre;
                        const option = new Option(nombre, estacion.id);
                        estacionSelect.add(option);
                    });
                    estacionSelect.disabled = false;
                } else {
                    estacionSelect.innerHTML = '<option value="">No hay estaciones disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar estaciones:', error);
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