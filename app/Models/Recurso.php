<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


//para documentar

/*
Configurar el almacenamiento de archivos en Laravel:

Laravel usa el sistema de almacenamiento storage con public y s3 como opciones por defecto.
Nos basamos en ejecutar php artisan storage:link para que los archivos sean accesibles desde la web si usamos public.

*/

class Recurso extends Model
{
    use HasFactory;
    protected $fillable =  ['titulo', 'fecha', 'archivo', 'categoria_id', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];


    public function categoria()
    {
        return $this->belongsTo(CatRecurso::class, 'categoria_id');
    }

    public function getArchivoUrlAttribute()
    {
        return Storage::url($this->archivo);
    }

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'paciente_recurso', 'recurso_id', 'paciente_id');
    }
}
