<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        /* Aquí se incluyen estilos adicionales */
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

        <div class="button-container">
            <button class="btn btn-download" id="downloadPdfBtn">Descargar PDF</button>
            <a href="{{ route('home') }}" class="btn btn-back">Volver</a>
        </div>
    </div>

    <!-- jsPDF and html2pdf Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
    <script>
        document.getElementById('downloadPdfBtn').addEventListener('click', function() {
            const element = document.getElementById('comprobante-container');
            
            // Ocultar los botones antes de la descarga
            document.querySelector('.button-container').style.display = 'none';

            // Usamos html2pdf.js para generar el PDF con los estilos
            html2pdf()
                .from(element)
                .save('comprobante_incidencia.pdf')
                .finally(() => {
                    // Restaurar los botones después de la descarga
                    document.querySelector('.button-container').style.display = 'block';
                });
        });
    </script>
</body>
</html>
