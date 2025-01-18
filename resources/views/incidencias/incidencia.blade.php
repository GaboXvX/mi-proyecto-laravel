<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</title>
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

        /* Información de la factura */
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

        
        .details-section {
            margin-top: 20px;
        }

        .details-section p {
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Pie de página con datos adicionales */
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #888;
        }

        .footer .comprobante {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Estilos para los botones */
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

    <div class="container">
    
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

            @if($incidencia->id_lider)
                <div class="details-section">
                    <p><strong>Líder Comunitario:</strong> {{ $incidencia->lider->nombre }} {{ $incidencia->lider->apellido }}</p>
                </div>
            @endif

            @if($incidencia->id_persona)
                <div class="details-section">
                    <p><strong>Persona Afectada:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
                </div>
            @endif
        </div>

     
        <div class="footer">
            <p class="comprobante">
                Comprobante emitido por el Ministerio del Poder Popular para la Atención de las Aguas (Minaguas), a los {{ now()->day }} días del mes de {{ now()->locale('es')->monthName }} del año {{ now()->year }}.
            </p>
        </div>

       
        <div class="button-container">
            <a href="{{ route('incidencias.descargar', $incidencia->slug) }}" class="btn btn-download">Descargar PDF</a>
            <a href="{{ route('incidencias.index') }}" class="btn btn-back">Volver</a>
        </div>
    </div>

</body>
</html>
