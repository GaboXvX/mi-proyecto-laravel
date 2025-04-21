{{-- filepath: c:\laragon\www\mi-proyecto-laravel-master\resources\views\usuarios\asignarPermisos.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="table-container">
    <h2>Asignar Permisos a {{ $usuario->empleadoAutorizado->nombre }} {{ $usuario->empleadoAutorizado->apellido }}</h2>
    <form id="form-permisos">
        @csrf
        <input type="hidden" name="id_usuario" value="{{ $usuario->id_usuario }}">
        <div class="row">
            @foreach ($permisos as $permiso)
                <div class="col-md-4">
                    <div class="form-check">
                        <input 
                            class="form-check-input permiso-checkbox" 
                            type="checkbox" 
                            name="permiso[]" 
                            value="{{ $permiso->name }}" 
                            id="permiso-{{ $permiso->id }}" 
                            {{ $usuario->hasPermissionTo($permiso->name) ? 'checked' : '' }}>
                        <label class="form-check-label" for="permiso-{{ $permiso->id }}">
                            {{ $permiso->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('.permiso-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const formData = new FormData();
            formData.append('id_usuario', document.querySelector('input[name="id_usuario"]').value);
            formData.append('permiso', this.value);

            fetch("{{ route('usuarios.togglePermisoAjax') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
        });
    });
</script>
@endsection