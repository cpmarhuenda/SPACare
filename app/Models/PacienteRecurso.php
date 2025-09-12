<?php

namespace App\Models;

use App\Models\Mensaje;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacienteRecurso extends Model
{
    protected $table = 'paciente_recurso';

    protected $fillable = [
        'paciente_id',
        'recurso_id',
        'descargado',
        'fecha_descarga',
        'active'
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function recurso(): BelongsTo
    {
        return $this->belongsTo(Recurso::class);
    }

    //para que se le cree un mensaje al asignar
    protected static function booted(): void
    {
        static::created(function ($pacienteRecurso) {
            $paciente = $pacienteRecurso->paciente;
            $psicologo = $paciente->psicologo;
            $recurso = $pacienteRecurso->recurso;

            // Usuario paciente
            $userPaciente = $paciente->user;

            if ($userPaciente && $recurso && $psicologo) {
                Mensaje::create([
                    'remitente_id' => $psicologo->user_id ?? auth()->id(),
                    'destinatario_id' => $userPaciente->id,
                    'contenido' => "Se te ha asignado un nuevo recurso: \"{$recurso->titulo}\".",
                    'responde_a' => null,
                    'leido' => false,
                ]);
            }
        });
    }
}
