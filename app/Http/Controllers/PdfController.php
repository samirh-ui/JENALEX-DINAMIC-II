<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Asignacion;
use App\Models\Mantenimiento;
use App\Models\Solicitud;
use App\Services\AnsService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    // REPORTE DE EQUIPOS
    public function equipos()
    {
        $equipos = Equipo::query()
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('pdf.equipos', compact('equipos'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('reporte-equipos.pdf');
    }


    // REPORTE DE ASIGNACIONES Y DEVOLUCIONES
    public function asignaciones()
    {
        $asignaciones = Asignacion::with([
            'equipo',
            'usuario',
            'entregador',
            'receptor',
        ])
            ->orderByDesc('fecha_asignacion')
            ->get();

        $pdf = Pdf::loadView(
            'pdf.asignaciones',
            compact('asignaciones')
        );

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('reporte-asignaciones.pdf');
    }


    // REPORTE DE SOLICITUDES DE SOPORTE CON ANS
    public function solicitudes(AnsService $ansService)
    {
        $solicitudes = Solicitud::with([
            'equipo',
            'usuario',
            'tecnico',
        ])
            ->orderByDesc('created_at')
            ->get();

        $solicitudes->each(function ($solicitud) use ($ansService) {
            $solicitud->ans_respuesta_estado =
                $ansService->estadoRespuesta($solicitud);

            $solicitud->ans_solucion_estado =
                $ansService->estadoSolucion($solicitud);
        });

        $pdf = Pdf::loadView(
            'pdf.solicitudes',
            compact('solicitudes')
        );

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('reporte-solicitudes.pdf');
    }


    // REPORTE DE MANTENIMIENTOS
    public function mantenimientos()
    {
        $mantenimientos = Mantenimiento::with([
            'equipo',
            'tecnico',
        ])
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView(
            'pdf.mantenimientos',
            compact('mantenimientos')
        );

        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream('reporte-mantenimientos.pdf');
    }


    // REPORTE GENERAL DEL SISTEMA CON INDICADORES ANS
    public function general(AnsService $ansService)
    {
        // ==========================================
        // ESTADÍSTICAS DE EQUIPOS
        // ==========================================

        $totalEquipos = Equipo::count();

        $equiposPorEstado = Equipo::selectRaw(
            'estado, COUNT(*) as total'
        )
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();


        // ==========================================
        // ESTADÍSTICAS DE ASIGNACIONES
        // ==========================================

        $totalAsignaciones = Asignacion::count();

        $asignacionesActivas = Asignacion::whereNull(
            'fecha_devolucion'
        )->count();

        $asignacionesDevueltas = Asignacion::whereNotNull(
            'fecha_devolucion'
        )->count();


        // ==========================================
        // ESTADÍSTICAS DE SOLICITUDES
        // ==========================================

        $totalSolicitudes = Solicitud::count();

        $solicitudesPorEstado = Solicitud::selectRaw(
            'estado, COUNT(*) as total'
        )
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();


        // ==========================================
        // INDICADORES DE CUMPLIMIENTO ANS
        // ==========================================

        // Obtener todas las solicitudes para evaluar sus indicadores ANS
          $solicitudesAns = Solicitud::all();

        // Contar las solicitudes que tienen ambos tiempos ANS asignados
           $totalSolicitudesAns = $solicitudesAns->filter(function ($solicitud) {
               return $solicitud->ans_respuesta_limite !== null
                 && $solicitud->ans_solucion_limite !== null;
           })->count();

        $ansRespuestaResumen = [
            'cumplido' => 0,
            'incumplido' => 0,
            'vencido' => 0,
            'pendiente' => 0,
            'sin_asignar' => 0,
        ];

        $ansSolucionResumen = [
            'cumplido' => 0,
            'incumplido' => 0,
            'vencido' => 0,
            'pendiente' => 0,
            'sin_asignar' => 0,
        ];

        foreach ($solicitudesAns as $solicitud) {

            $estadoRespuesta = $ansService->estadoRespuesta(
                $solicitud
            );

            $estadoSolucion = $ansService->estadoSolucion(
                $solicitud
            );

            $ansRespuestaResumen[$estadoRespuesta]++;

            $ansSolucionResumen[$estadoSolucion]++;
        }


        // Casos evaluables: cumplidos, incumplidos y vencidos.
        // Los pendientes dentro del plazo no se consideran fallidos.

        $totalRespuestaEvaluada =
            $ansRespuestaResumen['cumplido']
            + $ansRespuestaResumen['incumplido']
            + $ansRespuestaResumen['vencido'];

        $totalSolucionEvaluada =
            $ansSolucionResumen['cumplido']
            + $ansSolucionResumen['incumplido']
            + $ansSolucionResumen['vencido'];

        // Porcentaje de cumplimiento de respuesta

        $porcentajeCumplimientoRespuesta = $totalRespuestaEvaluada > 0
            ? round(
                ($ansRespuestaResumen['cumplido'] / $totalRespuestaEvaluada) * 100,
                2
            )
            : 0;

        // Porcentaje de cumplimiento de solución

        $porcentajeCumplimientoSolucion = $totalSolucionEvaluada > 0
            ? round(
                ($ansSolucionResumen['cumplido'] / $totalSolucionEvaluada) * 100,
                2
            )
            : 0;


        // ==========================================
        // ESTADÍSTICAS DE MANTENIMIENTOS
        // ==========================================

        $totalMantenimientos = Mantenimiento::count();

        $mantenimientosPorTipo = Mantenimiento::selectRaw(
            'tipo, COUNT(*) as total'
        )
            ->groupBy('tipo')
            ->orderBy('tipo')
            ->get();

        $mantenimientosPorEstado = Mantenimiento::selectRaw(
            'estado, COUNT(*) as total'
        )
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();


        // ==========================================
        // GENERAR REPORTE GENERAL
        // ==========================================

        $pdf = Pdf::loadView('pdf.general', compact(
            'totalEquipos',
            'equiposPorEstado',
            'totalAsignaciones',
            'asignacionesActivas',
            'asignacionesDevueltas',
            'totalSolicitudes',
            'solicitudesPorEstado',
            'totalMantenimientos',
            'mantenimientosPorTipo',
            'mantenimientosPorEstado',
            'totalSolicitudesAns',
            'ansRespuestaResumen',
            'ansSolucionResumen',
            'porcentajeCumplimientoRespuesta',
            'porcentajeCumplimientoSolucion'
        ));

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('reporte-general.pdf');
    }
}
