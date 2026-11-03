@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="card" style="max-width:520px; margin:0 auto;">
    <h1>Iniciar sesión</h1>

    @if ($errors->any())
        <div class="alert" style="background:#fee2e2; color:#991b1b;">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST">
        @csrf

        <div class="form-grid" style="grid-template-columns:1fr;">
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" required>
            </div>

            <label>
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Mantener la sesión iniciada
            </label>
        </div>

        <button type="submit" class="btn">Ingresar</button>
    </form>
</div>
@endsection
