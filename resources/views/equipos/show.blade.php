
@extends('layouts.app')

@section('title', 'Detalle del equipo')

@section('content')

<div class="card">

    <h1>Información del equipo</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert" style="background:#fee2e2;color:#991b1b;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p><strong>Código:</strong> {{ $equipo->codigo }}</p>
    <p><strong>Nombre:</strong> {{ $equipo->nombre }}</p>
    <p><strong>Categoría:</strong> {{ $equipo->categoria }}</p>
    <p><strong>Marca:</strong> {{ $equipo->marca ?? 'No especificada' }}</p>
    <p><strong>Modelo:</strong> {{ $equipo->modelo ?? 'No especificado' }}</p>
    <p><strong>Número de serie:</strong> {{ $equipo->numero_serie ?? 'No especificado' }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($equipo->estado) }}</p>
    <p><strong>Descripción:</strong> {{ $equipo->descripcion ?? 'Sin descripción' }}</p>

    <hr>

    -- FORMULARIO DE ASIGNACIÓN --

    @can('assign-equipment')

        @if($equipo->estado === 'disponible')

            <h2>Asignar equipo</h2>

            <form action="{{ route('asignaciones.store', $equipo) }}"
                  method="POST">

                @csrf

                <div class="form-group">
                    <label for="usuario_id">
                        Seleccionar usuario responsable
                    </label>

                    <select name="usuario_id" id="usuario_id" required>

                        <option value="">Seleccione un usuario</option>

                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}"
                                {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>

                                {{ $usuario->name }}
                                - {{ $usuario->documento }}

                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group" style="margin-top:15px;">

                    <label for="observaciones">
                        Observaciones de la entrega
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                        rows="3"
                        placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>

                </div>

                <button type="submit" class="btn" style="margin-top:15px;">
                    Registrar asignación
                </button>

            </form>

        @else

            <p>
                <strong>
                    Este equipo no está disponible para una nueva asignación.
                </strong>
            </p>

        @endif

    @endcan

    <hr>

    -- HISTORIAL DE ASIGNACIONES --

    <h2>Historial de asignaciones</h2>

    @if($equipo->asignaciones->isEmpty())

        <p>Este equipo todavía no tiene asignaciones registradas.</p>

    @else

        <div style="overflow-x:auto;">

            <table>

                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Entregado por</th>
                        <th>Fecha de entrega</th>
                        <th>Fecha de devolución</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($equipo->asignaciones->sortByDesc('id') as $asignacion)

                        <tr>

                            <td>{{ $asignacion->usuario->name }}</td>

                            <td>{{ $asignacion->entregador->name }}</td>

                            <td>
                                {{ $asignacion->fecha_asignacion->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $asignacion->fecha_devolucion
                                    ? $asignacion->fecha_devolucion->format('d/m/Y H:i')
                                    : 'Pendiente' }}
                            </td>

                            <td>
                                @if($asignacion->fecha_devolucion)
                                    <span style="color:green;">Devuelto</span>
                                @else
                                    <span style="color:#2563eb;">Asignado</span>
                                @endif
                            </td>

                            <td>

                                @if(!$asignacion->fecha_devolucion)

                                    @can('assign-equipment')

                                        <form
                                            action="{{ route('asignaciones.devolver', $asignacion) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Confirmas la devolución de este equipo?')">

                                            @csrf
                                            @method('PUT')

                                            <button type="submit" class="btn btn-secondary">
                                                Registrar devolución
                                            </button>

                                        </form>

                                    @endcan

                                @else

                                    {{ $asignacion->receptor?->name ?? 'No registrado' }}

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

            

        </div>

    @endif

<hr>

{{-- HOJA DE VIDA DEL EQUIPO --}}

<h2>Hoja de vida del equipo</h2>

<p>
    Aquí puedes consultar el historial de entregas y devoluciones
    de este dispositivo.
</p>

@if($equipo->asignaciones->isEmpty())

    <div class="alert">
        Este equipo todavía no tiene movimientos registrados.
    </div>

@else

    <div style="overflow-x:auto;">

        <table>

            <thead>
                <tr>
                    <th>Movimiento</th>
                    <th>Usuario responsable</th>
                    <th>Fecha</th>
                    <th>Responsable del proceso</th>
                    <th>Observaciones</th>
                </tr>
            </thead>

            <tbody>

                @foreach($equipo->asignaciones->sortBy('fecha_asignacion') as $asignacion)

                    {{-- MOVIMIENTO DE ENTREGA --}}

                    <tr>

                        <td>
                            <strong>Entrega</strong>
                        </td>

                        <td>
                            {{ $asignacion->usuario->name }}
                        </td>

                        <td>
                            {{ $asignacion->fecha_asignacion->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            {{ $asignacion->entregador->name }}
                        </td>

                        <td>
                            {{ $asignacion->observaciones ?? 'Sin observaciones' }}
                        </td>

                    </tr>

                    {{-- MOVIMIENTO DE DEVOLUCIÓN --}}

                    @if($asignacion->fecha_devolucion)

                        <tr>

                            <td>
                                <strong>Devolución</strong>
                            </td>

                            <td>
                                {{ $asignacion->usuario->name }}
                            </td>

                            <td>
                                {{ $asignacion->fecha_devolucion->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $asignacion->receptor?->name ?? 'No registrado' }}
                            </td>

                            <td>
                                Devolución registrada
                            </td>

                        </tr>

                    @endif

                @endforeach

            </tbody>

        </table>

    </div>

@endif


    <hr>

    <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
        Volver al inventario
    </a>

    @can('edit-equipment')
        <a href="{{ route('equipos.edit', $equipo) }}" class="btn">
            Editar equipo
        </a>
    @endcan

</div>

@endsection
