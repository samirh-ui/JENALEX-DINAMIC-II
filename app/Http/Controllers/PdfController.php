<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Asignacion;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Mantenimiento;
use App\Models\Solicitud;

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


    // REPORTE DE SOLICITUDES DE SOPORTE
    public function solicitudes()
    {
        $solicitudes = \App\Models\Solicitud::with([
            'equipo',
            'usuario',
            'tecnico',
        ])
        ->orderByDesc('created_at')
        ->get();

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


    // REPORTE GENERAL DEL SISTEMA
    public function general()
    {
        // Estadísticas de equipos
        $totalEquipos = Equipo::count();

        $equiposPorEstado = Equipo::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        // Estadísticas de asignaciones
        $totalAsignaciones = Asignacion::count();

        $asignacionesActivas = Asignacion::whereNull('fecha_devolucion')->count();

        $asignacionesDevueltas = Asignacion::whereNotNull('fecha_devolucion')->count();

        // Estadísticas de solicitudes
        $totalSolicitudes = Solicitud::count();

        $solicitudesPorEstado = Solicitud::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        // Estadísticas de mantenimientos
        $totalMantenimientos = Mantenimiento::count();

        $mantenimientosPorTipo = Mantenimiento::selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->orderBy('tipo')
            ->get();

        $mantenimientosPorEstado = Mantenimiento::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

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
            'mantenimientosPorEstado'
        ));

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('reporte-general.pdf');
    }



}

