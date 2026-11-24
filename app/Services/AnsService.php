<?php

namespace App\Services;

use App\Models\Solicitud;
use Carbon\Carbon;

class AnsService
{
    /**
     * Tiempos máximos según prioridad.
     * Los valores están expresados en minutos.
     */
    public const TIEMPOS = [
        'baja' => [
            'respuesta' => 480,
            'solucion' => 2880,
        ],

        'media' => [
            'respuesta' => 240,
            'solucion' => 1440,
        ],

        'alta' => [
            'respuesta' => 120,
            'solucion' => 480,
        ],

        'critica' => [
            'respuesta' => 30,
            'solucion' => 240,
        ],
    ];

    /**
     * Calcula las fechas límite al asignar prioridad.
     */
    public function calcularLimites(string $prioridad, Carbon $fechaInicio): array
    {
        $tiempos = self::TIEMPOS[$prioridad] ?? null;

        if (!$tiempos) {
            throw new \InvalidArgumentException(
                'La prioridad seleccionada no tiene tiempos ANS configurados.'
            );
        }

        return [
            'ans_respuesta_limite' => $fechaInicio->copy()
                ->addMinutes($tiempos['respuesta']),

            'ans_solucion_limite' => $fechaInicio->copy()
                ->addMinutes($tiempos['solucion']),
        ];
    }

    
    /**
     * Determina el estado del ANS de respuesta.
     */
    public function estadoRespuesta(Solicitud $solicitud): string
    {
        if (!$solicitud->ans_respuesta_limite) {
            return 'sin_asignar';
        }

        if ($solicitud->fecha_respuesta) {
            return $solicitud->fecha_respuesta->lte(
                $solicitud->ans_respuesta_limite
            ) ? 'cumplido' : 'incumplido';
        }

        return now()->gt($solicitud->ans_respuesta_limite)
            ? 'vencido'
            : 'pendiente';
    }

    /**
     * Determina el estado del ANS de solución.
     */
    public function estadoSolucion(Solicitud $solicitud): string
    {
        if (!$solicitud->ans_solucion_limite) {
            return 'sin_asignar';
        }

        if ($solicitud->fecha_solucion) {
            return $solicitud->fecha_solucion->lte(
                $solicitud->ans_solucion_limite
            ) ? 'cumplido' : 'incumplido';
        }

        return now()->gt($solicitud->ans_solucion_limite)
            ? 'vencido'
            : 'pendiente';
    }

}