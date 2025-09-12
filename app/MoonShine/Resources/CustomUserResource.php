<?php

// app/MoonShine/Resources/CustomUserResource.php
namespace App\MoonShine\Resources;

use Sweet1s\MoonshineRBAC\Resource\UserResource as BaseUserResource;
use App\Models\User;

class CustomUserResource extends BaseUserResource
{
    protected string $model = User::class;
}
