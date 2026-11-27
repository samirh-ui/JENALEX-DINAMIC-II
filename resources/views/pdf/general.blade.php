

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte general</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #172554;
        }

        h1 {
            font-size: 19px;
        }

        h2 {
            font-size: 14px;
            margin-top: 22px;
            text-align: left;
            border-bottom: 2px solid #172554;
            padding-bottom: 5px;
        }

        .fecha {
            text-align: right;
            margin-bottom: 20px;
        }

        .resumen {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }

        .resumen td {
            width: 25%;
            background-color: #eef2ff;
            border: 1px solid #cbd5e1;
            padding: 12px;
            text-align: center;
        }

        .numero {
            font-size: 20px;
            font-weight: bold;
            color: #172554;
        }

        table.detalle {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .detalle th, .detalle td {
            border: 1px solid #ccc;
            padding: 7px;
            text-align: left;
        }

        .detalle th {
            background-color: #172554;
            color: white;
        }

        .detalle td:last-child {
            text-align: center;
        }

        .ans-resumen {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 12px;
        }

        .ans-resumen td {
            width: 50%;
            padding: 12px;
            text-align: center;
            background-color: #eef2ff;
            border: 1px solid #cbd5e1;
        }

        .ans-numero {
            font-size: 18px;
            font-weight: bold;
            color: #172554;
        }

        .cumplido {
            color: #166534;
        }

        .incumplido,
        .vencido {
            color: #991b1b;
        }

        .pendiente {
            color: #854d0e;
        }

        .sin-asignar {
            color: #64748b;
        }

        .nota {
            margin-top: 25px;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>

    <h1>JENALEX DYNAMICS</h1>

    <h2 style="text-align:center; border:none;">
        Reporte general del sistema
    </h2>

    <p class="fecha">
        Fecha de generación: {{ now()->format('d/m/Y H:i') }}
    </p>


    {{-- RESUMEN GENERAL --}}

    <h2>Resumen general</h2>

    <table class="resumen">
        <tr>
            <td>
                <div>Equipos</div>
                <div class="numero">{{ $totalEquipos }}</div>
            </td>

            <td>
                <div>Asignaciones</div>
                <div class="numero">{{ $totalAsignaciones }}</div>
            </td>

            <td>
                <div>Solicitudes</div>
                <div class="numero">{{ $totalSolicitudes }}</div>
            </td>

            <td>
                <div>Mantenimientos</div>
                <div class="numero">{{ $totalMantenimientos }}</div>
            </td>
        </tr>
    </table>


    {{-- INDICADORES ANS --}}

    <h2>Indicadores de cumplimiento ANS</h2>

    <p>
        Solicitudes con tiempos ANS asignados:
        <strong>{{ $totalSolicitudesAns }}</strong>
    </p>

    <table class="ans-resumen">
        <tr>
            <td>
                <div>Cumplimiento de respuesta</div>

                <div class="ans-numero">
                    {{ number_format($porcentajeCumplimientoRespuesta, 2) }}%
                </div>
            </td>

            <td>
                <div>Cumplimiento de solución</div>

                <div class="ans-numero">
                    {{ number_format($porcentajeCumplimientoSolucion, 2) }}%
                </div>
            </td>
        </tr>
    </table>


    {{-- DETALLE ANS DE RESPUESTA --}}

    <h2>Seguimiento ANS de respuesta</h2>

    <table class="detalle">
        <thead>
            <tr>
                <th>Estado</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="cumplido">Cumplido</td>
                <td>{{ $ansRespuestaResumen['cumplido'] }}</td>
            </tr>

            <tr>
                <td class="incumplido">Incumplido</td>
                <td>{{ $ansRespuestaResumen['incumplido'] }}</td>
            </tr>

            <tr>
                <td class="vencido">Vencido sin respuesta</td>
                <td>{{ $ansRespuestaResumen['vencido'] }}</td>
            </tr>

            <tr>
                <td class="pendiente">Pendiente dentro del plazo</td>
                <td>{{ $ansRespuestaResumen['pendiente'] }}</td>
            </tr>

            <tr>
                <td class="sin-asignar">Sin asignar</td>
                <td>{{ $ansRespuestaResumen['sin_asignar'] }}</td>
            </tr>
        </tbody>
    </table>


    {{-- DETALLE ANS DE SOLUCIÓN --}}

    <h2>Seguimiento ANS de solución</h2>

    <table class="detalle">
        <thead>
            <tr>
                <th>Estado</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="cumplido">Cumplido</td>
                <td>{{ $ansSolucionResumen['cumplido'] }}</td>
            </tr>

            <tr>
                <td class="incumplido">Incumplido</td>
                <td>{{ $ansSolucionResumen['incumplido'] }}</td>
            </tr>

            <tr>
                <td class="vencido">Vencido sin solución</td>
                <td>{{ $ansSolucionResumen['vencido'] }}</td>
            </tr>

            <tr>
                <td class="pendiente">Pendiente dentro del plazo</td>
                <td>{{ $ansSolucionResumen['pendiente'] }}</td>
            </tr>

            <tr>
                <td class="sin-asignar">Sin asignar</td>
                <td>{{ $ansSolucionResumen['sin_asignar'] }}</td>
            </tr>
        </tbody>
    </table>


    {{-- INVENTARIO --}}

    <h2>Inventario de equipos por estado</h2>

    <table class="detalle">
        <thead>
            <tr>
                <th>Estado del equipo</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse($equiposPorEstado as $item)
                <tr>
                    <td>{{ ucfirst($item->estado) }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No hay equipos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    {{-- ASIGNACIONES --}}

    <h2>Asignaciones y devoluciones</h2>

    <table class="detalle">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>Asignaciones activas</td>
                <td>{{ $asignacionesActivas }}</td>
            </tr>

            <tr>
                <td>Asignaciones devueltas</td>
                <td>{{ $asignacionesDevueltas }}</td>
            </tr>
        </tbody>
    </table>


    {{-- SOLICITUDES --}}

    <h2>Solicitudes de soporte por estado</h2>

    <table class="detalle">
        <thead>
            <tr>
                <th>Estado de la solicitud</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse($solicitudesPorEstado as $item)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->estado)) }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No hay solicitudes registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    {{-- MANTENIMIENTOS POR TIPO --}}

    <h2>Mantenimientos por tipo</h2>

    <table class="detalle">
        <thead>
            <tr>
                <th>Tipo de mantenimiento</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse($mantenimientosPorTipo as $item)
                <tr>
                    <td>{{ ucfirst($item->tipo) }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No hay mantenimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    {{-- MANTENIMIENTOS POR ESTADO --}}

    <h2>Mantenimientos por estado</h2>

    <table class="detalle">
        <thead>
            <tr>
                <th>Estado del mantenimiento</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse($mantenimientosPorEstado as $item)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->estado)) }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No hay mantenimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>


    <p class="nota">
        Documento generado automáticamente por el sistema
        JENALEX DYNAMICS.
        Los porcentajes ANS se calculan sobre los casos evaluables:
        cumplidos, incumplidos y vencidos.
    </p>

</body>
</html>
