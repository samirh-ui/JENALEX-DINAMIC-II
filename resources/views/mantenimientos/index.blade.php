
@extends('layouts.app')

@section('title', 'Mantenimientos')

@section('content')

<div class="card">

    <h1>Gestión de mantenimientos</h1>

    <p>Consulta y administra los mantenimientos de los equipos.</p>

    {{-- MENSAJES --}}

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert" style="background:#fee2e2;color:#991b1b;">
            {{ session('error') }}
        </div>
    @endif

    {{-- BOTÓN DE REGISTRO --}}

    <a href="{{ route('mantenimientos.create') }}" class="btn">
        Registrar mantenimiento
    </a>

    <hr>

    {{-- FILTROS --}}

    <form action="{{ route('mantenimientos.index') }}"
          method="GET"
          class="filters">

        <div class="form-group">

            <label for="buscar">Buscar equipo</label>

            <input
                type="text"
                name="buscar"
                id="buscar"
                value="{{ request('buscar') }}"
                placeholder="Código o nombre del equipo">

        </div>

        <div class="form-group">

            <label for="tipo">Tipo de mantenimiento</label>

            <select name="tipo" id="tipo">

                <option value="">Todos</option>

                <option value="preventivo"
                    @selected(request('tipo') === 'preventivo')>
                    Preventivo
                </option>

                <option value="correctivo"
                    @selected(request('tipo') === 'correctivo')>
                    Correctivo
                </option>

            </select>

        </div>

        <div class="form-group">

            <label for="estado">Estado</label>

            <select name="estado" id="estado">

                <option value="">Todos</option>

                <option value="pendiente"
                    @selected(request('estado') === 'pendiente')>
                    Pendiente
                </option>

                <option value="en_proceso"
                    @selected(request('estado') === 'en_proceso')>
                    En proceso
                </option>

                <option value="finalizado"
                    @selected(request('estado') === 'finalizado')>
                    Finalizado
                </option>

            </select>

        </div>

        <button type="submit" class="btn">
            Buscar
        </button>

        <a href="{{ route('mantenimientos.index') }}"
           class="btn btn-secondary">
            Limpiar
        </a>

    </form>

    <hr>

    {{-- TABLA DE MANTENIMIENTOS --}}

    <div style="overflow-x:auto;">

        <table>

            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Tipo</th>
                    <th>Técnico responsable</th>
                    <th>Fecha de inicio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($mantenimientos as $mantenimiento)

                    <tr>

                        <td>
                            {{ $mantenimiento->equipo->codigo }}
                            -
                            {{ $mantenimiento->equipo->nombre }}
                        </td>

                        <td>
                            {{ ucfirst($mantenimiento->tipo) }}
                        </td>

                        <td>
                            {{ $mantenimiento->tecnico->name }}
                        </td>

                        <td>
                            {{ $mantenimiento->fecha_inicio
                                ? $mantenimiento->fecha_inicio->format('d/m/Y H:i')
                                : 'No registrada' }}
                        </td>

                        <td>

                            @if($mantenimiento->estado === 'pendiente')

                                <span style="color:#d97706;">
                                    Pendiente
                                </span>

                            @elseif($mantenimiento->estado === 'en_proceso')

                                <span style="color:#2563eb;">
                                    En proceso
                                </span>

                            @else

                                <span style="color:green;">
                                    Finalizado
                                </span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ route('mantenimientos.show', $mantenimiento) }}"
                               class="btn btn-secondary">
                                Ver
                            </a>

                            @if($mantenimiento->estado !== 'finalizado')

                               @can('manage-maintenance')
                                  <a href="{{ route('mantenimientos.edit', $mantenimiento) }}"
                                    class="btn btn-warning">
                                           Editar
                                               </a>
                            @endcan

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            No hay mantenimientos registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINACIÓN --}}

    <div style="margin-top:20px;">
        {{ $mantenimientos->links() }}
    </div>

</div>

@endsection
