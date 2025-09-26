<?php

namespace App\MoonShine\Pages;

use MoonShine\Pages\Page;
use App\Models\Cita;
use App\Models\Psicologo;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;
use App\MoonShine\Components\CalendarioComponent;

class CalendarioPage extends Page
{
    public function title(): string
    {
        return __('menu.calendario');
    }

    public function components(): array
    {
        $user = Auth::user();

        // ---- 1) Query con la misma lógica de visibilidad que CitaResource ----
        $query = Cita::query()->with(['paciente', 'psicologo']);

        // Admin (id=1) ve todo
        if (! $user->roles->contains('id', 3)) {
            if ($user->hasRole('Psicologo')) {
                $psId = Psicologo::where('user_id', $user->id)->value('id');
                $query->where('psicologo_id', $psId ?? -1);
            } elseif ($user->hasRole('Paciente')) {
                $paciente = $user->paciente;
                $query->where('paciente_id', $paciente?->id ?? -1);
            } else {
                // otros roles sin permiso → nada
                $query->where('id', -1);
            }
        }

        $citas = $query->get();

        // ---- 2) Título por rol + url adecuada ----
        $soyPsico   = $user->hasRole('Psicologo');
        $soyPaciente= $user->hasRole('Paciente');
        $soyAdmin   = $user->roles->contains('id', 1);

        $events = $citas->map(function ($cita) use ($soyPsico, $soyPaciente, $soyAdmin) {
            // Título
            if ($soyPsico) {
                $title = __('fields.citaConPaciente') . ' ' . ($cita->paciente->nombre_completo ?? '');
            } elseif ($soyPaciente) {
                $title = __('fields.citaConPsicologo') . ' ' . ($cita->psicologo->nombre_completo ?? '');
            } else { // admin u otros con acceso
                $title = sprintf(
                    '%s — %s',
                    $cita->paciente->nombre_completo ?? __('fields.paciente'),
                    $cita->psicologo->nombre_completo ?? __('fields.psicologo')
                );
            }

            // Duración robusta: soporta "HH:MM" o "HH:MM:SS"
            $dur = $cita->duracion ?? '01:00';
            [$h,$m,$s] = array_map('intval', array_pad(explode(':', $dur), 3, 0));
            $end = Carbon::parse($cita->fecha_hora)->copy()->add(CarbonInterval::hours($h)->minutes($m)->seconds($s));

            // URL: si tienes detalle de recurso MoonShine, usa esa ruta; si prefieres videollamada, deja el enlace
           // $detailUrl = "/resource/cita-resource/detail-page/{$cita->id}";
            $detailUrl = "/resource/cita-resource/detail-page?resourceItem={$cita->id}";
            $url = $detailUrl; // o $cita->enlace_videollamada si quieres ir directo a la videollamada

            return [
                'title' => trim($title),
                'start' => Carbon::parse($cita->fecha_hora)->toIso8601String(),
                'end'   => $end->toIso8601String(),

                // Lo que FullCalendar abre al hacer click (si usas eventClick nativo)
                'url'   => $url,

                // Cosas útiles en extendedProps por si las usas en tooltips/modales
                'extendedProps' => [
                    'paciente'      => $cita->paciente->nombre_completo ?? null,
                    'psicologo'     => $cita->psicologo->nombre_completo ?? null,
                    'videollamada'  => $cita->enlace_videollamada,
                    'cita_id'       => $cita->id,
                ],
            ];
        })->toArray();

        // Locale Laravel → FullCalendar
        $fcLocale = str_starts_with(app()->getLocale(), 'en') ? 'en' : 'es';

        return [ new CalendarioComponent($events, $fcLocale) ];
    }
}
