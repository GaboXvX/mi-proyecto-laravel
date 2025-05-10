@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2 class="mb-3">Registrar Persona</h2>

    <div class="card-body px-4">
        <form id="registroPersonaForm" action="{{ route('personas.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" class="form-control" maxlength="8" required>
                </div>
                <div class="col-md-6">
                    <label for="categoria" class="form-label">Categoría:</label>
                    <select name="categoria" id="categoria" class="form-select" required>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id_categoria_persona }}">{{ $categoria->nombre_categoria }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" maxlength="12" required>
                </div>
                <div class="col-md-6">
                    <label for="apellido" class="form-label">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" maxlength="12" required>
                </div>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="correo" class="form-label">Correo:</label>
                    <input type="email" id="correo" name="correo" class="form-control" maxlength="350" required>
                </div>
                <div class="col-md-6">
                    <label for="genero" class="form-label">Género:</label>
                    <select name="genero" id="genero" class="form-select" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control" maxlength="11" required>
                </div>
                <div class="col-md-6">
                    <label for="es_principal" class="form-label">¿Dirección principal?</label>
                    <select name="es_principal" id="es_principal" class="form-select" required>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <livewire:dropdown-persona/>

            <div class="accordion mb-3" id="direccionAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDireccion">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDireccion">
                            Dirección
                        </button>
                    </h2>
                    <div id="collapseDireccion" class="accordion-collapse collapse" data-bs-parent="#direccionAccordion">
                        <div class="accordion-body">
                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="calle" class="form-label">Calle:</label>
                                    <input type="text" id="calle" name="calle" class="form-control" required maxlength="16">
                                </div>
                                <div class="col-md-6">
                                    <label for="manzana" class="form-label">Manzana:</label>
                                    <input type="text" id="manzana" name="manzana" class="form-control" maxlength="10">
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="bloque" class="form-label">Bloque:</label>
                                    <input type="text" id="bloque" name="bloque" class="form-control" maxlength="3">
                                </div>
                                <div class="col-md-6">
                                    <label for="num_vivienda" class="form-label">Número de Vivienda:</label>
                                    <input type="text" id="num_vivienda" name="num_vivienda" class="form-control" maxlength="5" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('personas.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('registroPersonaForm').addEventListener('submit', async function(event) {
        event.preventDefault();
    
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
    
        // Limpiar errores previos
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            const feedback = el.nextElementSibling;
            if (feedback) feedback.textContent = '';
        });
    
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Procesando...
        `;
    
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });
    
            let data;
            try {
                data = await response.json();
            } catch (jsonError) {
                console.error('Error al analizar JSON:', jsonError);
                await Swal.fire({
                    icon: 'error',
                    title: 'Error Inesperado',
                    text: 'Ocurrió un error al procesar la respuesta del servidor.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
    
            if (!response.ok) {
                // Mostrar errores de validación
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, messages]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        const errorElement = input?.nextElementSibling;
    
                        if (input && errorElement) {
                            input.classList.add('is-invalid');
                            errorElement.textContent = messages[0];
                        }
                    });
    
                    let errorMessage = data.message || 'Por favor corrige los siguientes errores:';
                    errorMessage += '<ul>';
                    for (const field in data.errors) {
                        errorMessage += `<li>${data.errors[field][0]}</li>`;
                    }
                    errorMessage += '</ul>';
    
                    await Swal.fire({
                        icon: 'warning',
                        title: data.title || 'Error de validación',
                        html: errorMessage,
                        confirmButtonText: 'Entendido'
                    });
    
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.focus();
                    }
                } else {
                    // Mostrar otros errores del servidor (no validación)
                    await Swal.fire({
                        icon: 'error',
                        title: data.title || 'Error del Servidor',
                        text: data.message || 'Ocurrió un error al procesar la solicitud.',
                        confirmButtonText: 'Entendido'
                    });
                }
            } else {
                // Éxito
                await Swal.fire({
                    icon: 'success',
                    title: data.title || '¡Registro exitoso!',
                    text: data.message,
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = data.redirect_url || "{{ route('personas.index') }}";
                });
            }
    
        } catch (error) {
            console.error('Error de Fetch:', error);
            await Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo completar la operación. Por favor verifica tu conexión e intenta nuevamente.',
                confirmButtonText: 'Entendido'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Registrar';
        }
    });
    </script>
    

@endsection
