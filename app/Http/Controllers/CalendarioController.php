<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;

class CalendarioController extends Controller
{
    public function index()
    {
        $citas = Cita::all()->map(function ($cita) {
            return [
                'title' => 'Cita: ' . $cita->paciente_id,
                'start' => $cita->fecha_hora,
                'end' => now()->parse($cita->fecha_hora)->add(\Carbon\CarbonInterval::fromString($cita->duracion ?? '01:00:00')),
                'url' => '',  
            ];
        });

        return view('moonshine.pages.calendario', [
            'citas' => $citas
        ]);
    }
}
