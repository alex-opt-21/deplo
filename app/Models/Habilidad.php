<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Habilidad extends Model
{
    protected $table = 'habilidades';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'nombre',
        'tipo',
        'nivel_cuantitativo',
        'nivel_cualitativo',
        'descripcion',
        'categorÃ­a',
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
