<form action="{{ route('personas.update', $persona->slug) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" id="nombre" name="nombre" class="form-control"
            value="{{ old('nombre', $persona->nombre) }}" required maxlength="11">
    </div>

    <div class="mb-3">
        <label for="apellido" class="form-label">Apellido:</label>
        <input type="text" id="apellido" name="apellido" class="form-control"
            value="{{ old('apellido', $persona->apellido) }}" required maxlength="11">
    </div>

    <div class="mb-3">
        <label for="cedula" class="form-label">Cédula:</label>
        <input type="number" id="cedula" name="cedula" class="form-control"
            value="{{ old('cedula', $persona->cedula) }}" required maxlength="8">
    </div>

    <div class="mb-3">
        <label for="correo" class="form-label">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo" class="form-control"
            value="{{ old('correo', $persona->correo) }}" required maxlength="16">
    </div>

    <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" class="form-control"
            value="{{ old('telefono', $persona->telefono) }}" required maxlength="11">
    </div>

    <div class="mb-3">
        <label for="genero" class="form-label">Género:</label>
        <select name="genero" id="genero" class="form-select" required>
            <option value="M" {{ old('genero', $persona->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('genero', $persona->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="altura" class="form-label">Altura (cm):</label>
        <input type="text" id="altura" name="altura" class="form-control"
            value="{{ old('altura', $persona->altura) }}" required min="0" step="0.01" maxlength="4">
    </div>

    <div class="mb-3">
        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control"
            value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento) }}" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Actualizar</button>
</form>