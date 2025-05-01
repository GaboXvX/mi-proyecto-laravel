@foreach($movimientos as $mov)
<tr class="border-bottom">
    <td class="py-3">
        <div class="d-flex flex-column">
            <span class="fw-bold">{{ $mov->created_at->format('d/m/Y') }}</span>
            <small class="text-muted">{{ $mov->created_at->format('H:i:s') }}</small>
        </div>
    </td>
    <td class="py-3">
        @php
            $tipo = '';
            $badgeClass = 'bg-secondary';
            if ($mov->id_usuario_afectado) {
                $tipo = 'Usuario';
                $badgeClass = 'bg-info';
            } elseif ($mov->id_persona) {
                $tipo = 'Persona';
                $badgeClass = 'bg-success';
            } elseif ($mov->id_direccion) {
                $tipo = 'Dirección';
                $badgeClass = 'bg-warning text-dark';
            } elseif ($mov->id_incidencia) {
                $tipo = 'Incidencia';
                $badgeClass = 'bg-danger';
            } else {
                $tipo = 'Sistema';
            }
        @endphp
        <span class="badge {{ $badgeClass }} rounded-pill p-2">
            <i class="bi 
                @if($tipo == 'Usuario') bi-person 
                @elseif($tipo == 'Persona') bi-person-badge 
                @elseif($tipo == 'Dirección') bi-geo-alt 
                @elseif($tipo == 'Incidencia') bi-exclamation-triangle 
                @else bi-gear @endif
            me-1"></i>
            {{ $tipo }}
        </span>
    </td>
    <td class="py-3">
        @if($mov->id_usuario_afectado)
            <span class="d-flex align-items-center">
                <i class="bi bi-person me-2 text-info"></i>
                Usuario: {{ $mov->usuarioAfectado->nombre_usuario ?? 'N/A' }}
            </span>
        @elseif($mov->id_persona)
            <span class="d-flex align-items-center">
                <i class="bi bi-person-badge me-2 text-success"></i>
                Persona C.I {{ $mov->persona->cedula ?? 'N/A' }}
            </span>
        @elseif($mov->id_direccion)
            <span class="d-flex align-items-center">
                <i class="bi bi-geo-alt me-2 text-warning"></i>
                Dirección #{{ $mov->id_direccion }}
            </span>
        @elseif($mov->id_incidencia)
            <span class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                Incidencia #{{ $mov->incidencia->cod_incidencia ?? 'N/A' }}
            </span>
        @else
            <span class="text-muted">Sistema</span>
        @endif
    </td>
    <td class="py-3">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                {{ Str::limit($mov->descripcion, 80) }}
                @if(strlen($mov->descripcion) > 80)
                    <a href="#" class="text-primary ms-2" data-bs-toggle="tooltip" title="{{ $mov->descripcion }}">
                        <i class="bi bi-eye-fill"></i>
                    </a>
                @endif
            </div>
        </div>
    </td>
    <td class="py-3">
        <button class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="tooltip" title="Ver detalles">
            <i class="bi bi-three-dots"></i>
        </button>
    </td>
</tr>
@endforeach