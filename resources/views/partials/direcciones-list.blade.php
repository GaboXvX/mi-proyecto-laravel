@foreach($direcciones as $direccion)
    <tr id="direccion_{{ $direccion->id_direccion }}">
        <td>{{ $direccion->estado->nombre }}</td>
        <td>{{ $direccion->municipio->nombre }}</td>
        <td>{{ $direccion->parroquia->nombre }}</td>
        <td>{{ $direccion->urbanizacion->nombre }}</td>
        <td>{{ $direccion->sector->nombre }}</td>
        <td>{{ $direccion->comunidad->nombre }}</td>
        <td>{{ $direccion->calle }}</td>
        <td>{{ $direccion->manzana }}</td>
        <td>{{ $direccion->numero_de_vivienda }}</td>
        <td>{{ $direccion->bloque }}</td>
        <td>
            <span class="{{ $direccion->es_principal ? 'text-success' : 'text-danger' }}">
                {{ $direccion->es_principal ? 'Sí' : 'No' }}
            </span>
        </td>
        <td>
            <span class="{{ $direccion->esLider ? 'text-success' : 'text-danger' }}">
                {{ $direccion->esLider ? 'Sí' : 'No' }}
            </span>
        </td>
        <td>
            <div class="d-flex flex-column">
                <!-- Botón de Modificar -->
                <button type="button" class="btn btn-warning btn-sm mb-1 edit-btn" data-id="{{ $direccion->id_direccion }}" 
                        data-estado="{{ $direccion->estado }}" data-municipio="{{ $direccion->municipio }}"
                        data-parroquia="{{ $direccion->parroquia->nombre }}" data-urbanizacion="{{ $direccion->urbanizacion->nombre }}"
                        data-sector="{{ $direccion->sector->nombre }}" data-comunidad="{{ $direccion->comunidad->nombre }}"
                        data-calle="{{ $direccion->calle }}" data-manzana="{{ $direccion->manzana }}"
                        data-numero-de-vivienda="{{ $direccion->numero_de_vivienda }}" data-bloque="{{ $direccion->bloque }}"
                        data-id-persona="{{ $direccion->id_persona }}"
                        data-bs-toggle="modal" data-bs-target="#editDireccionModal">
                    Modificar
                </button>
                <!-- Botón de Marcar como Principal -->
                @if(!$direccion->es_principal)
                    <form action="{{ route('personas.marcarPrincipal') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="id_direccion" value="{{ $direccion->id_direccion }}">
                        <button type="submit" class="btn btn-primary btn-sm mark-principal-btn" title="Marcar como principal">
                            <i class="bi bi-arrow-up-circle"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@endforeach
