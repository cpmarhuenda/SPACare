<?php

namespace App\MoonShine\Components;

use MoonShine\Components\MoonShineComponent;

class RawHtml extends MoonShineComponent
{
    protected string $view = 'moonshine.components.raw-html';

    public function __construct(protected string $html) {}

    protected function viewData(): array
    {
        return [
            'html' => $this->html,
        ];
    }
}
