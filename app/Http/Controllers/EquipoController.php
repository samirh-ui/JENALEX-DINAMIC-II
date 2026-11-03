<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{
    // Mostrar todos los equipos
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', Rule::in(['disponible', 'asignado', 'mantenimiento', 'baja'])],
        ]);

        $equipos = Equipo::query()
            ->when($filtros['buscar'] ?? null, function ($query, $buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('codigo', 'LIKE', "%{$buscar}%")
                        ->orWhere('nombre', 'LIKE', "%{$buscar}%")
                        ->orWhere('marca', 'LIKE', "%{$buscar}%")
                        ->orWhere('categoria', 'LIKE', "%{$buscar}%");
                });
            })
            ->when($filtros['estado'] ?? null, fn ($query, $estado) => $query->where('estado', $estado))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('equipos.index', compact('equipos', 'filtros'));
    }

    // Mostrar formulario de registro
    public function create()
    {
        return view('equipos.create');
    }

    // Guardar un nuevo equipo
    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:50|unique:equipos,codigo',
            'nombre' => 'required|string|max:150',
            'categoria' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => 'nullable|string|max:100|unique:equipos,numero_serie',
            'estado' => [
                'required',
                Rule::in(['disponible', 'asignado', 'mantenimiento', 'baja']),
            ],
            'descripcion' => 'nullable|string',
        ]);

        Equipo::create($datos);

        return redirect()
            ->route('equipos.index')
            ->with('success', 'Equipo registrado correctamente.');
    }

    // Mostrar información de un equipo
    public function show(Equipo $equipo)
    {
        return view('equipos.show', compact('equipo'));
    }

    // Mostrar formulario de edición
    public function edit(Equipo $equipo)
    {
        return view('equipos.edit', compact('equipo'));
    }

    // Actualizar información de un equipo
    public function update(Request $request, Equipo $equipo)
    {
        $datos = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('equipos', 'codigo')->ignore($equipo->id),
            ],
            'nombre' => 'required|string|max:150',
            'categoria' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('equipos', 'numero_serie')->ignore($equipo->id),
            ],
            'estado' => [
                'required',
                Rule::in(['disponible', 'asignado', 'mantenimiento', 'baja']),
            ],
            'descripcion' => 'nullable|string',
        ]);

        $equipo->update($datos);

        return redirect()
            ->route('equipos.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    // Eliminar un equipo
    public function destroy(Equipo $equipo)
    {
        $equipo->delete();

        return redirect()
            ->route('equipos.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }
}
