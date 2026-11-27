

@extends('layouts.app')

@section('title', 'Detalle de solicitud')

@section('content')

@if (session('success'))
    <div class="success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $estadosAns = [
        'sin_asignar' => [
            'texto' => 'Sin asignar',
            'color' => '#64748b',
            'fondo' => '#f1f5f9',
        ],
        'pendiente' => [
            'texto' => 'Pendiente',
            'color' => '#854d0e',
            'fondo' => '#fef9c3',
        ],
        'cumplido' => [
            'texto' => 'Cumplido',
            'color' => '#166534',
            'fondo' => '#dcfce7',
        ],
        'incumplido' => [
            'texto' => 'Incumplido',
            'color' => '#991b1b',
            'fondo' => '#fee2e2',
        ],
        'vencido' => [
            'texto' => 'Vencido',
            'color' => '#991b1b',
            'fondo' => '#fee2e2',
        ],
    ];

    $estadoRespuesta = $estadosAns[$ansRespuestaEstado]
        ?? $estadosAns['sin_asignar'];

    $estadoSolucion = $estadosAns[$ansSolucionEstado]
        ?? $estadosAns['sin_asignar'];
@endphp

<div class="card">

    <h1>Detalle de solicitud</h1>

    <h2>{{ $solicitud->codigo }}</h2>

    <p>
        <strong>Estado:</strong>
        {{ ucfirst(str_replace(['_', '-'], ' ', $solicitud->estado)) }}
    </p>

    <p>
        <strong>Prioridad:</strong>
        {{ $solicitud->prioridad
            ? ucfirst($solicitud->prioridad)
            : 'Sin clasificar' }}
    </p>

    <hr>

    {{-- INFORMACIÓN DEL REPORTE --}}

    <h3>Información del reporte</h3>

    <p>
        <strong>Equipo:</strong>
        {{ $solicitud->equipo->nombre ?? 'Sin equipo' }}
    </p>

    <p>
        <strong>Reportado por:</strong>
        {{ $solicitud->usuario->name ?? 'Sin usuario' }}
    </p>

    <p>
        <strong>Tipo:</strong>
        {{ $solicitud->tipo }}
    </p>

    <p>
        <strong>Descripción:</strong>
        {{ $solicitud->descripcion }}
    </p>

    <hr>

    {{-- GESTIÓN DE SOPORTE --}}

    <h3>Gestión de soporte</h3>

    <p>
        <strong>Técnico asignado:</strong>
        {{ $solicitud->tecnico->name ?? 'Pendiente de asignación' }}
    </p>

    <p>
        <strong>Escalado por:</strong>
        {{ $solicitud->escalador->name ?? 'Pendiente de escalamiento' }}
    </p>

    <p>
        <strong>Fecha de asignación:</strong>
        {{ $solicitud->fecha_asignacion
            ? $solicitud->fecha_asignacion->format('d/m/Y H:i')
            : 'Pendiente' }}
    </p>

    <hr>

    {{-- SEGUIMIENTO ANS --}}

    <h3>Seguimiento de ANS</h3>

    <p>
        El Acuerdo de Nivel de Servicio (ANS) permite controlar
        los tiempos establecidos para responder y solucionar
        cada solicitud según su prioridad.
    </p>

    <div style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 18px;
        margin: 20px 0;
    ">

        {{-- ANS DE RESPUESTA --}}

        <div style="
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 18px;
        ">

            <h4 style="margin-top: 0;">
                Tiempo de respuesta
            </h4>

            <p>
                <strong>Fecha límite:</strong><br>

                {{ $solicitud->ans_respuesta_limite
                    ? $solicitud->ans_respuesta_limite->format('d/m/Y H:i')
                    : 'Sin asignar' }}
            </p>

            <p>
                <strong>Fecha de respuesta:</strong><br>

                {{ $solicitud->fecha_respuesta
                    ? $solicitud->fecha_respuesta->format('d/m/Y H:i')
                    : 'Pendiente' }}
            </p>

            <p>
                <strong>Estado:</strong><br>

                <span style="
                    display: inline-block;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 13px;
                    font-weight: bold;
                    color: {{ $estadoRespuesta['color'] }};
                    background: {{ $estadoRespuesta['fondo'] }};
                ">
                    {{ $estadoRespuesta['texto'] }}
                </span>
            </p>

        </div>

        {{-- ANS DE SOLUCIÓN --}}

        <div style="
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 18px;
        ">

            <h4 style="margin-top: 0;">
                Tiempo de solución
            </h4>

            <p>
                <strong>Fecha límite:</strong><br>

                {{ $solicitud->ans_solucion_limite
                    ? $solicitud->ans_solucion_limite->format('d/m/Y H:i')
                    : 'Sin asignar' }}
            </p>

            <p>
                <strong>Fecha de solución:</strong><br>

                {{ $solicitud->fecha_solucion
                    ? $solicitud->fecha_solucion->format('d/m/Y H:i')
                    : 'Pendiente' }}
            </p>

            <p>
                <strong>Estado:</strong><br>

                <span style="
                    display: inline-block;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 13px;
                    font-weight: bold;
                    color: {{ $estadoSolucion['color'] }};
                    background: {{ $estadoSolucion['fondo'] }};
                ">
                    {{ $estadoSolucion['texto'] }}
                </span>
            </p>

        </div>

    </div>

    <hr>

    {{-- ATENCIÓN TÉCNICA: SOLO TÉCNICO ASIGNADO --}}

    @can('attend-requests')

        @if (
            (int) $solicitud->tecnico_id === (int) auth()->id()
            && !in_array($solicitud->estado, ['resuelta', 'cerrada'], true)
        )

            <h3>Atención técnica</h3>

            <p>
                Registra el diagnóstico y el trabajo realizado
                sobre el equipo.
            </p>

            <form
                action="{{ route('solicitudes.atencion', $solicitud->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="diagnostico">
                        Diagnóstico técnico
                    </label>

                    <textarea
                        name="diagnostico"
                        id="diagnostico"
                        rows="5"
                        maxlength="5000"
                        required
                        placeholder="Describe las pruebas realizadas y la causa del problema..."
                    >{{ old('diagnostico', $solicitud->diagnostico) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="solucion">
                        Solución aplicada
                    </label>

                    <textarea
                        name="solucion"
                        id="solucion"
                        rows="5"
                        maxlength="5000"
                        placeholder="Completa este campo cuando hayas solucionado el problema..."
                    >{{ old('solucion') }}</textarea>
                </div>

                <button type="submit" class="btn">
                    Guardar atención técnica
                </button>

            </form>

        @endif

    @endcan

    {{-- CLASIFICACIÓN Y ASIGNACIÓN: ADMINISTRADOR / COORDINADOR --}}

    @can('assign-requests')

        @if (
            $solicitud->estado === 'pendiente'
            && $solicitud->tecnico_id === null
            && $solicitud->fecha_escalamiento === null
        )

            <hr>

            <h3>Clasificar y asignar solicitud</h3>

            <form
                action="{{ route('solicitudes.asignar', $solicitud->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="prioridad">
                        Prioridad oficial
                    </label>

                    <select name="prioridad" id="prioridad" required>
                        <option value="">Selecciona prioridad</option>

                        <option
                            value="baja"
                            @selected(old('prioridad', $solicitud->prioridad) === 'baja')
                        >
                            Baja
                        </option>

                        <option
                            value="media"
                            @selected(old('prioridad', $solicitud->prioridad) === 'media')
                        >
                            Media
                        </option>

                        <option
                            value="alta"
                            @selected(old('prioridad', $solicitud->prioridad) === 'alta')
                        >
                            Alta
                        </option>

                        <option
                            value="critica"
                            @selected(old('prioridad', $solicitud->prioridad) === 'critica')
                        >
                            Crítica
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tecnico_id">
                        Técnico responsable
                    </label>

                    <select name="tecnico_id" id="tecnico_id" required>
                        <option value="">Selecciona un técnico</option>

                        @foreach (
                            \App\Models\User::where('role', 'soporte_tecnico')
                                ->where('estado', 'activo')
                                ->orderBy('name')
                                ->get() as $tecnico
                        )
                            <option
                                value="{{ $tecnico->id }}"
                                @selected(old('tecnico_id', $solicitud->tecnico_id) == $tecnico->id)
                            >
                                {{ $tecnico->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group">
                    <label for="observacion">
                        Observaciones
                    </label>

                    <textarea
                        name="observacion"
                        id="observacion"
                        rows="3"
                        maxlength="2000"
                        placeholder="Observaciones de la clasificación o asignación..."
                    >{{ old('observacion') }}</textarea>
                </div>

                <button type="submit" class="btn">
                    Escalar y asignar técnico
                </button>

            </form>

        @elseif ($solicitud->tecnico_id !== null)

            <div class="success" style="margin-top: 20px;">

                <h3>Solicitud escalada y asignada</h3>

                <p>
                    <strong>Técnico asignado:</strong>
                    {{ $solicitud->tecnico->name ?? 'No disponible' }}
                </p>

                <p>
                    <strong>Escalado por:</strong>
                    {{ $solicitud->escalador->name ?? 'No disponible' }}
                </p>

                <p>
                    <strong>Fecha de asignación:</strong>
                    {{ $solicitud->fecha_asignacion
                        ? $solicitud->fecha_asignacion->format('d/m/Y H:i')
                        : 'No disponible' }}
                </p>

            </div>

        @endif

    @endcan

    {{-- DIAGNÓSTICO Y SOLUCIÓN REGISTRADOS --}}

    <hr>

    <h3>Resultado de la atención</h3>

    <p>
        <strong>Diagnóstico:</strong>
        {{ $solicitud->diagnostico ?? 'Pendiente' }}
    </p>

    <p>
        <strong>Solución:</strong>
        {{ $solicitud->solucion ?? 'Pendiente' }}
    </p>

    {{-- DEVOLUCIÓN Y CIERRE: SOLO ADMINISTRADOR / COORDINADOR --}}

    @can('assign-requests')

        @if ($solicitud->estado === 'resuelta')

            <hr>

            <h3>Devolver al técnico</h3>

            <p>
                Si la solución no cumple con lo solicitado,
                puedes devolver la solicitud al técnico
                para que continúe la atención.
            </p>

            <form
                action="{{ route('solicitudes.devolver', $solicitud->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="motivo_devolucion">
                        Motivo de devolución
                    </label>

                    <textarea
                        name="motivo_devolucion"
                        id="motivo_devolucion"
                        rows="4"
                        minlength="10"
                        maxlength="2000"
                        required
                        placeholder="Explica qué debe corregir o revisar el técnico..."
                    >{{ old('motivo_devolucion') }}</textarea>
                </div>

                <button type="submit" class="btn">
                    Devolver al técnico
                </button>

            </form>

            <hr>

            <h3>Verificación y cierre de solicitud</h3>

            <p>
                El técnico ha registrado una solución.
                Verifica que el trabajo esté completo antes de cerrar.
            </p>

            <p>
                <strong>Solución registrada:</strong>
                {{ $solicitud->solucion }}
            </p>

            <form
                action="{{ route('solicitudes.cerrar', $solicitud->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="observacion_cierre">
                        Observaciones de verificación
                    </label>

                    <textarea
                        name="observacion"
                        id="observacion_cierre"
                        rows="4"
                        maxlength="2000"
                        required
                        placeholder="Describe la verificación realizada y confirma el resultado..."
                    >{{ old('observacion') }}</textarea>
                </div>

                <button type="submit" class="btn">
                    Verificar y cerrar solicitud
                </button>

            </form>

        @elseif ($solicitud->estado === 'cerrada')

            <hr>

            <div class="success">

                <h3>Solicitud cerrada</h3>

                <p>
                    Esta solicitud ya fue verificada y cerrada.
                </p>

                <p>
                    <strong>Fecha de cierre:</strong>
                    {{ $solicitud->fecha_cierre
                        ? $solicitud->fecha_cierre->format('d/m/Y H:i')
                        : 'No disponible' }}
                </p>

            </div>

        @endif

    @endcan

    {{-- HISTORIAL DE MOVIMIENTOS --}}

    <hr>

    <h3>Historial de movimientos</h3>

    @forelse ($solicitud->historial as $movimiento)

        <div style="padding:12px; border-bottom:1px solid #ddd;">

            <strong>
                {{ ucfirst(str_replace(
                    ['_', '-'],
                    ' ',
                    $movimiento->accion
                )) }}
            </strong>

            <p>
                Usuario:
                {{ $movimiento->usuario->name ?? 'No disponible' }}
            </p>

            <p>
                {{ $movimiento->observacion ?? 'Sin observaciones' }}
            </p>

            <small>
                {{ $movimiento->created_at->format('d/m/Y H:i') }}
            </small>

        </div>

    @empty

        <p>No hay movimientos registrados.</p>

    @endforelse

    <br>

    <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">
        Volver a solicitudes
    </a>

</div>

@endsection
