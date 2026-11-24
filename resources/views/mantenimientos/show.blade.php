
@extends('layouts.app')

@section('title', 'Detalle del mantenimiento')

@section('content')

<div class="container">

    <div class="card">
        <h2>Detalle del mantenimiento</h2>

        <div style="margin-bottom: 20px;">
            <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">
                Volver a mantenimientos
            </a>

            @can('manage-maintenance')
                @if($mantenimiento->estado !== 'finalizado')
                    <a href="{{ route('mantenimientos.edit', $mantenimiento) }}"
                       class="btn btn-warning">
                        Editar mantenimiento
                    </a>
                @endif
            @endcan
        </div>

        <h3>Información general</h3>

        <table class="table">
            <tr>
                <th>Equipo</th>
                <td>
                    {{ $mantenimiento->equipo?->codigo ?? 'N/A' }}
                    -
                    {{ $mantenimiento->equipo?->nombre ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <th>Tipo de mantenimiento</th>
                <td>{{ ucfirst($mantenimiento->tipo) }}</td>
            </tr>

            <tr>
                <th>Técnico responsable</th>
                <td>{{ $mantenimiento->tecnico?->name ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>Estado</th>
                <td>
                    {{ ucfirst(str_replace('_', ' ', $mantenimiento->estado)) }}
                </td>
            </tr>

            <tr>
                <th>Fecha de inicio</th>
                <td>
                    {{ $mantenimiento->fecha_inicio?->format('d/m/Y H:i') ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <th>Fecha de finalización</th>
                <td>
                    {{ $mantenimiento->fecha_finalizacion?->format('d/m/Y H:i') ?? 'Pendiente' }}
                </td>
            </tr>
        </table>

        <h3>Diagnóstico y trabajo realizado</h3>

        <table class="table">
            <tr>
                <th>Diagnóstico</th>
                <td>{{ $mantenimiento->diagnostico ?? 'Sin diagnóstico registrado' }}</td>
            </tr>

            <tr>
                <th>Trabajo realizado</th>
                <td>{{ $mantenimiento->trabajo_realizado ?? 'Pendiente' }}</td>
            </tr>

            <tr>
                <th>Observaciones</th>
                <td>{{ $mantenimiento->observaciones ?? 'Sin observaciones' }}</td>
            </tr>
        </table>

        @can('manage-maintenance')

            @if($mantenimiento->estado !== 'finalizado')

                <hr>

                <h3>Actualizar mantenimiento</h3>

                <form action="{{ route('mantenimientos.update', $mantenimiento) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="tecnico_id">Técnico responsable</label>

                        <select name="tecnico_id" id="tecnico_id" required>
                            @foreach(
                                \App\Models\User::where('role', 'soporte_tecnico')
                                    ->where('estado', 'activo')
                                    ->orderBy('name')
                                    ->get() as $tecnico
                            )
                                <option value="{{ $tecnico->id }}"
                                    {{ old('tecnico_id', $mantenimiento->tecnico_id) == $tecnico->id ? 'selected' : '' }}>
                                    {{ $tecnico->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('tecnico_id')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>

                        <select name="estado" id="estado" required>
                            <option value="pendiente"
                                {{ old('estado', $mantenimiento->estado) == 'pendiente' ? 'selected' : '' }}>
                                Pendiente
                            </option>

                            <option value="en_proceso"
                                {{ old('estado', $mantenimiento->estado) == 'en_proceso' ? 'selected' : '' }}>
                                En proceso
                            </option>
                        </select>

                        @error('estado')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="diagnostico">Diagnóstico</label>

                        <textarea name="diagnostico" id="diagnostico"
                                  rows="3">{{ old('diagnostico', $mantenimiento->diagnostico) }}</textarea>

                        @error('diagnostico')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="trabajo_realizado">Trabajo realizado</label>

                        <textarea name="trabajo_realizado" id="trabajo_realizado"
                                  rows="3">{{ old('trabajo_realizado', $mantenimiento->trabajo_realizado) }}</textarea>

                        @error('trabajo_realizado')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>

                        <textarea name="observaciones" id="observaciones"
                                  rows="3">{{ old('observaciones', $mantenimiento->observaciones) }}</textarea>

                        @error('observaciones')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </form>

                <hr>

                <h3>Finalizar mantenimiento</h3>

                <p>
                    Antes de finalizar, asegúrate de haber registrado
                    el trabajo realizado.
                </p>

                <form action="{{ route('mantenimientos.finalizar', $mantenimiento) }}"
                      method="POST"
                      onsubmit="return confirm('¿Estás seguro de finalizar este mantenimiento?')">

                    @csrf
                    @method('PUT')

                    <button type="submit" class="btn btn-success">
                        Finalizar mantenimiento
                    </button>
                </form>

            @else

                <p style="color:green; font-weight:bold;">
                    Este mantenimiento ya se encuentra finalizado.
                </p>

            @endif

        @else

            <hr>

            <p>
                Puedes consultar la información de este mantenimiento.
                No tienes permisos para modificarlo ni finalizarlo.
            </p>

        @endcan

    </div>

</div>

@endsection
