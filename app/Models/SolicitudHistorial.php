<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudHistorial extends Model
{
    protected $table = 'solicitud_historiales';

    protected $fillable = [
        'solicitud_id',
        'usuario_id',
        'accion',
        'estado_anterior',
        'estado_nuevo',
        'prioridad_anterior',
        'prioridad_nueva',
        'tecnico_anterior',
        'tecnico_nuevo',
        'observacion',
    ];

    // Solicitud a la que pertenece el movimiento
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(
            Solicitud::class,
            'solicitud_id'
        );
    }

    // Usuario que realizó el movimiento
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_id'
        );
    }
}
