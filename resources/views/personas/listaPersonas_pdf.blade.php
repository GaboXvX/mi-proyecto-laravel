<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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

        footer{
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div style="text-align: center;">
            @if(isset($logoBase64))
                <img src="{{ $logoBase64 }}" style="height: 60px; margin-bottom: 10px;"><br>
            @endif
            {!! $membrete !!}
        </div>
    </header>

    <div class="header">
        <h1>Lista de Personas</h1>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cédula</th>
                <th>Correo</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($personas as $persona)
                <tr>
                    <td>{{ $persona->nombre }}</td>
                    <td>{{ $persona->apellido }}</td>
                    <td>{{ $persona->cedula }}</td>
                    <td>{{ $persona->correo }}</td>
                    <td>{{ $persona->telefono }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        @isset($pie_html)
            {!! $pie_html !!}<br>
        @endisset
        <span>Generado el {{ now()->format('d/m/Y H:i:s') }}</span>
    </footer>
</body>
</html>