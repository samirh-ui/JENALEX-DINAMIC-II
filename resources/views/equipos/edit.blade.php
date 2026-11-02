
@extends('layouts.app')

@section('title', 'Editar equipo')

@section('content')

<div class="card">

    <h1>Editar equipo electrónico</h1>

    <p>Modifica la información del equipo seleccionado.</p>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="error">
            <strong>Por favor, revisa los siguientes campos:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario de actualización --}}
    <form action="{{ route('equipos.update', $equipo) }}" method="POST">

        @csrf

        @method('PUT')

        @include('partials.equipo-form')

        <button type="submit" class="btn">
            Actualizar equipo
        </button>

        <a href="{{ route('equipos.index') }}"
           class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

@endsection
