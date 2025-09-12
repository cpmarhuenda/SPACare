<?php

namespace App\MoonShine\Resources;

use Spatie\Permission\Models\Permission;
use Sweet1s\MoonshineRBAC\Resource\PermissionResource as BasePermissionResource;

class CustomPermissionResource extends BasePermissionResource
{
    protected string $model = Permission::class;
}
