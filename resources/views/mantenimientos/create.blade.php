
@extends('layouts.app')

@section('title', 'Registrar mantenimiento')

@section('content')

<div class="card">

    <h1>Registrar mantenimiento</h1>

    <p>Registra una intervención técnica para un equipo.</p>

    {{-- ERRORES DE VALIDACIÓN --}}

    @if($errors->any())
        <div class="alert" style="background:#fee2e2;color:#991b1b;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('mantenimientos.store') }}" method="POST">

        @csrf

        {{-- SELECCIONAR EQUIPO --}}

        <div class="form-group">

            <label for="equipo_id">Equipo</label>

            <select name="equipo_id" id="equipo_id" required>

                <option value="">Seleccione un equipo</option>

                @foreach($equipos as $equipo)

                    <option value="{{ $equipo->id }}"
                        @selected(old('equipo_id') == $equipo->id)>

                        {{ $equipo->codigo }}
                        - {{ $equipo->nombre }}
                        ({{ ucfirst($equipo->estado) }})

                    </option>

                @endforeach

            </select>

        </div>

        {{-- SELECCIONAR TÉCNICO --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="tecnico_id">Técnico responsable</label>

            <select name="tecnico_id" id="tecnico_id" required>

                <option value="">Seleccione un técnico</option>

                @foreach($tecnicos as $tecnico)

                    <option value="{{ $tecnico->id }}"
                        @selected(old('tecnico_id') == $tecnico->id)>

                        {{ $tecnico->name }}
                        - {{ $tecnico->documento }}

                    </option>

                @endforeach

            </select>

        </div>

        {{-- TIPO DE MANTENIMIENTO --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="tipo">Tipo de mantenimiento</label>

            <select name="tipo" id="tipo" required>

                <option value="">Seleccione el tipo</option>

                <option value="preventivo"
                    @selected(old('tipo') === 'preventivo')>
                    Preventivo
                </option>

                <option value="correctivo"
                    @selected(old('tipo') === 'correctivo')>
                    Correctivo
                </option>

            </select>

        </div>

        {{-- DIAGNÓSTICO --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="diagnostico">Diagnóstico inicial</label>

            <textarea
                name="diagnostico"
                id="diagnostico"
                rows="4"
                placeholder="Describe el problema o las actividades preventivas necesarias...">{{ old('diagnostico') }}</textarea>

        </div>

        {{-- OBSERVACIONES --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="observaciones">Observaciones</label>

            <textarea
                name="observaciones"
                id="observaciones"
                rows="3"
                placeholder="Información adicional...">{{ old('observaciones') }}</textarea>

        </div>

        {{-- BOTONES --}}

        <div style="margin-top:20px;">

            <button type="submit" class="btn">
                Registrar mantenimiento
            </button>

            <a href="{{ route('mantenimientos.index') }}"
               class="btn btn-secondary">
                Cancelar
            </a>

        </div>

    </form>

</div>

@endsection
