<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Equipo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AsignacionController extends Controller
{
    /**
     * Registrar la entrega de un equipo.
     */
    public function store(Request $request, Equipo $equipo)
    {
        // 1. Validar los datos recibidos
        $datos = $request->validate([
            'usuario_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('estado', 'activo'),
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        // 2. Ejecutar la operación dentro de una transacción
        DB::transaction(function () use ($equipo, $datos) {

            // Bloquear el registro mientras se realiza la asignación
            $equipo = Equipo::whereKey($equipo->id)
                ->lockForUpdate()
                ->firstOrFail();

            // 3. Verificar que el equipo esté disponible
            if ($equipo->estado !== 'disponible') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'equipo' => 'El equipo no está disponible para asignación.',
                ]);
            }

            // 4. Crear el registro de asignación
            Asignacion::create([
                'equipo_id' => $equipo->id,
                'usuario_id' => $datos['usuario_id'],
                'entregado_por' => Auth::id(),
                'fecha_asignacion' => now(),
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            // 5. Actualizar el estado del equipo
            $equipo->update([
                'estado' => 'asignado',
            ]);
        });

        // 6. Regresar al inventario
        return redirect()
            ->route('equipos.show', $equipo)
            ->with('success', 'Equipo asignado correctamente.');
    }

    /**
     * Registrar la devolución de un equipo.
     */
    public function devolver(Asignacion $asignacion)
    {
        DB::transaction(function () use ($asignacion) {

            // Bloquear la asignación
            $asignacion = Asignacion::whereKey($asignacion->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Verificar que no haya sido devuelto anteriormente
            if ($asignacion->fecha_devolucion !== null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'asignacion' => 'Este equipo ya fue devuelto.',
                ]);
            }

            // Bloquear el equipo relacionado
            $equipo = Equipo::whereKey($asignacion->equipo_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Registrar la devolución
            $asignacion->update([
                'fecha_devolucion' => now(),
                'recibido_por' => Auth::id(),
            ]);

            // Actualizar el estado del equipo
            if ($equipo->estado === 'asignado') {
                $equipo->update([
                    'estado' => 'disponible',
                ]);
            }
        });

        return redirect()
            ->route('equipos.show', $asignacion->equipo_id)
            ->with('success', 'Devolución registrada correctamente.');
    }
}

