<div>
    <div class="row mb-3">
        <!-- Dropdown de Estados -->
        <div class="col-md-6">
            <label for="estado" class="form-label">Estado:</label>
            <select name="estado" id="estado" class="form-select" wire:model.live="estadoId" required>
                <option value="">Seleccione un estado</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->id_estado }}" {{ $estado->id_estado == $estadoId ? 'selected' : '' }}>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Dropdown de Municipios -->
        <div class="col-md-6">
            <label for="municipio" class="form-label">Municipio:</label>
            <select name="municipio" id="municipio" class="form-select" wire:model.live="municipioId" required>
                <option value="">Seleccione un municipio</option>
                @foreach($municipios as $municipio)
                    <option value="{{ $municipio->id_municipio }}" {{ $municipio->id_municipio == $municipioId ? 'selected' : '' }}>
                        {{ $municipio->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <!-- Dropdown de Parroquias -->
        <div class="col-md-6">
            <label for="parroquia" class="form-label">Parroquia:</label>
            <select name="parroquia" id="parroquia" class="form-select" wire:model.live="parroquiaId" required>
                <option value="">Seleccione una parroquia</option>
                @foreach($parroquias as $parroquia)
                    <option value="{{ $parroquia->id_parroquia }}" {{ $parroquia->id_parroquia == $parroquiaId ? 'selected' : '' }}>
                        {{ $parroquia->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Dropdown de Urbanizaciones -->
        <div class="col-md-6">
            <label for="urbanizacion" class="form-label">Urbanización:</label>
            <select name="urbanizacion" id="urbanizacion" class="form-select" wire:model.live="urbanizacionId" required>
                <option value="">Seleccione una urbanización</option>
                @foreach($urbanizaciones as $urbanizacion)
                    <option value="{{ $urbanizacion->id_urbanizacion }}" {{ $urbanizacion->id_urbanizacion == $urbanizacionId ? 'selected' : '' }}>
                        {{ $urbanizacion->nombre }}
                    </option>
                @endforeach
                <option value="agregar_nueva_urbanizacion">Agregar nueva urbanización...</option>
            </select>
            @if($urbanizacionId === 'agregar_nueva_urbanizacion')
                <div class="input-group mt-2">
                    <input type="text" class="form-control" placeholder="Nombre de la nueva urbanización" wire:model.defer="nuevaUrbanizacionNombre">
                    <button class="btn btn-success" type="button" wire:click="crearUrbanizacion">Agregar</button>
                </div>
                @error('nuevaUrbanizacionNombre')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                @if($mensajeUrbanizacion)
                    <div class="text-success small">{{ $mensajeUrbanizacion }}</div>
                @endif
            @endif
        </div>
    </div>
    
    <div class="row mb-3">
        <!-- Dropdown de Sectores -->
        <div class="col-md-6">
            <label for="sector" class="form-label">Sector:</label>
            <select name="sector" id="sector" class="form-select" wire:model.live="sectorId" required>
                <option value="">Seleccione un sector</option>
                @foreach($sectores as $sector)
                    <option value="{{ $sector->id_sector }}" {{ $sector->id_sector == $sectorId ? 'selected' : '' }}>
                        {{ $sector->nombre }}
                    </option>
                @endforeach
                <option value="agregar_nuevo_sector">Agregar nuevo sector...</option>
            </select>
            @if($sectorId === 'agregar_nuevo_sector')
                <div class="input-group mt-2">
                    <input type="text" class="form-control" placeholder="Nombre del nuevo sector" wire:model.defer="nuevoSectorNombre">
                    <button class="btn btn-success" type="button" wire:click="crearSector">Agregar</button>
                </div>
                @error('nuevoSectorNombre')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                @if($mensajeSector)
                    <div class="text-success small">{{ $mensajeSector }}</div>
                @endif
            @endif
        </div>

        <!-- Dropdown de Comunidades -->
        <div class="col-md-6">
            <label for="comunidad" class="form-label">Comunidad:</label>
            <select name="comunidad" id="comunidad" class="form-select" wire:model.live="comunidadId" required>
                <option value="">Seleccione una comunidad</option>
                @foreach($comunidades as $comunidad)
                    <option value="{{ $comunidad->id_comunidad }}" {{ $comunidad->id_comunidad == $comunidadId ? 'selected' : '' }}>
                        {{ $comunidad->nombre }}
                    </option>
                @endforeach
                <option value="agregar_nueva_comunidad">Agregar nueva comunidad...</option>
            </select>
            @if($comunidadId === 'agregar_nueva_comunidad')
                <div class="input-group mt-2">
                    <input type="text" class="form-control" placeholder="Nombre de la nueva comunidad" wire:model.defer="nuevaComunidadNombre">
                    <button class="btn btn-success" type="button" wire:click="crearComunidad">Agregar</button>
                </div>
                @error('nuevaComunidadNombre')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                @if($mensajeComunidad)
                    <div class="text-success small">{{ $mensajeComunidad }}</div>
                @endif
            @endif
        </div>
    </div>
</div>