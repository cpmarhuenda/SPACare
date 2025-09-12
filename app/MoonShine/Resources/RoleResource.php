<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use MoonShine\Fields\{ID, Text};
use MoonShine\Resources\ModelResource;

class RoleResource extends ModelResource
{
    public string $model = Role::class;
    protected string $title = 'Roles';

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Nombre del rol', 'name')->required(),
        ];
    }

    public function search(): array
    {
        return ['id', 'name'];
    }

    public function rules(Model $item): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($item->getKey()),
            ],
        ];
    }
}
