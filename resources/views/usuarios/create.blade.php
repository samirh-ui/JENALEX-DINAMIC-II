
@extends('layouts.app')

@section('title', 'Registrar usuario')

@section('content')

<div class="card">

    <h1>Registrar usuario</h1>



    @if ($errors->any())
        <div class="alert" style="background:#fee2e2; color:#991b1b;">
            Revise los campos del formulario. Hay información pendiente o incorrecta.
        </div>
    @endif

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        @include('usuarios.usuario-form')

        <div style="display:flex; gap:12px; margin-top:20px;">

            <button type="submit" class="btn">
                Guardar usuario
            </button>

            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </div>
    </form>

</div>

@endsection
