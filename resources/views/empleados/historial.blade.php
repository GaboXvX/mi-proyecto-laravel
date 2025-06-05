@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center position-relative">
                        <h5 class="mb-0">Historial de {{ $empleado->nombre }} {{ $empleado->apellido }}</h5>
                        <span class="badge badge-secondary text-secondary">{{ $empleado->nacionalidad }}-{{ $empleado->cedula }}</span>
                        <a href="{{ route('empleados.historial.pdf', $empleado->id_empleado_autorizado) }}" type="button" class="btn btn-primary" title="Descargar Historial">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-arrow-down" viewBox="0 0 16 16">
                            <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293z"/>
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenedor principal con la línea de tiempo -->
            <div class="compact-timeline">
                <!-- Línea vertical central -->
                <div class="timeline-line"></div>
                
                <!-- Contenedor de items -->
                <div id="timeline-items">
                    @foreach($historial as $evento)
                        <div class="timeline-item timeline-{{ $evento['tipo'] }}">
                            <div class="timeline-point timeline-point-{{ $evento['color'] }}">
                                <i class="fas fa-{{ $evento['icono'] }}"></i>
                            </div>
                            <div class="timeline-event">
                                <div class="timeline-heading">
                                    <h6>{{ $evento['titulo'] }}</h6>
                                    <div class="timeline-date">
                                        <i class="far fa-clock"></i>
                                        {{ $evento['fecha']->format('d/m/Y') }}
                                    </div>
                                    @isset($evento['usuario'])
                                        <small class="text-muted">
                                            Por: {{ $evento['usuario'] }}
                                        </small>
                                    @endisset
                                </div>
                                <div class="timeline-content">
                                    {{ $evento['descripcion'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <div id="timeline-pagination">
                        {{ $historial->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="position-absolute bottom-0 end-0 m-3">
        <div class="d-flex align-items-center mb-1">
            <div class="me-2 rounded-circle" style="width: 15px; height: 15px; background-color: #e3342f;"></div>
            <span>Retirada</span>
        </div>
        <div class="d-flex align-items-center mb-1">
            <div class="me-2 rounded-circle" style="width: 15px; height: 15px; background-color: #38c172;"></div>
            <span>Incorporación</span>
        </div>
        <div class="d-flex align-items-center">
            <div class="me-2 rounded-circle" style="width: 15px; height: 15px; background-color: #3490dc;"></div>
            <span>Registro</span>
        </div>
    </div>
</div>

<style>
    /* Estructura compacta */
    .compact-timeline {
        position: relative;
        padding: 0 20px;
    }

    /* Línea vertical central */
    .timeline-line {
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #3490dc, #6cb2eb, #38c172);
        transform: translateX(-50%);
        z-index: 0;
    }

    /* Items de la línea de tiempo */
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        min-height: 60px;
        z-index: 1;
        animation: fadeIn 0.5s ease-out forwards;
    }

    /* Puntos de la línea de tiempo */
    .timeline-point {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .timeline-point:hover {
        transform: translateX(-50%) scale(1.1);
    }

    .timeline-point i {
        font-size: 0.8rem;
    }

    .timeline-point-primary {
        background-color: #3490dc;
        border: 2px solid #a0c6f7;
    }

    .timeline-point-success {
        background-color: #38c172;
        border: 2px solid #a6e4c0;
    }

    .timeline-point-danger {
        background-color: #e3342f;
        border: 2px solid #f3a6a4;
    }

    .timeline-point-info {
        background-color: #17a2b8;
        border: 2px solid #79dfec;
    }

    /* Eventos */
    .timeline-event {
        width: 45%;
        background-color: light-dark(white, #1e293b);
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .timeline-event:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .timeline-item:nth-child(odd) .timeline-event {
        margin-right: auto;
        margin-left: 5%;
    }

    .timeline-item:nth-child(even) .timeline-event {
        margin-left: auto;
        margin-right: 5%;
    }

    /* Contenido de los eventos */
    .timeline-heading h6 {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .timeline-date {
        font-size: 0.75rem;
        color: #6c757d;
        display: flex;
        align-items: center;
    }

    .timeline-date i {
        margin-right: 5px;
    }

    .timeline-content {
        font-size: 0.85rem;
        margin-top: 8px;
        padding: 8px;
        border-radius: 4px;
    }

    /* Estilos específicos para cada tipo de evento */
    .timeline-creacion_empleado .timeline-content {
        background-color: #f8f9fa;
        border-left: 3px solid #3490dc;
    }

    .timeline-creacion_usuario .timeline-content {
        background-color: #e7f5ff;
        border-left: 3px solid #17a2b8;
    }

    .timeline-incorporacion .timeline-content,
    .timeline-aceptacion .timeline-content {
        background-color: #e6ffed;
        border-left: 3px solid #38c172;
    }

    .timeline-retiro .timeline-content,
    .timeline-rechazo .timeline-content {
        background-color: #ffebee;
        border-left: 3px solid #e3342f;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Paginación */
    .pagination {
        flex-wrap: wrap;
    }

    .page-item.active .page-link {
        background-color: #3490dc;
        border-color: #3490dc;
    }

    .page-link {
        color: #3490dc;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .timeline-event {
            width: 80%;
        }
        
        .timeline-item:nth-child(odd) .timeline-event,
        .timeline-item:nth-child(even) .timeline-event {
            margin-left: auto;
            margin-right: auto;
        }
        
        .timeline-line {
            left: 20px;
        }
        
        .timeline-point {
            left: 20px;
        }
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    // Paginación AJAX
    $(document).on('click', '#timeline-pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {
                $('#timeline-items').html(response.html);
                $('#timeline-pagination').html(response.pagination);
                
                // Scroll suave al principio
                $('html, body').animate({
                    scrollTop: $('.compact-timeline').offset().top - 20
                }, 500);
            }
        });
    });
    
    // Animación al aparecer
    const timelineItems = document.querySelectorAll('.timeline-item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${index * 0.1}s`;
                entry.target.style.opacity = 1;
            }
        });
    }, { threshold: 0.1 });
    
    timelineItems.forEach(item => observer.observe(item));
});
</script>
@endsection
@endsection