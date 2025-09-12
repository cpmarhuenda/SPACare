<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Psicologo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'apellidos',
        'user_id',
        'active',
        'fotografia',
        'numero_colegiado',
        'especialidad',
        'formacion',
        'falta',
    ];

    public function getFotografiaUrlAttribute(): ?string
    {
        return $this->fotografia ? Storage::url($this->fotografia) : null;
    }

    // Evento que se ejecuta antes de guardar el paciente


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($psicologo) {


            if (!$psicologo->user_id) {

                $password = request('password_temp') ?? '12345678';
                $mail = request('user_email');

                $user = User::create([
                    'name' => $psicologo->name,
                    'email' => $mail,
                    'password' => bcrypt($password),
                    'tipo_usuario' => 4, // Psicologo

                ]);
                $user->assignRole('Psicologo');
                $psicologo->user_id = $user->id;
                unset(
                    $psicologo->user_email,
                    $psicologo->password_temp,
                    $psicologo->password_repeat_temp
                );
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function citas()
    {
        return $this->hasMany(Cita::class, 'psicologo_id');
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }

    public function getTotalPacientesAttribute(): int
    {
        return $this->pacientes()->count();
    }

    public function getTotalCitasAttribute(): int
    {
        return $this->citas()->count();
    }



    public function getAntiguedadAttribute(): string
    {
        if (!$this->falta) {
            return 'Sin fecha';
        }

        return Carbon::parse($this->falta)->diffForHumans(now(), [
            'parts' => 1,
            'short' => true,
            'syntax' => Carbon::DIFF_ABSOLUTE
        ]);
    }
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->name}";
    }
}
