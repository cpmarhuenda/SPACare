<?php

declare(strict_types=1);

namespace App\MoonShine;

use MoonShine\Components\Layout\{Content, Flash, Footer, Header, LayoutBlock, LayoutBuilder, Menu, Sidebar};
use MoonShine\Contracts\MoonShineLayoutContract;

final class MoonShineLayout implements MoonShineLayoutContract
{
    public static function build(): LayoutBuilder
    {
        return LayoutBuilder::make([
            Sidebar::make([
                Menu::make()->customAttributes(['class' => 'mt-2']),
            ]),
            LayoutBlock::make([
                Flash::make(),
                Header::make(),
                Content::make(),
                /*Footer::make()->copyright(fn (): string => <<<'HTML'
                        &copy; 2021-2023 Made with ❤️ by
                        <a href="https://cutcode.dev"
                            class="font-semibold text-primary hover:text-secondary"
                            target="_blank"
                        >
                            CutCode
                        </a>
                    HTML)->menu([
                    'https://moonshine.cutcode.dev' => 'Documentation',
                ]),*/
            ])->customAttributes(['class' => 'layout-page']),
        ]);
    }
}
