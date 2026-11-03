
@extends('layouts.app')

@section('title', 'Detalle del equipo')

@section('content')

<div class="card">

    <h1>Información del equipo</h1>

    <p><strong>Código:</strong> {{ $equipo->codigo }}</p>

    <p><strong>Nombre:</strong> {{ $equipo->nombre }}</p>

    <p><strong>Categoría:</strong> {{ $equipo->categoria }}</p>

    <p><strong>Marca:</strong>
        {{ $equipo->marca ?? 'No especificada' }}
    </p>

    <p><strong>Modelo:</strong>
        {{ $equipo->modelo ?? 'No especificado' }}
    </p>

    <p><strong>Número de serie:</strong>
        {{ $equipo->numero_serie ?? 'No especificado' }}
    </p>

    <p><strong>Estado:</strong>
        {{ ucfirst($equipo->estado) }}
    </p>

    <p><strong>Descripción:</strong>
        {{ $equipo->descripcion ?? 'Sin descripción' }}
    </p>

    <p><strong>Fecha de registro:</strong>
        {{ $equipo->created_at->format('d/m/Y H:i') }}
    </p>

    <a href="{{ route('equipos.index') }}"
       class="btn btn-secondary">
        Volver al inventario
    </a>

    @can('edit-equipment')
        <a href="{{ route('equipos.edit', $equipo) }}" class="btn">
            Editar equipo
        </a>
    @endcan

</div>

@endsection
