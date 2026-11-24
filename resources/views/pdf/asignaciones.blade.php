
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de asignaciones</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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

        .activa {
            color: #15803d;
            font-weight: bold;
        }

        .devuelta {
            color: #1d4ed8;
            font-weight: bold;
        }

        .vacio {
            text-align: center;
            padding: 15px;
        }
    </style>
</head>

<body>

    <h2>JENALEX DYNAMICS</h2>
    <h2>Reporte de asignaciones y devoluciones</h2>

    <p class="fecha">
        Fecha de generación: {{ now()->format('d/m/Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Usuario asignado</th>
                <th>Entregado por</th>
                <th>Fecha asignación</th>
                <th>Fecha devolución</th>
                <th>Recibido por</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse($asignaciones as $asignacion)
                <tr>
                    <td>
                        {{ $asignacion->equipo?->codigo ?? 'N/A' }}
                        -
                        {{ $asignacion->equipo?->nombre ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $asignacion->usuario?->name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $asignacion->entregador?->name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $asignacion->fecha_asignacion?->format('d/m/Y H:i') ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $asignacion->fecha_devolucion?->format('d/m/Y H:i') ?? 'Pendiente' }}
                    </td>

                    <td>
                        {{ $asignacion->receptor?->name ?? 'N/A' }}
                    </td>

                    <td>
                        @if($asignacion->fecha_devolucion)
                            <span class="devuelta">Devuelta</span>
                        @else
                            <span class="activa">Activa</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="vacio">
                        No hay asignaciones registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p>Total de registros: {{ $asignaciones->count() }}</p>

</body>
</html>
