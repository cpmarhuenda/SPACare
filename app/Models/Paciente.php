<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{


    use HasFactory;

    //DOCUMENTACION DOCUMENTAR IMPORTANTE PARA PONER EN LA DOCU. TUVIMOS QUE MODIFICAR ÉSTA PARTE PORQUE SINO CREA EL PACIENTE ANTES QUE EL USUARIO, ENTONCES NO LO PODIA RELACIONAR.

    protected $fillable = [
        'name',
        'apellidos',
        'telefono',
        'direccion',
        'password',
        'user_id',
        'fotografia',
        'observaciones',
        'password_temp',
        'password_repeat_temp'
    ];

    // Evento que se ejecuta antes de guardar el paciente
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($paciente) {
            if (!$paciente->user_id) {
                // Crear usuario antes de guardar el paciente
                $password = request('password_temp') ?? '12345678';
                $mail = request('user_email');
                $user = User::create([
                    'name' => $paciente->name,
                    'email' => $mail,
                    'password' => bcrypt($password),
                    'tipo_usuario' => 2, // Paciente 
                ]);
                //asignamos el rol paciente
                $user->assignRole('Paciente');
                $paciente->user_id = $user->id;
                unset(
                    $paciente->user_email,
                    $paciente->password_temp,
                    $paciente->password_repeat_temp
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
        return $this->hasMany(Cita::class, 'paciente_id'); // Especifica la clave foránea
    }
    public function recursos()
    {
        return $this->belongsToMany(Recurso::class, 'paciente_recurso');
    }
    public function psicologo()
    {
        return $this->belongsTo(Psicologo::class);
    }

    public function getNombreCompletoAttribute(): string
    {

        return "{$this->name} ";
    }
    public function getFotografiaUrlAttribute(): ?string
    {
        return $this->fotografia ? Storage::url($this->fotografia) : null;
    }
}
