
@extends('layouts.app')

@section('title', 'Inventario de equipos')

@section('content')

<div class="card">
    <h1>Inventario de equipos electrónicos</h1>

   

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @can('register-equipment')
        <div style="margin-bottom: 20px;">
            <a href="{{ route('equipos.create') }}" class="btn">
                Registrar equipo
            </a>
        </div>
    @endcan

    <form action="{{ route('equipos.index') }}" method="GET">
        <input
            type="text"
            name="buscar"
            placeholder="Buscar por código, nombre, marca o categoría"
            value="{{ $buscar ?? '' }}"
        >

        <button type="submit" class="btn">
            Buscar
        </button>

        <a href="{{ route('equipos.index') }}" class="btn">
            Limpiar
        </a>
    </form>

    <h3>Equipos registrados</h3>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse($equipos as $equipo)
                <tr>
                    <td>{{ $equipo->codigo }}</td>
                    <td>{{ $equipo->nombre }}</td>
                    <td>{{ $equipo->categoria }}</td>
                    <td>{{ $equipo->marca }}</td>
                    <td>{{ ucfirst($equipo->estado) }}</td>

                    <td>
                        <a href="{{ route('equipos.show', $equipo) }}">
                            Ver
                        </a>

                        @can('edit-equipment')
                            <a href="{{ route('equipos.edit', $equipo) }}">Editar</a>
                        @endcan

                        @can('delete-equipment')
                            <form
                                action="{{ route('equipos.destroy', $equipo) }}"
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('¿Deseas eliminar este equipo?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit">Eliminar</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        No hay equipos registrados todavía.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $equipos->links() }}
    </div>
</div>

@endsection
