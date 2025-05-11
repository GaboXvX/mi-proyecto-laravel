<div>
    <div class="mb-3">
        <label for="parroquia" class="form-label">Parroquia:</label>
        <select id="parroquia" class="form-select" wire:model="parroquiasId" required>
            <option value="">Seleccione una parroquia</option>
            @foreach($parroquias as $parroquia)
                <option value="{{ $parroquia->id_parroquia }}">{{ $parroquia->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="urbanizacion" class="form-label">Urbanización:</label>
        <select id="urbanizacion" class="form-select" wire:model="urbanizacionesId" required>
            <option value="">Seleccione una urbanización</option>
            @foreach($urbanizaciones as $urbanizacion)
                <option value="{{ $urbanizacion->id_urbanizacion }}">{{ $urbanizacion->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="sector" class="form-label">Sector:</label>
        <select id="sector" class="form-select" wire:model="sectoresId" required>
            <option value="">Seleccione un sector</option>
            @foreach($sectores as $sector)
                <option value="{{ $sector->id_sector }}">{{ $sector->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="comunidad" class="form-label">Comunidad:</label>
        <select id="comunidad" class="form-select" wire:model="comunidadesId" required>
            <option value="">Seleccione una comunidad</option>
            @foreach($comunidades as $comunidad)
                <option value="{{ $comunidad->id_comunidad }}">{{ $comunidad->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>
