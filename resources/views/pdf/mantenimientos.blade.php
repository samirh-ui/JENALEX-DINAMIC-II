
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de mantenimientos</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #172554;
        }

        .fecha {
            text-align: right;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #172554;
            color: white;
        }

        .vacio {
            text-align: center;
            padding: 15px;
        }
    </style>
</head>

<body>

    <h2>JENALEX DYNAMICS</h2>
    <h2>Reporte de mantenimientos</h2>

    <p class="fecha">
        Fecha de generación: {{ now()->format('d/m/Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Tipo</th>
                <th>Técnico</th>
                <th>Estado</th>
                <th>Fecha inicio</th>
                <th>Fecha finalización</th>
                <th>Diagnóstico</th>
                <th>Trabajo realizado</th>
                <th>Observaciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse($mantenimientos as $mantenimiento)
                <tr>
                    <td>
                        {{ $mantenimiento->equipo?->codigo ?? 'N/A' }}
                        -
                        {{ $mantenimiento->equipo?->nombre ?? 'N/A' }}
                    </td>

                    <td>{{ ucfirst($mantenimiento->tipo) }}</td>

                    <td>
                        {{ $mantenimiento->tecnico?->name ?? 'N/A' }}
                    </td>

                    <td>{{ ucfirst(str_replace('_', ' ', $mantenimiento->estado)) }}</td>

                    <td>
                        {{ $mantenimiento->fecha_inicio?->format('d/m/Y H:i') ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $mantenimiento->fecha_finalizacion?->format('d/m/Y H:i') ?? 'Pendiente' }}
                    </td>

                    <td>{{ $mantenimiento->diagnostico ?? 'N/A' }}</td>

                    <td>{{ $mantenimiento->trabajo_realizado ?? 'Pendiente' }}</td>

                    <td>{{ $mantenimiento->observaciones ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="vacio">
                        No hay mantenimientos registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>Total de mantenimientos: {{ $mantenimientos->count() }}</p>

</body>
</html>
