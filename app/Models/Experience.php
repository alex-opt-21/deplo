<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $table = 'experience';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'company',
        'title',
        'descripcion',
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
