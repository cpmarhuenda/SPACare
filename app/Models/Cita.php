<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\support\str;
use App\Models\Mensaje;



class Cita extends Model
{
    use HasFactory;
    protected $fillable = [
        'paciente_id',
        'psicologo_id',
        'fecha_hora',
        'duracion',
        'tipo',
        'enlace_videollamada',
        'fecha_inicio',
        'fecha_fin',
        'periodicidad'
    ];

    protected $casts = [
        'hora_recurrente' => 'string',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function psicologo()
    {
        return $this->belongsTo(Psicologo::class, 'psicologo_id');
    }

    //generamos automaticamente el enlace a la videollamada al generar la cita


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($cita) {
            if (empty($cita->enlace_videollamada)) {
                $cita->enlace_videollamada = 'https://meet.jit.si/' . Str::uuid();
            }
        });
    }
    public function getEnlaceVideollamadaUrlAttribute(): ?string
{
    $url = (string) ($this->enlace_videollamada ?? '');
    if ($url === '') return null;
    return preg_match('~^https?://~i', $url) ? $url : 'https://' . ltrim($url, '/');
}


    protected static function booted()
    {
        static::creating(function ($item) {
            if ($item->tipo === 'puntual') {
                $item->fecha_inicio = null;
                $item->fecha_fin = null;
                $item->periodicidad = null;
            }
        });
        //para los mensajes
        static::created(function ($cita) {
            $paciente = $cita->paciente;
            $psicologo = $cita->psicologo;

            if ($paciente && $paciente->user && $psicologo) {
                $fecha = \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m/Y H:i');

                Mensaje::create([
                    'remitente_id' => $psicologo->user_id ?? auth()->id(),
                    'destinatario_id' => $paciente->user->id,
                    'contenido' => "Tienes una nueva cita programada para el día {$fecha}.",
                    'responde_a' => null,
                    'leido' => false,
                ]);
            }
        });
    }
}
