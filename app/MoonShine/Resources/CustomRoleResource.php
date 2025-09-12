<?php

namespace App\MoonShine\Resources;

use Spatie\Permission\Models\Role;
use Sweet1s\MoonshineRBAC\Resource\RoleResource as BaseRoleResource;

class CustomRoleResource extends BaseRoleResource
{
    protected string $model = Role::class;
}
