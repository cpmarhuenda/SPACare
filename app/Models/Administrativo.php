<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Administrativo extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'apellidos',
        'telefono',
        'fotografia'
    ];



    protected static function booted(): void
    {
        static::creating(function ($administrativo) {

            if (!$administrativo->user_id) {
                $password = request('password_temp') ?? '12345678';
                $mail = request('user_email');

                $user = User::create([
                    'name' => $administrativo->name,
                    'email' => $mail,
                    'password' => bcrypt($password),
                    'tipo_usuario' => 3, // Administrativo

                ]);

                $user->assignRole('Administrativo');

                $administrativo->user_id = $user->id;
                unset(
                    $administrativo->user_email,
                    $administrativo->password_temp,
                    $administrativo->password_repeat_temp
                );
            }
        });
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->name}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
