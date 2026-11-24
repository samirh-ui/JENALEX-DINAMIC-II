<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    protected $fillable = [
        'equipo_id',
        'usuario_id',
        'entregado_por',
        'fecha_asignacion',
        'fecha_devolucion',
        'recibido_por',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_devolucion' => 'datetime',
        ];
    }

    // Equipo al que pertenece la asignación
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    // Usuario que recibe el equipo
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Usuario que registra la entrega
    public function entregador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    // Usuario que recibe el equipo devuelto
    public function receptor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recibido_por');
    }
}

