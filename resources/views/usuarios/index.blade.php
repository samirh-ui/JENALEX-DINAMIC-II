
@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">

        <div>
            <h1>Gestión de usuarios</h1>
        </div>

        <a href="{{ route('usuarios.create') }}" class="btn">
            + Registrar usuario
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('usuarios.index') }}" method="GET" class="filters">
        <div class="form-group">
            <label for="buscar">Buscar usuario</label>
            <input
                type="search"
                id="buscar"
                name="buscar"
                value="{{ $filtros['buscar'] ?? '' }}"
                placeholder="Nombre, documento, correo, teléfono o cargo"
            >
        </div>

        <div class="form-group">
            <label for="role">Rol</label>
            <select id="role" name="role">
                <option value="">Todos los roles</option>
                @foreach (\App\Models\User::ROLE_LABELS as $value => $label)
                    <option value="{{ $value }}" @selected(($filtros['role'] ?? '') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(($filtros['estado'] ?? '') === 'activo')>Activo</option>
                <option value="inactivo" @selected(($filtros['estado'] ?? '') === 'inactivo')>Inactivo</option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn">Filtrar</button>
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    <h3>Usuarios registrados</h3>
    <p>Mostrando {{ $usuarios->firstItem() ?? 0 }}–{{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} usuarios.</p>

    <div style="overflow-x:auto;">

        <table>
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre completo</th>
                    <th>Correo electrónico</th>
                    <th>Teléfono</th>
                    <th>Cargo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($usuarios as $usuario)

                    <tr>
                        <td>{{ $usuario->documento }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->telefono ?? 'No registrado' }}</td>
                        <td>{{ $usuario->cargo }}</td>
                        <td>{{ $usuario->roleName() }}</td>

                        <td>
                            @if($usuario->estado == 'activo')
                                <span style="background:#dcfce7; color:#166534; padding:5px 10px; border-radius:15px;">
                                    Activo
                                </span>
                            @else
                                <span style="background:#fee2e2; color:#991b1b; padding:5px 10px; border-radius:15px;">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">

                                <a href="{{ route('usuarios.show', $usuario) }}" class="btn">
                                    Ver
                                </a>

                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-secondary">
                                    Editar
                                </a>

                                <form
                                    action="{{ route('usuarios.destroy', $usuario) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Está seguro de eliminar este usuario?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn" style="background:#dc2626;">
                                        Eliminar
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="8" style="text-align:center; padding:25px;">
                            No hay usuarios registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>

    </div>

    <div class="pagination">
        {{ $usuarios->links() }}
    </div>

</div>

@endsection
