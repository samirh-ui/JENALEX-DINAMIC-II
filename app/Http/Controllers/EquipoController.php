<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{
    // Mostrar equipos según el rol del usuario
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => [
                'nullable',
                Rule::in([
                    'disponible',
                    'asignado',
                    'mantenimiento',
                    'baja'
                ])
            ],
        ]);

        $usuario = auth()->user();

        $equipos = Equipo::query()

            // Si es usuario normal, mostrar solo sus equipos asignados
            ->when($usuario->role === 'usuario', function ($query) use ($usuario) {

                $query->whereHas('asignaciones', function ($asignacion) use ($usuario) {

                    $asignacion->where('usuario_id', $usuario->id)
                        ->whereNull('fecha_devolucion');

                });

            })

            // Filtro de búsqueda
            ->when($filtros['buscar'] ?? null, function ($query, $buscar) {

                $query->where(function ($query) use ($buscar) {

                    $query->where('codigo', 'LIKE', "%{$buscar}%")
                        ->orWhere('nombre', 'LIKE', "%{$buscar}%")
                        ->orWhere('marca', 'LIKE', "%{$buscar}%")
                        ->orWhere('categoria', 'LIKE', "%{$buscar}%");

                });

            })

            // Filtro por estado
            ->when($filtros['estado'] ?? null, function ($query, $estado) {

                $query->where('estado', $estado);

            })

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
                Rule::in([
                    'disponible',
                    'asignado',
                    'mantenimiento',
                    'baja'
                ]),
            ],
            'descripcion' => 'nullable|string',
        ]);

        Equipo::create($datos);

        return redirect()
            ->route('equipos.index')
            ->with('success', 'Equipo registrado correctamente.');
    }


    // Mostrar detalle de un equipo
    public function show(Equipo $equipo)
    {
        $usuario = auth()->user();

        // Verificar que el usuario normal tenga el equipo asignado
        if ($usuario->role === 'usuario') {

            $tieneAsignacion = $equipo->asignaciones()
                ->where('usuario_id', $usuario->id)
                ->whereNull('fecha_devolucion')
                ->exists();

            abort_unless($tieneAsignacion, 403);

        }

        // Cargar historial de asignaciones
        $equipo->load([
            'asignaciones.usuario',
            'asignaciones.entregador',
            'asignaciones.receptor'
        ]);

        // Usuarios activos para el formulario de asignación
        $usuarios = User::where('estado', 'activo')
            ->orderBy('name')
            ->get();

        return view('equipos.show', compact(
            'equipo',
            'usuarios'
        ));
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
                Rule::in([
                    'disponible',
                    'asignado',
                    'mantenimiento',
                    'baja'
                ]),
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
        // No eliminar equipos que tengan historial de asignaciones
        if ($equipo->asignaciones()->exists()) {

            return redirect()
                ->route('equipos.index')
                ->with(
                    'error',
                    'No se puede eliminar este equipo porque tiene historial de asignaciones. Puedes marcarlo como dado de baja.'
                );
        }

        $equipo->delete();

        return redirect()
            ->route('equipos.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }
}
