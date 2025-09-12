<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Aquí podrás registrar policies o gates si las necesitas
    }
    
    protected $policies = [
    \App\Models\Catrecurso::class => \App\Policies\CatrecursoPolicy::class,
];

}
