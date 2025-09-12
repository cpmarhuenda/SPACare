<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use MoonShine\Fields\{ID, Text};
use MoonShine\Resources\ModelResource;

class PermissionResource extends ModelResource
{
    public string $model = Permission::class;
    protected string $title = 'Permisos';

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Nombre del permiso', 'name')->required(),
        ];
    }

    public function search(): array { return ['id','name']; }

    public function rules(Model $item): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('permissions','name')->ignore($item->getKey()),
            ],
        ];
    }
}
