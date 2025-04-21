@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Mis Notificaciones</h5>
            @if($notificaciones->filter(fn($n) => !$n->pivot->leido)->count() > 0)
                <form action="{{ route('notificaciones.marcar-todas-leidas') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        Marcar todas como leídas
                    </button>
                </form>
            @else
                <button class="btn btn-sm btn-secondary" disabled>
                    Todas leídas
                </button>
            @endif
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50px"></th>
                            <th>Notificación</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notificaciones as $notificacion)
                        <tr class="{{ $notificacion->pivot->leido ? '' : 'table-active' }}" 
                            data-notification-id="{{ $notificacion->id_notificacion }}">
                            <td class="text-center">
                                @if(!$notificacion->pivot->leido)
                                <span class="badge bg-primary rounded-circle" style="width: 12px; height: 12px;"></span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong class="{{ $notificacion->pivot->leido ? 'text-dark' : 'text-primary' }}">
                                        {{ $notificacion->titulo }}
                                    </strong>
                                    <small class="{{ $notificacion->pivot->leido ? 'text-muted' : 'text-dark' }}">{{ $notificacion->mensaje }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="{{ $notificacion->pivot->leido ? 'text-muted' : 'text-dark' }}">
                                    {{ ucfirst(str_replace('_', ' ', $notificacion->tipo_notificacion)) }}
                                </span>
                            </td>
                            <td>
                                <span title="{{ $notificacion->created_at->format('d/m/Y H:i') }}" class="{{ $notificacion->pivot->leido ? 'text-muted' : 'text-dark' }}">
                                    {{ $notificacion->created_at->diffForHumans() }}
                                </span>
                            </td>
                            <td>
                                @if(!$notificacion->pivot->leido)
                                <a href="{{ route('notificaciones.marcar-leida', $notificacion->id_notificacion) }}" 
                                   class="btn btn-sm btn-outline-secondary marcar-leida"
                                   title="Marcar como leída">
                                    <i class="bi bi-check2"></i>
                                </a>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="bi bi-check2"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No tienes notificaciones
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($notificaciones->hasPages())
            <div class="card-footer">
                {{ $notificaciones->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marcar como leído al hacer clic en la fila completa
    document.querySelectorAll('tbody tr[data-notification-id]').forEach(row => {
        row.addEventListener('click', function(e) {
            // Evitar si se hizo clic en un botón o enlace
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            
            const notificationId = this.getAttribute('data-notification-id');
            if (notificationId && !this.classList.contains('table-active')) {
                fetch(`/notificaciones/marcar-leida/${notificationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        this.classList.remove('table-active');
                        const unreadBadge = this.querySelector('.badge.bg-primary');
                        if (unreadBadge) {
                            unreadBadge.remove();
                        }
                        
                        // Actualizar colores del texto
                        const title = this.querySelector('strong');
                        if (title) title.classList.remove('text-primary');
                        if (title) title.classList.add('text-dark');
                        
                        const message = this.querySelector('small');
                        if (message) message.classList.remove('text-dark');
                        if (message) message.classList.add('text-muted');
                        
                        const type = this.querySelector('td:nth-child(3) span');
                        if (type) type.classList.remove('text-dark');
                        if (type) type.classList.add('text-muted');
                        
                        const date = this.querySelector('td:nth-child(4) span');
                        if (date) date.classList.remove('text-dark');
                        if (date) date.classList.add('text-muted');
                        
                        // Actualizar el botón de acción individual
                        const actionCell = this.querySelector('td:nth-child(5) .marcar-leida');
                        if (actionCell) {
                            actionCell.outerHTML = `
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="bi bi-check2"></i>
                                </button>
                            `;
                        }
                        
                        // Verificar si quedan notificaciones sin leer
                        updateMarkAllButton();
                    }
                });
            }
        });
    });
    
    // Función para actualizar el botón "Marcar todas como leídas"
    function updateMarkAllButton() {
        const unreadCount = document.querySelectorAll('tbody tr.table-active').length;
        const markAllButton = document.querySelector('.card-header button[type="submit"]');
        
        if (unreadCount === 0) {
            if (markAllButton) {
                markAllButton.disabled = true;
                markAllButton.classList.remove('btn-primary');
                markAllButton.classList.add('btn-secondary');
                markAllButton.textContent = 'Todas leídas';
            }
        }
    }
    
    // Ejecutar al cargar la página
    updateMarkAllButton();
});
</script>
@endsection