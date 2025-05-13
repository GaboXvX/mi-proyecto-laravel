@extends('layouts.app')

@section('content')
<div class="table-container py-5">
    <h2 class="mb-5 text-center font-weight-bold">Panel de Instituciones</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @foreach($instituciones as $institucion)
            <div class="col-lg-6 mb-4">
                <div class="card border rounded-4 p-4 glass-card position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">{{ $institucion->nombre }}</h4>
                       <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#logoModal{{ $institucion->id_institucion }}">
    <i class="fas fa-sync-alt"></i> Logo
</button>

                    </div>

                    <div class="text-center mb-4">
                        @if($institucion->logo_path)
                            <img src="{{ asset('storage/' . $institucion->logo_path) }}" 
                                 alt="Logo {{ $institucion->nombre }}" 
                                 class="img-thumbnail border-0 shadow-sm" style="max-height: 120px;">
                        @else
                            <div class="text-muted">Logo no disponible</div>
                        @endif
                    </div>

                    <div class="border p-3 rounded mb-4">
                        <h6 class="text-muted">Membrete Actual</h6>
                        <div>
                            @if($institucion->encabezado_html)
                                {!! $institucion->encabezado_html !!}
                            @else
                                <em class="text-muted">Sin membrete configurado</em>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('instituciones.updateMembrete', $institucion->id_institucion) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="membrete{{ $institucion->id_institucion }}" class="text-muted">Editar Membrete</label>
                            <textarea id="membrete{{ $institucion->id_institucion }}" name="encabezado_html" rows="3" class="form-control shadow-sm">{{ $institucion->encabezado_html }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success mt-2">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal Logo Alternativo -->
            <!-- Modal Bootstrap Mejorado -->
<div class="modal fade" id="logoModal{{ $institucion->id_institucion }}" tabindex="-1" aria-labelledby="logoModalLabel{{ $institucion->id_institucion }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <form action="{{ route('instituciones.updateLogo', $institucion->id_institucion) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white rounded-top">
                    <h5 class="modal-title" id="logoModalLabel{{ $institucion->id_institucion }}">Actualizar Logo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subir nuevo logo (JPG/PNG, máx. 2MB)</label>
                        <input type="file" class="form-control" name="logo" accept="image/png, image/jpeg" required>
                    </div>
                </div>
                <div class="modal-footer rounded-bottom">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

        @endforeach
    </div>
</div>
<script>
    function openLogoModal(institucionId) {
        const modal = document.getElementById(`customModal${institucionId}`);
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Previene el scroll
    }

    function closeModal(institucionId) {
        const modal = document.getElementById(`customModal${institucionId}`);
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restaura el scroll
    }

    // Cerrar al hacer clic fuera del modal
    window.addEventListener('click', function(event) {
        document.querySelectorAll('.custom-modal').forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    });
</script>
@endsection

@push('styles')
<style>
    body {
        background: #f8f9fa;
    }

    .glass-card {
        backdrop-filter: blur(6px);
        background-color: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .card h4 {
        font-weight: 600;
    }

    /* Estilos para el modal personalizado */
    .custom-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1050;
    }

    .custom-modal-content {
        width: 90%;
        max-width: 500px;
        animation: modalFadeIn 0.3s;
    }

    .custom-modal-header, .custom-modal-footer {
        padding: 15px 20px;
    }

    .custom-modal-body {
        padding: 20px;
    }

    .custom-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>


@endpush