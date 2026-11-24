
@extends('layouts.app')

@section('title', 'Registrar solicitud')

@section('content')

<div class="card">

    <h1>Registrar solicitud de soporte</h1>

    <p>
        Completa la información del equipo y describe el problema
        que estás presentando.
    </p>

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Errores de validación --}}
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

    <form action="{{ route('solicitudes.store') }}" method="POST">

        @csrf

        <div class="form-group">
            <label for="equipo_id">Equipo afectado</label>

            <select name="equipo_id" id="equipo_id" required>
                <option value="">Selecciona un equipo</option>

                @foreach ($equipos as $equipo)
                    <option
                        value="{{ $equipo->id }}"
                        {{ old('equipo_id') == $equipo->id ? 'selected' : '' }}
                    >
                        {{ $equipo->codigo }} - {{ $equipo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo de problema</label>

            <select name="tipo" id="tipo" required>
                <option value="">Selecciona el tipo de problema</option>

                <option value="Hardware" {{ old('tipo') == 'Hardware' ? 'selected' : '' }}>
                    Hardware
                </option>

                <option value="Software" {{ old('tipo') == 'Software' ? 'selected' : '' }}>
                    Software
                </option>

                <option value="Red o conectividad" {{ old('tipo') == 'Red o conectividad' ? 'selected' : '' }}>
                    Red o conectividad
                </option>

                <option value="Otro" {{ old('tipo') == 'Otro' ? 'selected' : '' }}>
                    Otro
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción del problema</label>

            <textarea
                name="descripcion"
                id="descripcion"
                rows="5"
                maxlength="5000"
                placeholder="Describe detalladamente el problema..."
                required
            >{{ old('descripcion') }}</textarea>
        </div>

        <div class="form-group">
            <label>Estado inicial</label>
            <input type="text" value="Pendiente" disabled>
            <small>
                Mesa de ayuda revisará tu solicitud y establecerá
                la prioridad y el técnico responsable.
            </small>
        </div>

        <button type="submit" class="btn">
            Enviar solicitud
        </button>

        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

@endsection
