<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\AnsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SolicitudController extends Controller
{
    // Mostrar todas las solicitudes
    public function index()
    {
        $usuario = Auth::user();

        // Administrador y coordinador: todas las solicitudes
        if ($usuario->hasPermission('assign-requests')) {

            $solicitudes = Solicitud::with([
                'equipo',
                'usuario',
                'tecnico'
            ])
                ->latest()
                ->paginate(10);

        }
        // Técnico: únicamente las solicitudes asignadas a él
        elseif ($usuario->hasPermission('attend-requests')) {

            $solicitudes = Solicitud::with([
                'equipo',
                'usuario',
                'tecnico'
            ])
                ->where('tecnico_id', $usuario->id)
                ->latest()
                ->paginate(10);

        }
        // Usuario: únicamente las solicitudes que registró
        elseif ($usuario->hasPermission('view-own-request-tracking')) {

            $solicitudes = Solicitud::with([
                'equipo',
                'usuario',
                'tecnico'
            ])
                ->where('usuario_id', $usuario->id)
                ->latest()
                ->paginate(10);

        } else {
            abort(403, 'No tienes permiso para consultar solicitudes.');
        }

        return view('solicitudes.index', compact('solicitudes'));
    }


    // Mostrar formulario para registrar una solicitud
    public function create()
    {
        $equipos = Equipo::where('estado', '!=', 'baja')
            ->orderBy('nombre')
            ->get();

        return view('solicitudes.create', compact('equipos'));
    }


    // Registrar una nueva solicitud
    public function store(Request $request)
    {
        $datos = $request->validate([
            'equipo_id' => [
                'required',
                'exists:equipos,id',
            ],
            'tipo' => [
                'required',
                'string',
                'max:100',
            ],
            'descripcion' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $equipo = Equipo::findOrFail($datos['equipo_id']);

        if ($equipo->estado === 'baja') {
            return back()
                ->withInput()
                ->withErrors([
                    'equipo_id' => 'No puedes reportar un equipo dado de baja.',
                ]);
        }

        DB::transaction(function () use ($datos) {

            $solicitud = Solicitud::create([
                'codigo' => 'SOL-' . now()->format('YmdHis')
                    . '-' . Str::upper(Str::random(5)),

                'equipo_id' => $datos['equipo_id'],

                // El usuario se obtiene de la sesión iniciada
                'usuario_id' => Auth::id(),

                'tipo' => $datos['tipo'],
                'descripcion' => $datos['descripcion'],

                // Mesa de ayuda definirá prioridad y técnico
                'estado' => 'pendiente',
                'prioridad' => null,
                'tecnico_id' => null,
                'escalado_por' => null,
            ]);

            // Registrar la creación en el historial
            $solicitud->historial()->create([
                'usuario_id' => Auth::id(),
                'accion' => 'registro',
                'estado_nuevo' => 'pendiente',
                'observacion' => 'Solicitud registrada por el usuario.',
            ]);
        });

        return redirect()
            ->route('solicitudes.create')
            ->with('success', 'Tu solicitud fue registrada correctamente.');
    }


    // Consultar el detalle de una solicitud
    public function show(Solicitud $solicitud, AnsService $ansService)
    {
        $usuario = Auth::user();

        if ($usuario->hasPermission('assign-requests')) {

            // Administrador y coordinador pueden ver todos los tickets

        } elseif ($usuario->hasPermission('attend-requests')) {

            // El técnico solo puede ver los tickets asignados a él
            if ((int) $solicitud->tecnico_id !== (int) $usuario->id) {
                abort(403, 'Esta solicitud no está asignada a tu usuario.');
            }

        } elseif ($usuario->hasPermission('view-own-request-tracking')) {

            // El usuario solo puede ver los tickets que registró
            if ((int) $solicitud->usuario_id !== (int) $usuario->id) {
                abort(403, 'No tienes permiso para consultar esta solicitud.');
            }

        } else {
            abort(403, 'No tienes permiso para consultar solicitudes.');
        }

        // Consultar cumplimiento de los tiempos ANS
        $ansRespuestaEstado = $ansService->estadoRespuesta($solicitud);
        $ansSolucionEstado = $ansService->estadoSolucion($solicitud);

        return view('solicitudes.show', compact(
            'solicitud',
            'ansRespuestaEstado',
            'ansSolucionEstado'
        ));
    }


    // Clasificar, escalar y asignar una solicitud
    public function asignar(
        Request $request,
        Solicitud $solicitud,
        AnsService $ansService
    ) {
        // Solo administrador y coordinador pueden gestionar solicitudes
        if (!Auth::user()->hasPermission('assign-requests')) {
            abort(403, 'No tienes permiso para asignar solicitudes.');
        }

        // Evitar que una solicitud sea escalada más de una vez
        if (
            $solicitud->fecha_escalamiento !== null ||
            $solicitud->tecnico_id !== null
        ) {
            return back()->withErrors([
                'solicitud' => 'Esta solicitud ya fue escalada y tiene un técnico asignado.',
            ]);
        }

        // No permitir gestionar solicitudes cerradas
        if (in_array($solicitud->estado, ['cerrada', 'resuelta'], true)) {
            return back()->withErrors([
                'solicitud' => 'Esta solicitud ya está resuelta o cerrada.',
            ]);
        }

        // Validar los datos recibidos
        $datos = $request->validate([
            'prioridad' => [
                'required',
                'in:baja,media,alta,critica',
            ],
            'tecnico_id' => [
                'required',
                'exists:users,id',
            ],
            'observacion' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        // Comprobar que el usuario seleccionado sea un técnico activo
        $tecnico = User::where('id', $datos['tecnico_id'])
            ->where('role', 'soporte_tecnico')
            ->where('estado', 'activo')
            ->first();

        if (!$tecnico) {
            return back()->withErrors([
                'tecnico_id' => 'Selecciona un técnico activo.',
            ])->withInput();
        }

        DB::transaction(function () use (
            $solicitud,
            $datos,
            $tecnico,
            $ansService
        ) {

            // Bloquear la solicitud durante la asignación
            $solicitudBloqueada = Solicitud::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Evitar una segunda asignación o escalamiento
            if (
                $solicitudBloqueada->fecha_escalamiento !== null ||
                $solicitudBloqueada->tecnico_id !== null
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'solicitud' => 'Esta solicitud ya fue escalada y asignada.',
                ]);
            }

            // Guardar los valores anteriores antes de actualizar
            $estadoAnterior = $solicitudBloqueada->estado;
            $prioridadAnterior = $solicitudBloqueada->prioridad;
            $tecnicoAnterior = $solicitudBloqueada->tecnico_id;

            // Fecha de inicio del ANS
            $fechaAsignacion = now();

            // Calcular automáticamente los límites del ANS
            $limitesAns = $ansService->calcularLimites(
                $datos['prioridad'],
                $fechaAsignacion
            );

            // Guardar asignación y fechas límite
            $solicitudBloqueada->update([
                'prioridad' => $datos['prioridad'],
                'tecnico_id' => $tecnico->id,
                'escalado_por' => Auth::id(),
                'fecha_escalamiento' => $fechaAsignacion,
                'fecha_asignacion' => $fechaAsignacion,
                'ans_respuesta_limite' => $limitesAns['ans_respuesta_limite'],
                'ans_solucion_limite' => $limitesAns['ans_solucion_limite'],
                'estado' => 'escalada',
            ]);

            // Registrar el movimiento en el historial
            $solicitudBloqueada->historial()->create([
                'usuario_id' => Auth::id(),
                'accion' => 'escalamiento_y_asignacion',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'escalada',
                'prioridad_anterior' => $prioridadAnterior,
                'prioridad_nueva' => $datos['prioridad'],
                'tecnico_anterior' => $tecnicoAnterior,
                'tecnico_nuevo' => $tecnico->id,
                'observacion' => $datos['observacion']
                    ?? 'Solicitud clasificada, asignada y con tiempos ANS calculados.',
            ]);
        });

        return redirect()
            ->route('solicitudes.show', $solicitud->id)
            ->with('success', 'Solicitud escalada y asignada correctamente.');
    }


    // Registrar diagnóstico y solución del técnico
    public function actualizarAtencion(Request $request, Solicitud $solicitud)
    {
        $usuario = Auth::user();

        // Verificar permiso de atención técnica
        if (!$usuario->hasPermission('attend-requests')) {
            abort(403, 'No tienes permiso para atender solicitudes.');
        }

        // Verificar que el ticket esté asignado al técnico autenticado
        if ((int) $solicitud->tecnico_id !== (int) $usuario->id) {
            abort(403, 'Esta solicitud no está asignada a tu usuario.');
        }

        // No permitir modificar solicitudes cerradas o ya resueltas
        if (in_array($solicitud->estado, ['cerrada', 'resuelta'], true)) {
            return back()->withErrors([
                'solicitud' => 'Esta solicitud ya fue resuelta o cerrada.',
            ]);
        }

        $datos = $request->validate([
            'diagnostico' => [
                'required',
                'string',
                'max:5000',
            ],
            'solucion' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        DB::transaction(function () use ($solicitud, $datos) {

            // Bloquear la solicitud durante la actualización
            $solicitudBloqueada = Solicitud::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Comprobar nuevamente que el técnico siga asignado
            if ((int) $solicitudBloqueada->tecnico_id !== (int) Auth::id()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'solicitud' => 'Esta solicitud ya no está asignada a tu usuario.',
                ]);
            }

            if (in_array($solicitudBloqueada->estado, ['resuelta', 'cerrada'], true)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'solicitud' => 'Esta solicitud ya fue resuelta o cerrada.',
                ]);
            }

            $estadoAnterior = $solicitudBloqueada->estado;

            $solicitudBloqueada->diagnostico = $datos['diagnostico'];

            // Registrar la primera respuesta del técnico
            if (!$solicitudBloqueada->fecha_respuesta) {
                $solicitudBloqueada->fecha_respuesta = now();
            }

            if (!empty($datos['solucion'])) {
                $solicitudBloqueada->solucion = $datos['solucion'];
                $solicitudBloqueada->fecha_solucion = now();
                $solicitudBloqueada->estado = 'resuelta';

                $accion = 'solucion_registrada';
                $estadoNuevo = 'resuelta';

            } else {
                $solicitudBloqueada->estado = 'en_proceso';

                $accion = 'diagnostico_actualizado';
                $estadoNuevo = 'en_proceso';
            }

            $solicitudBloqueada->save();

            // Registrar el movimiento en el historial
            $solicitudBloqueada->historial()->create([
                'usuario_id' => Auth::id(),
                'accion' => $accion,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'observacion' => !empty($datos['solucion'])
                    ? 'El técnico registró la solución.'
                    : 'El técnico registró o actualizó el diagnóstico.',
            ]);
        });

        return redirect()
            ->route('solicitudes.show', $solicitud->id)
            ->with(
                'success',
                !empty($datos['solucion'])
                    ? 'Solución registrada. Pendiente de verificación por Mesa de ayuda.'
                    : 'Diagnóstico registrado correctamente.'
            );
    }


    // Cerrar una solicitud después de verificar la solución
    public function cerrar(Request $request, Solicitud $solicitud)
    {
        // Solo administrador y coordinador pueden cerrar solicitudes
        if (!Auth::user()->hasPermission('assign-requests')) {
            abort(403, 'No tienes permiso para cerrar solicitudes.');
        }

        $datos = $request->validate([
            'observacion' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use ($solicitud, $datos) {

            // Bloquear el registro durante el cierre
            $solicitudBloqueada = Solicitud::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Solo se pueden cerrar solicitudes resueltas
            if ($solicitudBloqueada->estado !== 'resuelta') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'solicitud' => 'Solo puedes cerrar solicitudes resueltas.',
                ]);
            }

            if (empty($solicitudBloqueada->solucion)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'solicitud' => 'La solicitud no tiene una solución registrada.',
                ]);
            }

            $estadoAnterior = $solicitudBloqueada->estado;

            // Registrar el cierre
            $solicitudBloqueada->update([
                'estado' => 'cerrada',
                'fecha_cierre' => now(),
            ]);

            // Registrar el cierre en el historial
            $solicitudBloqueada->historial()->create([
                'usuario_id' => Auth::id(),
                'accion' => 'solicitud_cerrada',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'cerrada',
                'observacion' => $datos['observacion'],
            ]);
        });

        return redirect()
            ->route('solicitudes.show', $solicitud->id)
            ->with('success', 'Solicitud verificada y cerrada correctamente.');
    }


    // Devolver una solicitud al técnico para continuar la atención
    public function devolver(Request $request, Solicitud $solicitud)
    {
        // Solo Mesa de ayuda puede devolver solicitudes
        if (!Auth::user()->hasPermission('assign-requests')) {
            abort(403, 'No tienes permiso para devolver solicitudes.');
        }

        $datos = $request->validate([
            'motivo_devolucion' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use ($solicitud, $datos) {

            $solicitudBloqueada = Solicitud::whereKey($solicitud->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Solo se pueden devolver solicitudes resueltas
            if ($solicitudBloqueada->estado !== 'resuelta') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'solicitud' => 'Solo puedes devolver solicitudes que estén resueltas.',
                ]);
            }

            $estadoAnterior = $solicitudBloqueada->estado;

            // La solicitud vuelve a estar en proceso
            $solicitudBloqueada->update([
                'estado' => 'en_proceso',
                'fecha_solucion' => null,
            ]);

            // Registrar la devolución en el historial
            $solicitudBloqueada->historial()->create([
                'usuario_id' => Auth::id(),
                'accion' => 'solicitud_devuelta',
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'en_proceso',
                'observacion' => $datos['motivo_devolucion'],
            ]);
        });

        return redirect()
            ->route('solicitudes.show', $solicitud->id)
            ->with('success', 'La solicitud fue devuelta al técnico correctamente.');
    }
}
