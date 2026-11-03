<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de equipos</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 24px;
        }

        .header {
            border-bottom: 2px solid #172554;
            margin-bottom: 18px;
            padding-bottom: 10px;
        }

        .header table,
        .header td {
            border: 0;
            padding: 0;
        }

        .logo {
            height: 48px;
            width: 48px;
        }

        h1 {
            color: #172554;
            font-size: 20px;
            margin: 0 0 4px;
        }

        .date {
            color: #475569;
            margin: 0;
        }

        .report {
            border-collapse: collapse;
            width: 100%;
        }

        .report th,
        .report td {
            border: 1px solid #cbd5e1;
            padding: 7px;
            text-align: left;
            vertical-align: top;
        }

        .report th {
            background: #dbeafe;
            color: #172554;
        }

        .footer {
            bottom: 0;
            color: #64748b;
            font-size: 9px;
            position: fixed;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>Reporte de equipos</h1>
                    <p class="date">Fecha del reporte: {{ now()->format('d/m/Y H:i') }}</p>
                </td>
                <td style="text-align: right;">
                    <img class="logo" src="{{ public_path('images/image.png') }}" alt="Logo Jenalex Dinamic">
                </td>
            </tr>
        </table>
    </div>

    <table class="report">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Número de serie</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipos as $equipo)
                <tr>
                    <td>{{ $equipo->codigo }}</td>
                    <td>{{ $equipo->nombre }}</td>
                    <td>{{ $equipo->categoria }}</td>
                    <td>{{ $equipo->marca ?? 'No especificada' }}</td>
                    <td>{{ $equipo->modelo ?? 'No especificado' }}</td>
                    <td>{{ $equipo->numero_serie ?? 'No especificado' }}</td>
                    <td>{{ ucfirst($equipo->estado) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay equipos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Jenalex Dinamic - Inventario de equipos</div>
</body>
</html>
