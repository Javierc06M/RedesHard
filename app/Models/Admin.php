<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins';  // nombre correcto de la tabla

    protected $fillable = ['nombre', 'apellidos', 'username', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    // Si usas username para login, Laravel lo busca en el método username() (opcional)
    public function username()
    {
        return 'username';
    }

    public function ventas()
    {
        return $this->hasMany(Ventas::class);
    }
}
