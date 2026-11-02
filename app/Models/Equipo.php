<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}

