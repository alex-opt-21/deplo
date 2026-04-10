<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'provider',
        'provider_id',
        'biografia',
        'profesion',
        'fecha_nacimiento',
        'ubicacion',
        'foto_perfil',
        'foto_portada',
        'perfil_completado',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'perfil_completado' => 'boolean',
    ];

    public $timestamps = true;

    public function habilidades()
    {
        return $this->hasMany(Habilidad::class, 'usuario_id');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'usuario_id');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'usuario_id');
    }

    public function sociales()
    {
        return $this->hasMany(Social::class, 'usuario_id');
    }

    public function formacionAcademica()
    {
        return $this->hasMany(FormacionAcademica::class, 'usuario_id');
    }
}
