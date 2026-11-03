
<div class="form-grid">

    {{-- Código del equipo --}}
    <div class="form-group">
        <label for="codigo">Código del equipo *</label>
        <input
            type="text"
            id="codigo"
            name="codigo"
            value="{{ old('codigo', $equipo->codigo ?? '') }}"
            placeholder="Ej. EQ-001"
            required
        >
        @error('codigo')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Nombre --}}
    <div class="form-group">
        <label for="nombre">Nombre del equipo *</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            value="{{ old('nombre', $equipo->nombre ?? '') }}"
            placeholder="Ej. Computador portátil"
            required
        >
        @error('nombre')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Categoría --}}
    <div class="form-group">
        <label for="categoria">Categoría *</label>
        <select id="categoria" name="categoria" required>
            <option value="">Seleccione una categoría</option>
            @foreach(['Computador', 'Portátil', 'Monitor', 'Impresora', 'Periférico', 'Redes', 'Otro'] as $categoria)
                <option
                    value="{{ $categoria }}"
                    {{ old('categoria', $equipo->categoria ?? '') == $categoria ? 'selected' : '' }}
                >
                    {{ $categoria }}
                </option>
            @endforeach
        </select>
        @error('categoria')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Marca --}}
    <div class="form-group">
        <label for="marca">Marca</label>
        <input
            type="text"
            id="marca"
            name="marca"
            value="{{ old('marca', $equipo->marca ?? '') }}"
            placeholder="Ej. HP, Dell, Lenovo"
        >
        @error('marca')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Modelo --}}
    <div class="form-group">
        <label for="modelo">Modelo</label>
        <input
            type="text"
            id="modelo"
            name="modelo"
            value="{{ old('modelo', $equipo->modelo ?? '') }}"
            placeholder="Modelo del equipo"
        >
        @error('modelo')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Número de serie --}}
    <div class="form-group">
        <label for="numero_serie">Número de serie</label>
        <input
            type="text"
            id="numero_serie"
            name="numero_serie"
            value="{{ old('numero_serie', $equipo->numero_serie ?? '') }}"
            placeholder="Número de serie"
        >
        @error('numero_serie')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Estado --}}
    <div class="form-group">
        <label for="estado">Estado *</label>
        <select id="estado" name="estado" required>
            @foreach([
                'disponible' => 'Disponible',
                'asignado' => 'Asignado',
                'mantenimiento' => 'En mantenimiento',
                'baja' => 'Dado de baja'
            ] as $valor => $nombreEstado)
                <option
                    value="{{ $valor }}"
                    {{ old('estado', $equipo->estado ?? 'disponible') == $valor ? 'selected' : '' }}
                >
                    {{ $nombreEstado }}
                </option>
            @endforeach
        </select>
        @error('estado')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

    {{-- Descripción --}}
    <div class="form-group full-width">
        <label for="descripcion">Descripción</label>
        <textarea
            id="descripcion"
            name="descripcion"
            rows="4"
            placeholder="Información adicional sobre el equipo..."
        >{{ old('descripcion', $equipo->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <small class="error">{{ $message }}</small>
        @enderror
    </div>

</div>
