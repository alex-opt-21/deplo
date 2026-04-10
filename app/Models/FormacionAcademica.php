<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FormacionAcademica extends Model
{
    protected $table = 'formacion_academica';

    protected $fillable = [
        'usuario_id',
        'tipo_formacion',
        'institucion',
        'nombre_carrera',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('usuario_id', $userId);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
