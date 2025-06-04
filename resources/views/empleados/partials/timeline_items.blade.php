@if($creacionUsuario)
<!-- Creación de usuario -->
<div class="timeline-item timeline-creation">
    <div class="timeline-point timeline-point-primary">
        <i class="fas fa-user-plus"></i>
    </div>
    <div class="timeline-event">
        <div class="timeline-heading">
            <h6>Solicitud de acceso creada</h6>
            <div class="timeline-date">
                <i class="far fa-clock"></i>
                {{ $creacionUsuario->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="timeline-content">
            Solicitud de acceso al sistema registrada
        </div>
    </div>
</div>
@endif

@foreach($historial as $item)
    <div class="timeline-item timeline-{{ $item['tipo'] }}">
        <div class="timeline-point timeline-point-{{ 
            $item['tipo'] == 'retiro' ? 'danger' : 
            ($item['tipo'] == 'incorporacion' ? 'success' : 
            ($item['tipo'] == 'aceptacion' ? 'success' : 
            ($item['tipo'] == 'rechazo' ? 'danger' : 'primary')))
        }}">
            <i class="fas fa-{{
                $item['tipo'] == 'retiro' ? 'user-minus' : 
                ($item['tipo'] == 'incorporacion' ? 'user-plus' : 
                ($item['tipo'] == 'aceptacion' ? 'check-circle' : 
                ($item['tipo'] == 'rechazo' ? 'times-circle' : 'user-plus')))
            }}"></i>
        </div>
        <div class="timeline-event">
            <div class="timeline-heading">
                <h6>{{ $item['data']['titulo'] }}</h6>
                <div class="timeline-date">
                    <i class="far fa-clock"></i>
                    {{ $item['fecha']->format('d/m/Y H:i') }}
                </div>
                @if(isset($item['data']['usuario']))
                    <small class="text-muted">
                        Por: {{ $item['data']['usuario'] }}
                    </small>
                @endif
            </div>
            <div class="timeline-content">
                {{ $item['data']['descripcion'] }}
            </div>
        </div>
    </div>
@endforeach