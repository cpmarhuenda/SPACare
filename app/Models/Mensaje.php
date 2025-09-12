<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\NuevoMensajeRecibido;


class Mensaje extends Model
{
    protected $fillable = [
        'remitente_id',
        'destinatario_id',
        'contenido',
        'responde_a',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function mensajePadre()
    {
        return $this->belongsTo(Mensaje::class, 'responde_a');
    }

    //esto lo hacemos para que pille el usuario autenticado en el remitente
    protected static function booted()
    {
        static::creating(function ($mensaje) {
            if (auth()->check()) {
                $mensaje->remitente_id = auth()->id();
            }
        });
        // Enviar notificación por correo después de crear
        static::created(function ($mensaje) {
            if ($mensaje->destinatario) {
                $mensaje->destinatario->notify(new NuevoMensajeRecibido($mensaje));
            }
        });
    }

    public function getNombreRemitenteAttribute(): string
    {
        $user = $this->remitente;

        if (!$user) {
            return 'Desconocido';
        }

        // Busca si el usuario tiene un psicólogo, paciente o administrativo
        if ($user->psicologo && $user->psicologo->nombre_completo) {
            return $user->psicologo->nombre_completo;
        }

        if ($user->paciente && $user->paciente->nombre_completo) {
            return $user->paciente->nombre_completo;
        }

        if ($user->administrativo && $user->administrativo->nombre_completo) {
            return $user->administrativo->nombre_completo;
        }

        return $user->name;
    }

    public function getNombreDestinatarioAttribute(): string
    {
        $user = $this->destinatario;

        if (!$user) {
            return 'Desconocido';
        }

        if ($user->psicologo && $user->psicologo->nombre_completo) {
            return $user->psicologo->nombre_completo;
        }

        if ($user->paciente && $user->paciente->nombre_completo) {
            return $user->paciente->nombre_completo;
        }

        if ($user->administrativo && $user->administrativo->nombre_completo) {
            return $user->administrativo->nombre_completo;
        }

        return $user->name;
    }

    public function scopeNoLeidosParaUsuario($query, $userId)
    {
        return $query->where('destinatario_id', $userId)
            ->where('leido', false);
    }
}
