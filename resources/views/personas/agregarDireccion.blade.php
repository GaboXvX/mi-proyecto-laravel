@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Agregar Dirección</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guardarDireccion',$persona->id_persona ) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado:</label>
                                <select name="estado" id="estado" class="form-select" required>
                                    <option value="Sucre" {{ old('estado') == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="municipio" class="form-label">Municipio:</label>
                                <select name="municipio" id="municipio" class="form-select" required>
                                    <option value="Sucre" {{ old('municipio') == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                                </select>
                            </div>
                            <livewire:dropdown-persona/>
                
                            <div class="mb-3">
                                <label for="calle" class="form-label">Calle:</label>
                                <input type="text" id="calle" name="calle" class="form-control" value="{{ old('calle') }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="manzana" class="form-label">Manzana:</label>
                                <input type="text" id="manzana" name="manzana" class="form-control" value="{{ old('manzana') }}">
                            </div>
                
                            <div class="mb-3">
                                <label for="numero_de_casa" class="form-label">Número de Casa:</label>
                                <input type="number" id="numero_de_casa" name="numero_de_casa" class="form-control" value="{{ old('numero_de_casa') }}" required min="1" step="1">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
