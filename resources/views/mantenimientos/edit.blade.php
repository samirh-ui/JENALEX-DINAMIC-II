
@extends('layouts.app')

@section('title', 'Editar mantenimiento')

@section('content')

<div class="card">

    <h1>Editar mantenimiento</h1>

    <p>
        Equipo:
        <strong>
            {{ $mantenimiento->equipo->codigo }}
            - {{ $mantenimiento->equipo->nombre }}
        </strong>
    </p>

    {{-- ERRORES DE VALIDACIÓN --}}

    @if($errors->any())
        <div class="alert" style="background:#fee2e2;color:#991b1b;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form
        action="{{ route('mantenimientos.update', $mantenimiento) }}"
        method="POST">

        @csrf
        @method('PUT')

        {{-- TÉCNICO RESPONSABLE --}}

        <div class="form-group">

            <label for="tecnico_id">Técnico responsable</label>

            <select name="tecnico_id" id="tecnico_id" required>

                <option value="">Seleccione un técnico</option>

                @foreach($tecnicos as $tecnico)

                    <option value="{{ $tecnico->id }}"
                        @selected(old('tecnico_id', $mantenimiento->tecnico_id) == $tecnico->id)>

                        {{ $tecnico->name }}
                        - {{ $tecnico->documento }}

                    </option>

                @endforeach

            </select>

        </div>

        {{-- ESTADO --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="estado">Estado del mantenimiento</label>

            <select name="estado" id="estado" required>

                <option value="pendiente"
                    @selected(old('estado', $mantenimiento->estado) === 'pendiente')>
                    Pendiente
                </option>

                <option value="en_proceso"
                    @selected(old('estado', $mantenimiento->estado) === 'en_proceso')>
                    En proceso
                </option>

            </select>

        </div>

        {{-- DIAGNÓSTICO --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="diagnostico">Diagnóstico</label>

            <textarea
                name="diagnostico"
                id="diagnostico"
                rows="4"
                placeholder="Describe el diagnóstico...">{{ old('diagnostico', $mantenimiento->diagnostico) }}</textarea>

        </div>

        {{-- TRABAJO REALIZADO --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="trabajo_realizado">Trabajo realizado</label>

            <textarea
                name="trabajo_realizado"
                id="trabajo_realizado"
                rows="5"
                placeholder="Describe las actividades realizadas...">{{ old('trabajo_realizado', $mantenimiento->trabajo_realizado) }}</textarea>

        </div>

        {{-- OBSERVACIONES --}}

        <div class="form-group" style="margin-top:15px;">

            <label for="observaciones">Observaciones</label>

            <textarea
                name="observaciones"
                id="observaciones"
                rows="3"
                placeholder="Observaciones adicionales...">{{ old('observaciones', $mantenimiento->observaciones) }}</textarea>

        </div>

        {{-- BOTONES --}}

        <div style="margin-top:20px;">

            <button type="submit" class="btn">
                Guardar cambios
            </button>

            <a href="{{ route('mantenimientos.show', $mantenimiento) }}"
               class="btn btn-secondary">
                Cancelar
            </a>

        </div>

    </form>

</div>

@endsection
