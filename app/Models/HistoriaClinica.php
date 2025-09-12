<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\support\str;




class HistoriaClinica extends Model
{
    use HasFactory;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'paciente_id',
        'psicologo_id',
        'fecha',
        'diagnostico',
        'tratamiento',
        'notas_psicologo',
        'antecedentes_medicos',
        'medicacion_actual',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function psicologo()
    {
        return $this->belongsTo(Psicologo::class, 'psicologo_id');
    }
}
