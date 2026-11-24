<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Equipo relacionado
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    // Usuario que registró la solicitud
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Técnico asignado
    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    // Usuario que escaló la solicitud
    public function escalador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalado_por');
    }

    // Historial de movimientos
    public function historial(): HasMany
    {
        return $this->hasMany(
            SolicitudHistorial::class,
            'solicitud_id'
        )->latest();
    }
}



