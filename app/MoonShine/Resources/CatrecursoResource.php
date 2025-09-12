<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Catrecurso;

use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Fields\Text;
use MoonShine\Components\MoonShineComponent;
use MoonShine\ActionButtons\ActionButton;

class CatrecursoResource extends ModelResource
{




    public string $model = Catrecurso::class;
    protected string $policy = \App\Policies\CatrecursoPolicy::class;


    protected string $title = 'Categorías de Recursos';
    public function title(): string
    {
        return __('menu.categorias');
    }
    protected bool $createInModal = false;
    protected bool $editInModal  = false;
    protected bool $detailInModal  = false;

    public function redirectAfterSave(): string
    {

        return '/resource/catrecurso-resource/index-page';
    }
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                Text::make(__('fields.nombre'), 'nombre')
                    ->required(),
                Text::make(__('fields.descripcion'), 'descripcion')
                    ->required(),
            ]),
        ];
    }

    /**
     * @param Catrecurso $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }



    public function can(string $ability): bool
    {
        if ($ability === 'create') {
            return !auth()->user()?->hasRole(['Paciente', 'Psicologo']);
        }


        if ($ability === 'delete') {
            return false;
        }

        if ($ability === 'update') {
            return !auth()->user()?->hasRole(['Paciente', 'Psicologo']);
        }


        return parent::can($ability);
    }
}
