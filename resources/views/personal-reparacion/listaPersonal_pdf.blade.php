<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Personal de Reparación</title>
</head>
<body>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h1 {
            font-size: 18px;
            margin: 5px 0;
            color: #333;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>

    <div class="header">
        <h1>Personal de Reparación</h1>
    </div>
    <table class="table table-striped datatable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Institución</th>
                    <th>Estación</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personal as $persona)
                <tr>
                    <td>{{ $persona->nombre }} </td>
                    <td>{{ $persona->apellido }}</td>
                    <td>{{ $persona->institucion->nombre ?? 'N/A' }}</td>
                    <td>{{ $persona->institucionEstacion->nombre ?? 'N/A' }}</td>
                    <td>{{ $persona->cedula }}</td>
                    <td>{{ $persona->telefono }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
</body>
</html>