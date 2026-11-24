<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'codigo',
        'equipo_id',
        'usuario_id',
        'tipo',
        'descripcion',
        'estado',
        'prioridad',
        'tecnico_id',
        'escalado_por',
        'fecha_escalamiento',
        'fecha_asignacion',
        'diagnostico',
        'solucion',
        'fecha_respuesta',
        'fecha_solucion',
        'fecha_cierre',
        'ans_respuesta_limite',
        'ans_solucion_limite',
    ];

    protected function casts(): array
    {
        return [
            'fecha_escalamiento' => 'datetime',
            'fecha_asignacion' => 'datetime',
            'fecha_respuesta' => 'datetime',
            'fecha_solucion' => 'datetime',
            'fecha_cierre' => 'datetime',
            'ans_respuesta_limite' => 'datetime',
            'ans_solucion_limite' => 'datetime',
        ];
    }

    // Equipo que presenta el problema
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    // Usuario que registra la solicitud
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Técnico asignado por Mesa de ayuda
    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    // Persona de Mesa de ayuda que escaló el caso
    public function escalador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalado_por');
    }
}
