<div>
    <!-- Dropdown de Estados -->
    <div class="mb-3">
        <label for="estado" class="form-label">Estado:</label>
        <select name="estado" id="estado" class="form-select" wire:model.live="estadoId" required>
            <option value="">Seleccione un estado</option>
            @foreach($estados as $estado)
                <option value="{{ $estado->id_estado }}">{{ $estado->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Dropdown de Municipios -->
    <div class="mb-3">
        <label for="municipio" class="form-label">Municipio:</label>
        <select name="municipio" id="municipio" class="form-select" wire:model.live="municipioId" required>
            <option value="">Seleccione un municipio</option>
            @foreach($municipios as $municipio)
                <option value="{{ $municipio->id_municipio }}">{{ $municipio->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Dropdown de Parroquias -->
    <div class="mb-3">
        <label for="parroquia" class="form-label">Parroquia:</label>
        <select name="parroquia" id="parroquia" class="form-select" wire:model.live="parroquiaId" required>
            <option value="">Seleccione una parroquia</option>
            @foreach($parroquias as $parroquia)
                <option value="{{ $parroquia->id_parroquia }}">{{ $parroquia->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Dropdown de Urbanizaciones -->
    <div class="mb-3">
        <label for="urbanizacion" class="form-label">Urbanización:</label>
        <select name="urbanizacion" id="urbanizacion" class="form-select" wire:model.live="urbanizacionId" required>
            <option value="">Seleccione una urbanización</option>
            @foreach($urbanizaciones as $urbanizacion)
                <option value="{{ $urbanizacion->id_urbanizacion }}">{{ $urbanizacion->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Dropdown de Sectores -->
    <div class="mb-3">
        <label for="sector" class="form-label">Sector:</label>
        <select name="sector" id="sector" class="form-select" wire:model.live="sectorId" required>
            <option value="">Seleccione un sector</option>
            @foreach($sectores as $sector)
                <option value="{{ $sector->id_sector }}">{{ $sector->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Dropdown de Comunidades -->
    <div class="mb-3">
        <label for="comunidad" class="form-label">Comunidad:</label>
        <select name="comunidad" id="comunidad" class="form-select" wire:model.live="comunidadId" required>
            <option value="">Seleccione una comunidad</option>
            @foreach($comunidades as $comunidad)
                <option value="{{ $comunidad->id_comunidad }}">{{ $comunidad->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>