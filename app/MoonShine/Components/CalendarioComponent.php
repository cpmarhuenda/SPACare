<?php

namespace App\MoonShine\Components;

use Illuminate\Contracts\View\View;
use MoonShine\Components\MoonShineComponent;

class CalendarioComponent  extends MoonShineComponent
{
    public function __construct(
        protected array $events,
        protected string $locale = 'es',
    ) {}

    public function render(): View
    {
        return view('moonshine.components.calendario', [
            'events' => $this->events,   // pasamos el array tal cual
            'locale' => $this->locale,   // 'es' o 'en'
            'labels' => [
                'today' => __('calendar.today'),
                'month' => __('calendar.month'),
                'week'  => __('calendar.week'),
                'day'   => __('calendar.day'),
                'list'  => __('calendar.list'),
            ],
        ]);
    }
}
