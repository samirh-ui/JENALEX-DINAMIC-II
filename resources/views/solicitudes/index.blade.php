
@extends('layouts.app')

@section('title', 'Mesa de ayuda')

@section('content')


<div class="card">

    <h1>Mesa de ayuda</h1>

    <p>Consulta y realiza seguimiento a las solicitudes de soporte.</p>

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @can('register-requests')
        <a href="{{ route('solicitudes.create') }}" class="btn">
            Registrar solicitud
        </a>
    @endcan

    <h2>Solicitudes registradas</h2>

    <div style="overflow-x:auto;">

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Equipo</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Técnico</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->codigo }}</td>

                        <td>
                            {{ $solicitud->equipo->nombre ?? 'Sin equipo' }}
                        </td>

                        <td>
                            {{ $solicitud->usuario->name ?? 'Sin usuario' }}
                        </td>

                        <td>{{ $solicitud->tipo }}</td>

                        <td>
                            {{ $solicitud->prioridad ?? 'Sin clasificar' }}
                        </td>

                        <td>
                            {{ $solicitud->tecnico->name ?? 'Sin asignar' }}
                        </td>

                        <td>{{ ucfirst($solicitud->estado) }}</td>

                        <td>
                            {{ $solicitud->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <a href="{{ route('solicitudes.show', $solicitud) }}">
                                Ver
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="9">
                            No hay solicitudes registradas todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $solicitudes->links() }}
    </div>

</div>

@endsection
