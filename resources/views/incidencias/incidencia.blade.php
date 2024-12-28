<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h1>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</h1>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Descripción</h5>
                <p class="card-text">{{ $incidencia->descripcion }}</p>
                
                <h5 class="card-title">Tipo de Incidencia</h5>
                <p class="card-text">{{ $incidencia->tipo_incidencia }}</p>

                <h5 class="card-title">Nivel de Prioridad</h5>
                <p class="card-text">{{ $incidencia->nivel_prioridad }}</p>

                <h5 class="card-title">Estado</h5>
                <p class="card-text">{{ $incidencia->estado }}</p>

                <h5 class="card-title">Fecha de Creación</h5>
                <p class="card-text">{{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
                
                <!-- Líder Comunitario -->
                @if($incidencia->id_lider)
                    <h5 class="card-title">Líder Comunitario</h5>
                    <p class="card-text">{{ $incidencia->lider->nombre }}</p>
                @endif

                <!-- Persona Afectada -->
                @if($incidencia->id_persona)
                    <h5 class="card-title">Persona Afectada</h5>
                    <p class="card-text">{{ $incidencia->persona->nombre }}</p>
                @endif

                <a href="{{ route('incidencias.download', $incidencia->slug) }}" class="btn btn-success">Descargar PDF</a>

                <br><br>

                
                <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Volver a la lista de incidencias</a>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
