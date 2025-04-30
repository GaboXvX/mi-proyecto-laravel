@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow border-0">
                <div class="card-header text-white py-3" style="background-color: #24476c;">
                    <h4 class="mb-0">Registrar Nueva Persona</h4>
                </div>
                <div class="card-body px-4">

                    <form id="registroPersonaForm" action="{{ route('personas.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Alertas globales -->
                        <div id="global-alerts" class="alert alert-danger d-none"></div>

                        <!-- Primera fila: Categoría y Género -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option selected disabled>Seleccione una categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id_categoria_persona }}">{{ $categoria->nombre_categoria }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="genero" class="form-label">Género</label>
                                <select class="form-select" id="genero" name="genero" required>
                                    <option selected disabled>Seleccione un género</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nombre y Apellido -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                                <label for="nombre">Nombre</label>
                            </div>
                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" required>
                                <label for="apellido">Apellido</label>
                            </div>
                        </div>

                        <!-- Cédula y Correo -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="cedula" name="cedula" maxlength="8" pattern="\d{7,8}" placeholder="Cédula" required>
                                <label for="cedula">Cédula</label>
                            </div>
                            <div class="col-md-6 form-floating">
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" required>
                                <label for="correo">Correo Electrónico</label>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-3 form-floating">
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" required pattern="^\d{11}$">
                            <label for="telefono">Teléfono</label>
                        </div>

                        <!-- Dropdown Livewire -->
                        <livewire:dropdown-persona />

                        <!-- Dirección (calle, manzana, bloque, num vivienda) -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="calle" name="calle" placeholder="Calle" required>
                                <label for="calle">Calle</label>
                            </div>
                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="manzana" name="manzana" placeholder="Manzana">
                                <label for="manzana">Manzana</label>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="bloque" name="bloque" placeholder="Bloque">
                                <label for="bloque">Bloque</label>
                            </div>
                            <div class="col-md-6 form-floating">
                                <input type="number" class="form-control" id="num_vivienda" name="num_vivienda" placeholder="Número de Vivienda" required>
                                <label for="num_vivienda">Número de Vivienda</label>
                            </div>
                        </div>

                        <!-- ¿Dirección principal? -->
                        <div class="mb-4">
                            <label for="es_principal" class="form-label">¿Es la dirección principal?</label>
                            <select name="es_principal" id="es_principal" class="form-select" required>
                                <option disabled selected>Seleccione una opción</option>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('personas.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle"></i> Registrar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
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
