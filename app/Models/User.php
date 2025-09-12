<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Auth\Passwords\CanResetPassword;

use App\Models\Role;

use Sweet1s\MoonshineRBAC\Traits\MoonshineRBACHasRoles;

class User extends Authenticatable
{
    // use HasFactory, Notifiable;
    use HasFactory, Notifiable, HasRoles, CanResetPassword;


    // use MoonshineRBACHasRoles;

    const SUPER_ADMIN_ROLE_ID = 1;

    protected $appends = ['nombre_completo'];
    // Relación con Paciente
    public function paciente()
    {
        return $this->hasOne(Paciente::class); // Un usuario tiene un paciente relacionado
    }

    protected $fillable = [

        'email',
        'password',
        'active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getRolesListAttribute(): string
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    public function isPaciente(): bool
    {
        return $this->hasRole('Paciente');
    }

    public function isPsicologo(): bool
    {
        return $this->hasRole('Psicólogo');
    }

    public function isAdministrativo(): bool
    {
        return $this->hasRole('Administrativo');
    }

    public function administrativo()
    {
        return $this->hasOne(\App\Models\Administrativo::class);
    }

    public function psicologo()
    {
        return $this->hasOne(\App\Models\Psicologo::class);
    }

    public function getNombreCompletoAttribute(): string
    {

        try {
            if ($this->paciente?->nombre_completo) {
                return $this->paciente->nombre_completo;
            }

            if ($this->psicologo?->nombre_completo) {
                return $this->psicologo->nombre_completo;
            }

            if ($this->administrativo?->nombre_completo) {
                return $this->administrativo->nombre_completo;
            }
        } catch (\Throwable $e) { 
        }

        return 'Admin';
    }



    protected static function booted()
    {
        static::updated(function (User $user) {
            // Solo actuar si ha cambiado el campo "active"
            if ($user->isDirty('active')) {
                $activo = $user->active;

                // Sincronizar con administrativo
                if ($user->relationLoaded('administrativo')) {
                    $user->administrativo?->update(['active' => $activo]);
                } else {
                    $user->administrativo()->update(['active' => $activo]);
                }

                // Sincronizar con psicologo
                if ($user->relationLoaded('psicologo')) {
                    $user->psicologo?->update(['active' => $activo]);
                } else {
                    $user->psicologo()->update(['active' => $activo]);
                }

                // Sincronizar con paciente
                if ($user->relationLoaded('paciente')) {
                    $user->paciente?->update(['active' => $activo]);
                } else {
                    $user->paciente()->update(['active' => $activo]);
                }
            }
        });
    }
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
}
