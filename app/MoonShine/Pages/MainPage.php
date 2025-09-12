<?php

namespace App\MoonShine\Pages;

use MoonShine\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Mensaje;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Textarea;
use MoonShine\Components\Html;


class MainPage extends Page
{
    public function title(): string
    {
        return 'Inicio';
    }


    public function components(): array
    {
        $user = Auth::user();
        $hoy = now()->toDateString();



        if ($user->hasRole('Paciente')) {
            $citasHoy = Cita::where('paciente_id', $user->paciente->id ?? 0)
                ->whereDate('fecha_hora', $hoy)
                ->count();
        } elseif ($user->hasRole('Psicologo')) {
            $citasHoy = Cita::where('psicologo_id', $user->psicologo->id ?? 0)
                ->whereDate('fecha_hora', $hoy)
                ->count();
        } else {
            // Administrativo o SuperAdmin: ver todas
            $citasHoy = Cita::whereDate('fecha_hora', $hoy)->count();
        }
        //   };

        // Próxima cita
        $proximaCita = match (true) {
            method_exists($user, 'paciente') && $user->paciente => Cita::where('paciente_id', $user->paciente->id)->where('fecha_hora', '>', now())->orderBy('fecha_hora')->first(),
            method_exists($user, 'psicologo') && $user->psicologo => Cita::where('psicologo_id', $user->psicologo->id)->where('fecha_hora', '>', now())->orderBy('fecha_hora')->first(),
            default => null,
        };




        $textoProximaCita = $proximaCita
            ? \Carbon\Carbon::parse($proximaCita->fecha_hora)->format('d/m/Y H:i')
            : 'No programada';

        // Mensajes no leídos
        $mensajesNoLeidos = Mensaje::where('destinatario_id', $user->id)
            ->where('leido', false)
            ->count();

        // Texto formateado
        $contenido = "📅 Citas para hoy: {$citasHoy}\n🕒 Próxima cita: {$textoProximaCita}\n📨 Mensajes sin leer: {$mensajesNoLeidos}";

        return [


            //bloque resumen
            Block::make('Resumen')->fields([
                Textarea::make('Resumen', 'resumen')
                    ->default($contenido)
                    ->disabled()
                    ->hideOnForm(),
            ]),

        ];
    }
}
