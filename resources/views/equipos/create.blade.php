
@extends('layouts.app')

@section('title', 'Registrar equipo')

@section('content')

<div class="card">

    <h1>Registrar equipo electrónico</h1>

    <p>Completa los siguientes campos para registrar un equipo en el inventario.</p>

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

    {{-- Formulario de registro --}}
    <form action="{{ route('equipos.store') }}" method="POST">

        @csrf

        @include('partials.equipo-form')

        <button type="submit" class="btn">
            Guardar equipo
        </button>

        <a href="{{ route('equipos.index') }}"
           class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

@endsection
