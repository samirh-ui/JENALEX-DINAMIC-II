
@extends('layouts.app')

@section('title', 'Detalle del usuario')

@section('content')

<div class="card">

    <h1>Información del usuario</h1>

    <table>

        <tbody>
            <tr>
                <th>Nombre completo</th>
                <td>{{ $usuario->name }}</td>
            </tr>

            <tr>
                <th>Documento</th>
                <td>{{ $usuario->documento }}</td>
            </tr>

            <tr>
                <th>Correo electrónico</th>
                <td>{{ $usuario->email }}</td>
            </tr>

            <tr>
                <th>Teléfono</th>
                <td>{{ $usuario->telefono ?? 'No registrado' }}</td>
            </tr>

            <tr>
                <th>Cargo</th>
                <td>{{ $usuario->cargo }}</td>
            </tr>

            <tr>
                <th>Rol</th>
                <td>{{ $usuario->roleName() }}</td>
            </tr>

            <tr>
                <th>Estado</th>
                <td>{{ ucfirst($usuario->estado) }}</td>
            </tr>

            <tr>
                <th>Fecha de registro</th>
                <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </tbody>

    </table>

    <div style="margin-top:25px; display:flex; gap:12px;">

        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn">
            Editar usuario
        </a>

        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            Volver
        </a>

    </div>

</div>

@endsection
