
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Reporte de solicitudes</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #172554;
            margin-bottom: 5px;
        }

        .subtitulo {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
        }

        .fecha {
            text-align: right;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 5px 3px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #172554;
            color: white;
            font-size: 7px;
        }

        td {
            font-size: 7px;
        }

        .vacio {
            text-align: center;
            padding: 15px;
        }

        .estado {
            font-weight: bold;
        }

        .cumplido {
            color: #166534;
            background-color: #dcfce7;
        }

        .pendiente {
            color: #854d0e;
            background-color: #fef9c3;
        }

        .vencido,
        .incumplido {
            color: #991b1b;
            background-color: #fee2e2;
        }

        .sin_asignar {
            color: #475569;
            background-color: #f1f5f9;
        }

        .total {
            margin-top: 15px;
            font-size: 9px;
            font-weight: bold;
        }

        .nota {
            margin-top: 10px;
            font-size: 7px;
            color: #555;
        }
    </style>
</head>

<body>

    <h2>JENALEX DYNAMICS</h2>

    <div class="subtitulo">
        Reporte de solicitudes de soporte y cumplimiento ANS
    </div>

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

                <th>Límite respuesta</th>
                <th>Respuesta real</th>
                <th>ANS respuesta</th>

                <th>Límite solución</th>
                <th>Solución real</th>
                <th>ANS solución</th>

                <th>Cierre</th>
            </tr>
        </thead>

        <tbody>

            @forelse($solicitudes as $solicitud)

                <tr>

                    <td>
                        {{ $solicitud->codigo }}
                    </td>

                    <td>
                        {{ $solicitud->usuario?->name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $solicitud->equipo?->codigo ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $solicitud->tipo }}
                    </td>

                    <td>
                        {{ $solicitud->prioridad
                            ? ucfirst($solicitud->prioridad)
                            : 'Sin clasificar' }}
                    </td>

                    <td>
                        {{ $solicitud->tecnico?->name ?? 'Sin asignar' }}
                    </td>

                    <td>
                        {{ ucfirst(str_replace('_', ' ', $solicitud->estado)) }}
                    </td>

                    <td>
                        {{ $solicitud->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                    </td>


                    {{-- ANS DE RESPUESTA --}}

                    <td>
                        {{ $solicitud->ans_respuesta_limite
                            ? $solicitud->ans_respuesta_limite->format('d/m/Y H:i')
                            : 'Sin asignar' }}
                    </td>

                    <td>
                        {{ $solicitud->fecha_respuesta
                            ? $solicitud->fecha_respuesta->format('d/m/Y H:i')
                            : 'Pendiente' }}
                    </td>

                    <td class="estado {{ $solicitud->ans_respuesta_estado }}">
                        {{ ucfirst(str_replace(
                            '_',
                            ' ',
                            $solicitud->ans_respuesta_estado
                        )) }}
                    </td>


                    {{-- ANS DE SOLUCIÓN --}}

                    <td>
                        {{ $solicitud->ans_solucion_limite
                            ? $solicitud->ans_solucion_limite->format('d/m/Y H:i')
                            : 'Sin asignar' }}
                    </td>

                    <td>
                        {{ $solicitud->fecha_solucion
                            ? $solicitud->fecha_solucion->format('d/m/Y H:i')
                            : 'Pendiente' }}
                    </td>

                    <td class="estado {{ $solicitud->ans_solucion_estado }}">
                        {{ ucfirst(str_replace(
                            '_',
                            ' ',
                            $solicitud->ans_solucion_estado
                        )) }}
                    </td>


                    {{-- FECHA DE CIERRE --}}

                    <td>
                        {{ $solicitud->fecha_cierre
                            ? $solicitud->fecha_cierre->format('d/m/Y H:i')
                            : 'Pendiente' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="15" class="vacio">
                        No hay solicitudes registradas.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    <p class="total">
        Total de solicitudes: {{ $solicitudes->count() }}
    </p>

    <p class="nota">
        Nota: El estado del ANS se calcula considerando las fechas
        límite y las fechas reales de respuesta y solución.
        Los estados pendiente y vencido se evalúan al momento
        de generar el reporte.
    </p>

</body>
</html>
