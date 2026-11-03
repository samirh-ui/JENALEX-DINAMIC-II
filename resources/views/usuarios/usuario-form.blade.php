
<div class="form-grid">

    {{-- Nombre completo --}}
    <div class="form-group">
        <label for="name">Nombre completo *</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $usuario->name ?? '') }}"
            placeholder="Nombre completo del usuario"
            required
        >
        @error('name')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Documento --}}
    <div class="form-group">
        <label for="documento">Documento *</label>
        <input
            type="text"
            id="documento"
            name="documento"
            value="{{ old('documento', $usuario->documento ?? '') }}"
            placeholder="Número de documento"
            required
        >
        @error('documento')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Correo --}}
    <div class="form-group">
        <label for="email">Correo electrónico *</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $usuario->email ?? '') }}"
            placeholder="correo@ejemplo.com"
            required
        >
        @error('email')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Teléfono --}}
    <div class="form-group">
        <label for="telefono">Teléfono</label>
        <input
            type="text"
            id="telefono"
            name="telefono"
            value="{{ old('telefono', $usuario->telefono ?? '') }}"
            placeholder="Número de contacto"
        >
        @error('telefono')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Cargo --}}
    <div class="form-group">
        <label for="cargo">Cargo *</label>
        <input
            type="text"
            id="cargo"
            name="cargo"
            value="{{ old('cargo', $usuario->cargo ?? '') }}"
            placeholder="Cargo del usuario"
            required
        >
        @error('cargo')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Rol --}}
    <div class="form-group">
        <label for="role">Rol *</label>
        <select id="role" name="role" required>
            @foreach (\App\Models\User::ROLE_LABELS as $value => $label)
                <option value="{{ $value }}"
                    {{ old('role', $usuario->role ?? 'usuario') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('role')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Estado --}}
    <div class="form-group">
        <label for="estado">Estado *</label>
        <select id="estado" name="estado" required>
            <option value="activo"
                {{ old('estado', $usuario->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>
                Activo
            </option>

            <option value="inactivo"
                {{ old('estado', $usuario->estado ?? '') == 'inactivo' ? 'selected' : '' }}>
                Inactivo
            </option>
        </select>
        @error('estado')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Contraseña --}}
    <div class="form-group">
        <label for="password">Contraseña *</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Mínimo 8 caracteres"
            {{ isset($usuario) ? '' : 'required' }}
        >
        @error('password')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Confirmar contraseña --}}
    <div class="form-group">
        <label for="password_confirmation">Confirmar contraseña *</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            placeholder="Repita la contraseña"
            {{ isset($usuario) ? '' : 'required' }}
        >
    </div>

</div>
