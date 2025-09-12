<?php

namespace App\Http\Controllers;

use App\Models\Paciente;

class ResumenClinicoController extends Controller
{
    public function show(Paciente $paciente)
    {
        $ultimaCita = $paciente->citas()->latest('fecha_hora')->first();
        $proximaCita = $paciente->citas()->where('fecha_hora', '>', now())->orderBy('fecha_hora')->first();
        $numRecursos = $paciente->recursos()->count();
        $estado = $paciente->estado_salud_actual ?? 'Sin estado registrado';

        return view('resumen-clinico', compact('paciente', 'ultimaCita', 'proximaCita', 'numRecursos', 'estado'));
    }
}
