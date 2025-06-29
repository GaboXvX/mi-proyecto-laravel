@extends('layouts.app')

@section('content')
<div class="table-container mt-5">
    <h2 class="mb-4">Registrar Incidencia </h2>

    <div id="alert-container"></div>
    
    <form id="incidenciaGeneralForm" action="{{ route('incidencias.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Si hay persona vinculada, incluir el campo oculto -->
        @if(isset($persona))
            <input type="hidden" name="id_persona" value="{{ $persona->id_persona }}" />
        @endif

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
                    <livewire:dropdown-persona />
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="calle" class="form-label">Calle:</label>
                            <input type="text" id="calle" name="calle" class="form-control" value="" required>
                        </div>
                        <div class="col-md-6">
                            <label for="punto_de_referencia" class="form-label">Punto de Referencia:</label>
                            <input type="text" id="punto_de_referencia" name="punto_de_referencia" class="form-control" value="" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between m-3">
                   <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('incidencias.index') }}" class="btn btn-secondary btn-sm py-2 px-3">
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
                            <option value="" disabled selected>--Seleccione una institución--</option>
                            @foreach ($instituciones as $institucion)
                                <option value="{{ $institucion->id_institucion }}">{{ $institucion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estacion" class="form-label">Unidad:</label>
                        <select id="estacion" name="estacion" class="form-select" required>
                            <option value="" disabled selected>--Seleccione una Unidad--</option>
                            <!-- Las estaciones se cargarán dinámicamente por JS -->
                        </select>
                    </div>

                    <!-- Sección de instituciones de apoyo -->
                    <div class="mb-3">
                        <button type="button" id="btn-agregar-apoyo" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Agregar Institución de Apoyo
                        </button>
                    </div>

                    <div id="contenedor-apoyo" class="d-none">
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
                                        <label for="estacion_apoyo" class="form-label">Unidad</label>
                                        <select id="estacion_apoyo" class="form-select form-select-sm">
                                            <option value="" selected>--Seleccione una Unidad--</option>
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
                                <!-- Se agregan dinámicamente -->
                            </div>
                            <!-- Campos ocultos para enviar los datos -->
                            <div id="campos-ocultos-apoyo">
                                <!-- Se agregan dinámicamente -->
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
                            <option value="" disabled selected>--Seleccione--</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_incidencia }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="nivel_prioridad" class="form-label">Nivel de Prioridad:</label>
                        <select id="nivel_prioridad" name="nivel_prioridad" class="form-select" required>
                            <option value="" disabled selected>--Seleccione--</option>
                            @foreach($prioridades as $prioridad)
                                @if($prioridad->activo == 1)
                                    <option value="{{ $prioridad->id_nivel_incidencia }}">{{ $prioridad->nivel }}/{{$prioridad->nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Imágenes -->
                    <div class="mb-3">
                        <label class="form-label">Agregar imágenes (máx. 3):</label>
                        <div id="preview-imagenes" class="row mb-2">
                            @for($i = 0; $i < 3; $i++)
                                <div class="col-md-4 mb-2 text-center" data-index="{{$i}}">
                                    <img src="" class="img-fluid rounded border mb-1 d-none" style="max-height: 120px;" alt="Previsualización" id="img-preview-{{$i}}">
                                    <input type="file" name="pruebasAntes[]" accept="image/jpeg,image/png,image/jpg" class="form-control mb-1 input-imagen" data-index="{{$i}}">
                                    <button type="button" class="btn btn-outline-danger btn-sm mt-1 d-none" id="btn-quitar-{{$i}}"><i class="bi bi-x"></i> Quitar</button>
                                </div>
                            @endfor
                        </div>
                        <small class="text-muted">Puedes subir hasta 3 imágenes.</small>
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
<script src="{{asset('js/sweetalert2.min.js')}}"></script>
<script src="{{asset('js/registroIncidencias.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    for(let i=0; i<3; i++) {
        const input = document.querySelector(`.input-imagen[data-index='${i}']`);
        const img = document.getElementById(`img-preview-${i}`);
        const btnQuitar = document.getElementById(`btn-quitar-${i}`);
        input.addEventListener('change', function() {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                    btnQuitar.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                img.src = '';
                img.classList.add('d-none');
                btnQuitar.classList.add('d-none');
            }
        });
        btnQuitar.addEventListener('click', function() {
            input.value = '';
            img.src = '';
            img.classList.add('d-none');
            btnQuitar.classList.add('d-none');
        });
    }
});
</script>

@endsection