@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2 class="mb-3">Registrar Persona</h2>

    <div class="card-body px-4">
        <form id="registroPersonaForm" action="{{ route('personas.store') }}" method="POST">
            @csrf

            {{-- Datos Personales --}}
            <div class="row g-3 mb-2">
                <div class="col-md-5">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control solo-letras" maxlength="12" required>
                </div>
                <div class="col-md-5">
                    <label for="apellido" class="form-label">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" class="form-control solo-letras" maxlength="12" required>
                </div>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-5">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" class="form-control solo-numeros" maxlength="8" required>
                </div>
                <div class="col-md-5">
                    <label for="correo" class="form-label">Correo:</label>
                    <input type="email" id="correo" name="correo" class="form-control" maxlength="350" required>
                </div>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-5">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control solo-numeros" maxlength="11" required>
                </div>
                <div class="col-md-5">
                    <label for="genero" class="form-label">Género:</label>
                    <select name="genero" id="genero" class="form-select" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-2">
                <div class="col-md-5">
                    <label for="categoria" class="form-label">Categoría:</label>
                    <select name="categoria" id="categoria" class="form-select" required>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id_categoria_persona }}">{{ $categoria->nombre_categoria }}</option>
                        @endforeach
                    </select>
                    <label for="categoria" class="form-label">Categoría:</label>
                    <select name="id_categoria_persona" id="categoria" class="form-select" required>
                        <option value="">Seleccione una categoría</option>
                        <option value="0">Ninguno</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id_categoria_persona }}" 
                                data-requiere-comunidad="{{ $categoria->reglasConfiguradas->requiere_comunidad ?? 0 }}"
                                data-unico-en-comunidad="{{ $categoria->reglasConfiguradas->unico_en_comunidad ?? 0 }}"
                                data-unico-en-sistema="{{ $categoria->reglasConfiguradas->unico_en_sistema ?? 0 }}">
                                {{ $categoria->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                    <div id="categoria-error" class="invalid-feedback"></div>
                </div>
                <div class="col-md-5">
                    <label for="es_principal" class="form-label">¿Dirección principal?</label>
                    <select name="es_principal" id="es_principal" class="form-select" required>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <!-- Dropdown de Estados -->
                <div class="col-md-5">
                    <label for="estado" class="form-label">Estado:</label>
                    <select name="estado" id="estado" class="form-select" wire:model.live="estadoId" required>
                        <option value="">Seleccione un estado</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id_estado }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown de Municipios -->
                <div class="col-md-5">
                    <label for="municipio" class="form-label">Municipio:</label>
                    <select name="municipio" id="municipio" class="form-select" wire:model.live="municipioId" required>
                        <option value="">Seleccione un municipio</option>
                        @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id_municipio }}">{{ $municipio->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <!-- Dropdown de Parroquias -->
                <div class="col-md-5">
                    <label for="parroquia" class="form-label">Parroquia:</label>
                    <select name="parroquia" id="parroquia" class="form-select" wire:model.live="parroquiaId" required>
                        <option value="">Seleccione una parroquia</option>
                        @foreach($parroquias as $parroquia)
                            <option value="{{ $parroquia->id_parroquia }}">{{ $parroquia->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown de Urbanizaciones -->
                <div class="col-md-5">
                    <label for="urbanizacion" class="form-label">Urbanización:</label>
                    <select name="urbanizacion" id="urbanizacion" class="form-select" wire:model.live="urbanizacionId" required>
                        <option value="">Seleccione una urbanización</option>
                        @foreach($urbanizaciones as $urbanizacion)
                            <option value="{{ $urbanizacion->id_urbanizacion }}">{{ $urbanizacion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <!-- Dropdown de Sectores -->
                <div class="col-md-5">
                    <label for="sector" class="form-label">Sector:</label>
                    <select name="sector" id="sector" class="form-select" wire:model.live="sectorId" required>
                        <option value="">Seleccione un sector</option>
                        @foreach($sectores as $sector)
                            <option value="{{ $sector->id_sector }}">{{ $sector->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown de Comunidades -->
                <div class="col-md-5">
                    <label for="comunidad" class="form-label">Comunidad:</label>
                    <select name="comunidad" id="comunidad" class="form-select" wire:model.live="comunidadId" required>
                        <option value="">Seleccione una comunidad</option>
                        @foreach($comunidades as $comunidad)
                            <option value="{{ $comunidad->id_comunidad }}">{{ $comunidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Dirección --}}
            <div class="row g-3 mb-2 mt-3">
                <div class="col-md-5">
                    <label for="calle" class="form-label">Calle:</label>
                    <input type="text" id="calle" name="calle" class="form-control" required maxlength="16">
                </div>
                <div class="col-md-5">
                    <label for="manzana" class="form-label">Manzana:</label>
                    <input type="text" id="manzana" name="manzana" class="form-control" maxlength="10">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label for="bloque" class="form-label">Bloque:</label>
                    <input type="text" id="bloque" name="bloque" class="form-control" maxlength="3">
                </div>
                <div class="col-md-5">
                    <label for="num_vivienda" class="form-label">Número de Vivienda:</label>
                    <input type="text" id="num_vivienda" name="num_vivienda" class="form-control" maxlength="5" required>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroPersonaForm');
    const categoriaSelect = document.getElementById('categoria');
    const comunidadInput = document.querySelector('[name="comunidad"]');

    // Validación en tiempo real de la categoría
    categoriaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const requiereComunidad = selectedOption.getAttribute('data-requiere-comunidad') === '1';
        const unicoEnComunidad = selectedOption.getAttribute('data-unico-en-comunidad') === '1';
        const unicoEnSistema = selectedOption.getAttribute('data-unico-en-sistema') === '1';

        if (requiereComunidad && !comunidadInput.value) {
            document.getElementById('categoria-error').textContent = 'Esta categoría requiere seleccionar una comunidad';
            categoriaSelect.classList.add('is-invalid');
        } else {
            document.getElementById('categoria-error').textContent = '';
            categoriaSelect.classList.remove('is-invalid');
        }
    });

    form.addEventListener('submit', async function(event) {
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
            // Validación adicional de categoría antes de enviar
            const selectedCategoria = categoriaSelect.value;
            const selectedOption = categoriaSelect.options[categoriaSelect.selectedIndex];
            const requiereComunidad = selectedOption.getAttribute('data-requiere-comunidad') === '1';
            const comunidadId = document.querySelector('[name="comunidad"]').value;

            if (selectedCategoria === "0") {
                // Ninguna categoría seleccionada - permitir
            } else if (requiereComunidad && !comunidadId) {
                document.getElementById('categoria-error').textContent = 'Esta categoría requiere seleccionar una comunidad';
                categoriaSelect.classList.add('is-invalid');
                throw new Error('Validación fallida');
            }

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
            if (error.message !== 'Validación fallida') {
                await Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo completar la operación. Por favor verifica tu conexión e intenta nuevamente.',
                    confirmButtonText: 'Entendido'
                });
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Registrar';
        }
    });
});
</script>
@endsection