
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de solicitudes</title>

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
    <h2>Reporte de solicitudes de soporte</h2>

    <p class="fecha">
        Fecha de generación: {{ now()->format('d/m/Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Usuario</th>
                <th>Equipo</th>
                <th>Tipo</th>
                <th>Prioridad</th>
                <th>Técnico</th>
                <th>Estado</th>
                <th>Registro</th>
                <th>Cierre</th>
            </tr>
        </thead>

        <tbody>
            @forelse($solicitudes as $solicitud)
                <tr>
                    <td>{{ $solicitud->codigo }}</td>

                    <td>
                        {{ $solicitud->usuario?->name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $solicitud->equipo?->codigo ?? 'N/A' }}
                    </td>

                    <td>{{ $solicitud->tipo }}</td>

                    <td>{{ $solicitud->prioridad ?? 'Sin clasificar' }}</td>

                    <td>
                        {{ $solicitud->tecnico?->name ?? 'Sin asignar' }}
                    </td>

                    <td>{{ $solicitud->estado }}</td>

                    <td>
                        {{ $solicitud->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $solicitud->fecha_cierre?->format('d/m/Y H:i') ?? 'Pendiente' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="vacio">
                        No hay solicitudes registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>Total de solicitudes: {{ $solicitudes->count() }}</p>

</body>
</html>
