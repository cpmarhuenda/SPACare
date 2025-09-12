<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Recurso;


class Catrecurso extends Model
{
    use HasFactory;
    protected $fillable  =  ['id', 'nombre', 'descripcion'];


    public function recursos(): BelongsToMany
    {
        return $this->belongstoMany(Recurso::class);
    }
}
