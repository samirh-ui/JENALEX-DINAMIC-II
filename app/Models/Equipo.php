<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'codigo',
        'nombre',
        'categoria',
        'marca',
        'modelo',
        'numero_serie',
        'estado',
        'descripcion'
    ];

    // Un equipo puede tener muchas asignaciones históricas
    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'equipo_id');
    }

    // Consultar solamente la asignación que sigue activa
    public function asignacionActiva()
    {
        return $this->hasOne(Asignacion::class, 'equipo_id')
            ->whereNull('fecha_devolucion');
    }

    public function solicitudes(): HasMany
{
    return $this->hasMany(Solicitud::class, 'equipo_id');
}

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'equipo_id');
    }
}



