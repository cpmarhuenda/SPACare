<?php

namespace App\MoonShine\Pages;

use MoonShine\Pages\Page;
use App\Models\Cita;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\MoonShine\Components\CalendarioComponent;

class CalendarioPage extends Page
{
    public function title(): string
    {
        return __('menu.calendario');
    }

    public function components(): array
    {
        $citas = Cita::all()->map(fn($cita) => [
            'title'        => __('fields.citaConPaciente'),
            'start'        => $cita->fecha_hora,
            'end'          => Carbon::parse($cita->fecha_hora)
                ->add(CarbonInterval::createFromFormat('H:i:s', $cita->duracion ?? '01:00:00'))
                ->toDateTimeString(),
            'videollamada' => $cita->enlace_videollamada,
            'url'          => $cita->enlace_videollamada,
        ])->toArray();

        // Mapea el locale de Laravel -> FullCalendar
        $fcLocale = str_starts_with(app()->getLocale(), 'en') ? 'en' : 'es';
        return [new CalendarioComponent($citas, $fcLocale)];
    }
}
