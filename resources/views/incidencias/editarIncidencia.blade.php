@extends('layouts.app')

@section('content')
<div class="table-container mt-5">
    <h2>Editar Incidencia</h2>

    <div id="alert-container"></div>

    <form id="form-editar-incidencia" action="{{ route('incidencias.update', $incidencia->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="id_persona" id="id_persona" value="{{ $incidencia->id_persona }}">
        
        <!-- Barra de progreso -->
        <div class="progress mb-3" style="height: 20px;">
            <div id="stepProgressBar" class="progress-bar" role="progressbar" style="width: 50%;">
                Paso 1 de 3
            </div>
        </div>
        
        <!-- Paso 1: Dirección -->
        <div class="step" id="step-1">
            <div class="card border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Dirección del Incidente</h5>
                </div>
                <div class="card-body">
                    <livewire:dropdown-persona 
                        :estadoId="$incidencia->direccionIncidencia->id_estado"
                        :municipioId="$incidencia->direccionIncidencia->id_municipio"
                        :parroquiaId="$incidencia->direccionIncidencia->id_parroquia"
                        :urbanizacionId="$incidencia->direccionIncidencia->id_urbanizacion ?? null"
                        :sectorId="$incidencia->direccionIncidencia->id_sector ?? null"
                        :comunidadId="$incidencia->direccionIncidencia->id_comunidad ?? null"
                    />
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" id="calle" name="calle" class="form-control" value="{{ $incidencia->direccionIncidencia->calle }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="punto_de_referencia" class="form-label">Punto de Referencia:</label>
                            <input type="text" id="punto_de_referencia" name="punto_de_referencia" class="form-control" value="{{ $incidencia->direccionIncidencia->punto_de_referencia }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                    <a href="{{ route('incidencias.index') }}" class="btn btn-secondary btn-sm py-2 px-3">
                        Cancelar
                    </a>
                    <button type="button" class="btn btn-primary" id="next-to-step-2">Siguiente</button>
                </div>
            </div>
        </div>

        <!-- Paso 2: Institución y Estación -->
        <div class="step d-none" id="step-2">
            <div class="card border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Institución Responsable</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="institucion" class="form-label">Institución:</label>
                        <select id="institucion" name="institucion" class="form-select" required>
                            <option value="" disabled>--Seleccione una institución--</option>
                            @foreach ($instituciones as $institucion)
                                <option value="{{ $institucion->id_institucion }}" 
                                    {{ $incidencia->id_institucion == $institucion->id_institucion ? 'selected' : '' }}>
                                    {{ $institucion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estacion" class="form-label">Estación:</label>
                        <select id="estacion" name="estacion" class="form-select" required>
                            <option value="" disabled>--Seleccione una estación--</option>
                            @foreach ($estaciones as $estacion)
                                @if($estacion->id_institucion == $incidencia->id_institucion)
                                    <option value="{{ $estacion->id_institucion_estacion }}" 
                                        {{ $incidencia->id_institucion_estacion == $estacion->id_institucion_estacion ? 'selected' : '' }}>
                                        {{ $estacion->nombre }} (Municipio: {{ $estacion->municipio->nombre }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Sección de instituciones de apoyo -->
                    <div class="mb-3">
                        <button type="button" id="btn-agregar-apoyo" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo
                        </button>
                    </div>

                    <div id="contenedor-apoyo" class="{{ $incidencia->institucionesApoyo->isEmpty() ? 'd-none' : '' }}">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Institución de Apoyo</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="institucion_apoyo" class="form-label">Institución</label>
                                        <select id="institucion_apoyo" class="form-select form-select-sm">
                                            <option value="" selected>--Seleccione--</option>
                                            @foreach($instituciones as $institucion)
                                                <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="estacion_apoyo" class="form-label">Estación</label>
                                        <select id="estacion_apoyo" class="form-select form-select-sm">
                                            <option value="" selected>--Seleccione una estación--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" id="btn-agregar-item" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de instituciones de apoyo agregadas -->
                        <div class="mb-3">
                            <h6>Instituciones de Apoyo:</h6>
                            <div id="lista-apoyo" class="list-group">
                                @foreach($incidencia->institucionesApoyo as $apoyo)
                                    <div class="list-group-item d-flex justify-content-between align-items-center" 
                                         data-institucion-id="{{ $apoyo->id_institucion }}" 
                                         data-estacion-id="{{ $apoyo->id_institucion_estacion }}"
                                         data-existente="true">
                                        <div>
                                            <strong>{{ $apoyo->institucion->nombre }}</strong> - 
                                            {{ $apoyo->estacion ? $apoyo->estacion->nombre.' ('.$apoyo->estacion->municipio->nombre.')' : 'Todas las estaciones' }}
                                            <span class="badge bg-success ms-2">Asignada</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Campos ocultos para enviar los datos -->
                            <div id="campos-ocultos-apoyo">
                                @foreach($incidencia->institucionesApoyo as $apoyo)
                                    <input type="hidden" name="instituciones_apoyo[]" value="{{ $apoyo->id_institucion }}">
                                    <input type="hidden" name="estaciones_apoyo[]" value="{{ $apoyo->id_institucion_estacion ?? '' }}">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                    <button type="button" class="btn btn-secondary" id="back-to-step-1">Atrás</button>
                    <button type="button" class="btn btn-primary" id="next-to-step-3">Siguiente</button>
                </div>
            </div>
        </div>

        <!-- Paso 3: Detalles de la Incidencia -->
        <div class="step d-none" id="step-3">
            <div class="card border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Detalles de la Incidencia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tipo_incidencia" class="form-label">Tipo de Incidencia:</label>
                        <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                            <option value="" disabled>--Seleccione--</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_incidencia }}"
                                    {{ $incidencia->id_tipo_incidencia == $tipo->id_tipo_incidencia ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required>{{ $incidencia->descripcion }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="nivel_prioridad" class="form-label">Nivel de Prioridad:</label>
                        <select id="nivel_prioridad" name="nivel_prioridad" class="form-select" required>
                            @foreach($prioridades as $prioridad)
                             @if($prioridad->activo == 1)
                                <option value="{{ $prioridad->id_nivel_incidencia }}"

                                    {{ $incidencia->id_nivel_incidencia == $prioridad->id_nivel_incidencia ? 'selected' : '' }}>
                                    {{ $prioridad->nivel }}/{{$prioridad->nombre}}
                                </option>
                                @endif
                
                            @endforeach
                        </select>
                    </div>

                    <!-- NUEVO: Imágenes actuales y carga de nuevas imágenes -->
                    <div class="mb-3">
                        <label class="form-label">Imágenes actuales (máx. 3):</label>
                        <div class="row">
                            @if(isset($imagenesAntes) && $imagenesAntes->count())
                                @foreach($imagenesAntes as $img)
                                    <div class="col-md-4 mb-2 text-center">
                                        <img src="{{ asset('storage/' . $img->ruta) }}" alt="Imagen" class="img-fluid rounded border mb-1" style="max-height: 150px;">
                                        <div>
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-reemplazar-imagen" data-img-id="{{ $img->id_prueba_fotografica }}">Reemplazar</button>
                                        </div>
                                        <input type="file" name="reemplazo_imagen[{{ $img->id_prueba_fotografica }}]" accept="image/jpeg,image/png,image/jpg" class="form-control mt-1 d-none input-reemplazo-imagen" data-img-id="{{ $img->id_prueba_fotografica }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-muted">No hay imágenes cargadas.</div>
                            @endif
                        </div>
                        <small class="text-muted">Puedes reemplazar una imagen existente haciendo clic en "Reemplazar".</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Agregar nuevas imágenes (máx. 3 en total):</label>
                        @php $faltan = 3 - (isset($imagenesAntes) ? $imagenesAntes->count() : 0); @endphp
                        @for($i = 0; $i < $faltan; $i++)
                            <input type="file" name="pruebasAntes[]" accept="image/jpeg,image/png,image/jpg" class="form-control mb-1">
                        @endfor
                        <small class="text-muted">Puedes subir nuevas imágenes si el total no supera 3.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                    <button type="button" class="btn btn-secondary btn-sm" id="back-to-step-2">Atrás</button>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('js/incidencias.js') }}"></script>
<script src="{{asset('js/editarIncidencias.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botones = document.querySelectorAll('.btn-reemplazar-imagen');

        botones.forEach(boton => {
            boton.addEventListener('click', function () {
                const imgId = this.getAttribute('data-img-id');
                const inputFile = document.querySelector(`.input-reemplazo-imagen[data-img-id="${imgId}"]`);
                
                if (inputFile) {
                    inputFile.classList.remove('d-none');
                    inputFile.click(); // Opcional: abre el diálogo para seleccionar archivo automáticamente
                }
            });
        });
    });
</script>

@endsection