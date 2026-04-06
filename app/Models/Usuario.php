<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens;
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

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function social()
    {
        return $this->hasMany(Social::class, 'usuario_id');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'usuario_id');
    }

    public function habilidades()
    {
        return $this->hasMany(Habilidad::class, 'usuario_id');
    }

    public function experiencias()
    {
        return $this->hasMany(Experience::class, 'usuario_id');
    }

    public function formacionAcademica()
    {
        return $this->hasMany(FormacionAcademica::class, 'usuario_id');
    }

    public $timestamps = true;
    /*public function profile()
    {
        return $this->hasOne(Profile::class, 'usuario_id');
    }*/
}
