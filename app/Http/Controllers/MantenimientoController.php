<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MantenimientoController extends Controller
{
    /**
     * Listado de mantenimientos.
     */
    public function index(Request $request)
    {
        $mantenimientos = Mantenimiento::with(['equipo', 'tecnico'])
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = $request->buscar;

                $query->whereHas('equipo', function ($equipo) use ($buscar) {
                    $equipo->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%");
                });
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                $query->where('estado', $request->estado);
            })
            ->when($request->filled('tipo'), function ($query) use ($request) {
                $query->where('tipo', $request->tipo);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mantenimientos.index', compact('mantenimientos'));
    }

    /**
     * Mostrar formulario de registro.
     */
    public function create()
    {
        $equipos = Equipo::whereIn('estado', [
            'disponible',
            'asignado',
        ])->orderBy('nombre')->get();

        $tecnicos = User::where('role', 'soporte_tecnico')
            ->where('estado', 'activo')
            ->orderBy('name')
            ->get();

        return view('mantenimientos.create', compact(
            'equipos',
            'tecnicos'
        ));
    }

    /**
     * Registrar un mantenimiento.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipo_id' => [
                'required',
                'exists:equipos,id',
            ],
            'tecnico_id' => [
                'required',
                'exists:users,id',
            ],
            'tipo' => [
                'required',
                Rule::in(['preventivo', 'correctivo']),
            ],
            'diagnostico' => [
                'nullable',
                'string',
            ],
            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        $mantenimiento = DB::transaction(function () use ($validated) {

            $equipo = Equipo::whereKey($validated['equipo_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($equipo->estado, ['disponible', 'asignado'], true)) {
                abort(422, 'El equipo no está disponible para mantenimiento.');
            }

            $tecnico = User::findOrFail($validated['tecnico_id']);

            if ($tecnico->role !== 'soporte_tecnico' || $tecnico->estado !== 'activo') {
                abort(422, 'El técnico seleccionado no está activo o no tiene el rol correspondiente.');
            }

            $mantenimiento = Mantenimiento::create([
                'equipo_id' => $equipo->id,
                'tecnico_id' => $tecnico->id,
                'tipo' => $validated['tipo'],
                'estado' => 'en_proceso',
                'fecha_inicio' => now(),
                'diagnostico' => $validated['diagnostico'] ?? null,
                'estado_anterior' => $equipo->estado,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            if ($validated['tipo'] === 'correctivo') {
                  $equipo->update([
                     'estado' => 'mantenimiento',
    ]);
}

            return $mantenimiento;
        });

        return redirect()
            ->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento registrado correctamente.');
    }

    /**
     * Consultar detalle de un mantenimiento.
     */
    public function show(Mantenimiento $mantenimiento)
    {
        $mantenimiento->load(['equipo', 'tecnico']);

        return view('mantenimientos.show', compact('mantenimiento'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Mantenimiento $mantenimiento)
    {
        if ($mantenimiento->estado === 'finalizado') {
            return back()->with('error', 'No se puede editar un mantenimiento finalizado.');
        }

        $tecnicos = User::where('role', 'soporte_tecnico')
            ->where('estado', 'activo')
            ->orderBy('name')
            ->get();

        return view('mantenimientos.edit', compact(
            'mantenimiento',
            'tecnicos'
        ));
    }

    /**
     * Actualizar información del mantenimiento.
     */
    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        if ($mantenimiento->estado === 'finalizado') {
            return back()->with('error', 'No se puede modificar un mantenimiento finalizado.');
        }

        $validated = $request->validate([
            'tecnico_id' => [
                'required',
                'exists:users,id',
            ],
            'estado' => [
                'required',
                Rule::in(['pendiente', 'en_proceso']),
            ],
            'diagnostico' => [
                'nullable',
                'string',
            ],
            'trabajo_realizado' => [
                'nullable',
                'string',
            ],
            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        $tecnico = User::findOrFail($validated['tecnico_id']);

        if ($tecnico->role !== 'soporte_tecnico' || $tecnico->estado !== 'activo') {
            return back()->withErrors([
                'tecnico_id' => 'El técnico seleccionado no está activo o no tiene el rol correspondiente.',
            ])->withInput();
        }

        $mantenimiento->update($validated);

        return redirect()
            ->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento actualizado correctamente.');
    }

    /**
     * Finalizar mantenimiento.
     */
   
public function finalizar(Mantenimiento $mantenimiento)
{
    if ($mantenimiento->estado === 'finalizado') {
        return back()->with(
            'error',
            'Este mantenimiento ya está finalizado.'
        );
    }

    if (empty($mantenimiento->trabajo_realizado)) {
        return back()->with(
            'error',
            'Debes registrar el trabajo realizado antes de finalizar.'
        );
    }

    DB::transaction(function () use ($mantenimiento) {

        // Bloquear y recuperar el mantenimiento
        $mantenimiento = Mantenimiento::whereKey($mantenimiento->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($mantenimiento->estado === 'finalizado') {
            abort(422, 'Este mantenimiento ya está finalizado.');
        }

        // Recuperar el equipo ANTES de consultar su estado
        $equipo = Equipo::whereKey($mantenimiento->equipo_id)
            ->lockForUpdate()
            ->firstOrFail();

        // Finalizar el mantenimiento
        $mantenimiento->update([
            'estado' => 'finalizado',
            'fecha_finalizacion' => now(),
        ]);

        // Restaurar el estado anterior si era correctivo
        if (
            $mantenimiento->tipo === 'correctivo' &&
            $equipo->estado === 'mantenimiento'
        ) {
            $equipo->update([
                'estado' => $mantenimiento->estado_anterior,
            ]);
        }
    });

    return redirect()
        ->route('mantenimientos.show', $mantenimiento)
        ->with('success', 'Mantenimiento finalizado correctamente.');
}
}
