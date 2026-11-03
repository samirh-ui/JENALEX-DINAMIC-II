
@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')

<div class="card">

    <h1>Editar usuario</h1>



    @if ($errors->any())
        <div class="alert" style="background:#fee2e2; color:#991b1b;">
            Revise los campos del formulario. Hay información incorrecta.
        </div>
    @endif

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST">

        @csrf
        @method('PUT')

        @include('usuarios.usuario-form')

        <div style="display:flex; gap:12px; margin-top:20px;">

            <button type="submit" class="btn">
                Actualizar usuario
            </button>

            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </div>

    </form>

</div>

@endsection
