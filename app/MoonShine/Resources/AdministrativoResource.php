<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Administrativo;
use MoonShine\Fields\{ID, Text, Image};
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Switcher;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Number;
use MoonShine\Fields\Date;
use MoonShine\Fields\Badge;
use MoonShine\Fields\Html;

use MoonShine\Fields\Select;
use MoonShine\Filters\SelectFilter;

class AdministrativoResource extends ModelResource
{
    public function title(): string
    {
        return __('menu.administrativos');
    }

    public string $model = Administrativo::class;

    public function fields(): array
    {
        \Log::info('entra a fields AdministrativoResource');
        return [

            Text::make('Nombre', 'name')->required(),
            Text::make('Teléfono', 'telefono')->nullable(),
            Switcher::make('Activo', 'active')->default(true),
            Image::make('Fotografía', 'fotografia')
                ->dir('administrativos')
                ->nullable(),
        ];
    }

    public function rules($item = null): array
    { 

        $isEditing = $item && $item->exists;

        return [
            'name' => 'required|string|max:255',
            'user_email' => $isEditing
                ? 'nullable|email|unique:users,email,' . ($item->user_id ?? 'null')
                : 'required|email|unique:users,email',
            'password_temp' => $isEditing ? 'nullable|min:8|same:password_repeat_temp' : 'required|min:8|same:password_repeat_temp',
            'password_repeat_temp' => $isEditing ? 'nullable|same:password_temp' : 'required_with:password_temp|same:password_temp',

        ];
    }

    public function redirectAfterSave(): string
    {
        return '/resource/administrativo-resource/index-page';
    }



    public function buttons(): array
    {
 
        $path = request()->path();

        if (
            str_contains($path, 'index-page') ||
            str_contains($path, 'create-page')
        ) {
            return [];
        }

        $resourceUri = $this->uriKey();

        return [
            ActionButton::make(
                label: 'Cancelar',
                url: ("/resource/{$this->uriKey()}/index-page")
            )
                ->icon('heroicons.outline.arrow-left')
                ->customAttributes(['class' => 'btn btn-secondary'])
        ];
    }

    public function formFields(): array
    {
 

        $item = $this->getItem();
        $isEditing = $item?->exists ?? false;

        $user = auth()->user();
        $rolesAutorizados = [1, 3]; // Super Admin y Administrativo


        $fields = [
            Block::make('Datos personales', [
                Text::make('Nombre', 'name'),
                Number::make('Teléfono', 'telefono'),

                Text::make('Email', 'user_email')
                    ->customAttributes(['type' => 'email'])
                    ->required()
                    ->hideOnIndex()
                    ->hideOnDetail()

                    ->hideOnUpdate()
                    ->showOnCreate(),

                Date::make('Fecha de alta', 'falta')->default(now()->toDateString())->hideOnIndex(),

                Image::make('Fotografía', 'fotografia')
                    ->disk('public')              // usa storage/app/public
                    ->dir('administrativos')      // subcarpeta
                    ->accept('image/*')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp'])
                    ->nullable(),

                Date::make('Fecha de alta', 'falta')
                    ->default(now()->toDateString())
                    ->hideOnIndex(),
            ]),


        ];


        if (! $isEditing) {
            $fields[] = Block::make('Contraseña', [
                Text::make('Contraseña temporal', 'password_temp')
                    ->customAttributes(['type' => 'password'])
                    ->hideOnIndex()
                    ->hideOnDetail(),

                Text::make('Repetir contraseña', 'password_repeat_temp')
                    ->customAttributes(['type' => 'password'])
                    ->hideOnIndex()
                    ->hideOnDetail(),
            ]);
        }

        return $fields;
    }

    public function filters(): array
    {
        return [
            Select::make('Activo', 'active')
                ->options([
                    '' => 'Todos',
                    1 => 'Activos',
                    0 => 'Inactivos',
                ]),

        ];
    }
    public function actions(): array
    {
        $estado = request()->input('filters.active');
        $activeStyle = 'background-color: #008542; color: white; border-radius: 0.5rem;';
        return [
            ActionButton::make(
                label: 'Todos',
                url: '/resource/administrativo-resource/index-page?filters[active]=',
            )->icon('heroicons.outline.users')
                ->customAttributes([
                    'style' => ($estado === null || $estado === '') ? $activeStyle : ''
                ]),

            ActionButton::make(
                label: 'Activos',
                url: '/resource/administrativo-resource/index-page?filters[active]=1',
            )->icon('heroicons.outline.check-circle')
                ->customAttributes([
                    'style' => $estado === '1' ? $activeStyle : ''
                ]),

            ActionButton::make(
                label: 'Inactivos',
                url: '/resource/administrativo-resource/index-page?filters[active]=0',
            )->icon('heroicons.outline.x-circle')
                ->customAttributes([
                    'style' => $estado === '0' ? $activeStyle : '',

                ])

        ];
    }


    public function can(string $ability): bool
    {


        if ($ability === 'delete') {
            return false; // Ocultar eliminar para todos
        }


        return parent::can($ability);
    }
}
