<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Incidencia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 30px;
            color: #003366;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #777;
            margin-top: 0;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #003366;
        }

        .invoice-details p {
            font-size: 14px;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #888;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin: 5px;
        }

        .btn-download {
            background-color: #28a745;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #6c757d;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        @media print {
            .button-container {
                display: none; 
            }
        }
    </style>
</head>
<body>
    <div class="container" id="comprobante-container">
        <div class="header">
            <h1>Comprobante de Incidencia</h1>
            <p>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</p>
        </div>

        <div class="invoice-details">
            <h3>Información de la Incidencia:</h3>
            <div class="details-section">
                <p><strong>Código de Incidencia:</strong> {{ $incidencia->cod_incidencia }}</p>
                <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
                <p><strong>Tipo de Incidencia:</strong> {{ $incidencia->tipo_incidencia }}</p>
                <p><strong>Nivel de Prioridad:</strong> {{ $incidencia->nivel_prioridad }}</p>
                <p><strong>Estado:</strong> {{ $incidencia->estado }}</p>
                <p><strong>Fecha de Creación:</strong> {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if($incidencia->id_persona)
            <div class="details-section">
                <p><strong>Persona Afectada:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
                    @if($incidencia->persona->es_lider==1)
                    <p><strong>¿Es lider? </strong> <br>
                    {{$incidencia->persona->es_lider ? 'si' :'No'}}
                    @else
                    <p><strong>Lugar de la incidencia:</strong></p>

                    @if($incidencia->direccion)
                        <p>
                            <strong>Estado:</strong> {{ $incidencia->direccion->estado->nombre }},
                            <strong>Municipio:</strong> {{ $incidencia->direccion->municipio->nombre }},
                            <strong>Parroquia:</strong> {{ $incidencia->direccion->parroquia->nombre }},
                            <strong>Urbanización:</strong> {{ $incidencia->direccion->urbanizacion->nombre }},
                            <strong>Sector:</strong> {{ $incidencia->direccion->sector->nombre }},
                            <strong>Comunidad:</strong> {{ $incidencia->direccion->comunidad->nombre }},
                            <strong>Calle:</strong> {{ $incidencia->direccion->calle }},
                            
                            @if($incidencia->direccion->manzana)
                                <strong>Manzana:</strong> {{ $incidencia->direccion->manzana }},
                            @endif
                    
                            @if($incidencia->direccion->numero_de_vivienda)
                                <strong>N° de vivienda:</strong> {{ $incidencia->direccion->numero_de_vivienda }},
                            @endif
                        </p>
                    @else
                        <p><em>No hay dirección asociada a esta incidencia.</em></p>
                    @endif
                    
                    
                    <p><strong>Lider comunitario </strong> <br>
                        @if($incidencia->lider)
                        {{$incidencia->lider->personas->nombre ?? 'Nombre no disponible'}} 
                        {{$incidencia->lider->personas->apellido ?? 'Nombre no disponible'}} <strong>V-</strong>
                        {{$incidencia->lider->personas->cedula ?? 'Nombre no disponible'}}
                    @else
                        <p>No tiene un líder asignado</p>
                    @endif
                                        @endif
            </div>
        @endif
        </div>

        <div class="footer">
            <p class="comprobante">
                Comprobante emitido por el Ministerio del Poder Popular para la Atención de las Aguas (Minaguas).
            </p>
        </div>
    </div>
</body>
</html>
