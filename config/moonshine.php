<?php

use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Forms\LoginForm;
use MoonShine\Http\Middleware\Authenticate;
use MoonShine\Http\Middleware\SecurityHeadersMiddleware;
use App\MoonShine\MoonShineLayout;
use MoonShine\Pages\ProfilePage;
//use App\MoonShine\pages\ReadonlyProfilePage;

return [
    'dir' => 'app/MoonShine',
    'namespace' => 'App\MoonShine',

    'home_page' => \App\MoonShine\Pages\MainPage::class,

    'title' => 'e-SPA', //'SPACare - UNED',
    'logo' => '/images/Logo_SPACare.png',
    'logo_small' => '/images/Logo_SPACare.png',

    'route' => [
        // 'prefix' => env('MOONSHINE_ROUTE_PREFIX', 'admin'),
        //lo desactivamos para que esté accesible en el raiz del sitio web
        'single_page_prefix' => 'page',
        'middlewares' => [
            SecurityHeadersMiddleware::class,
        ],
        'notFoundHandler' => MoonShineNotFoundException::class,
    ],

    'use_migrations' => true,
    'use_notifications' => true,
    'use_theme_switcher' => true,

    'disk' => 'public',

    'forms' => [
        'login' => LoginForm::class
    ],

    'pages' => [
        'dashboard' => \App\MoonShine\Pages\MainPage::class,
        //creamos nuestro propio profile de solo lectura
        'profile' =>  ProfilePage::class
    ],



    'model_resources' => [
        'default_with_import' => false,
        'default_with_export' => false,
    ],


    'layout_view' => 'vendor.moonshine.layouts.custom',

    'resources' => [
        \Sweet1s\MoonshineRBAC\Resource\UserResource::class,
        // \App\MoonShine\Resources\MensajeResource::class,
    ],

    'auth' => [
        'enable' => true,
        'middleware' => Authenticate::class,
        'fields' => [
            'username' => 'email',
            'password' => 'password',
            'name' => 'name',
        ],
        'guard' => 'web',
        'guards' => [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
        ],
        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => \App\Models\User::class,
            ],
            'moonshine' => [
                'driver' => 'eloquent',
                'model' => \App\Models\User::class,
            ],
        ],
    ],

    'locales' => ['es', 'en'],

    'tinymce' => [
        'file_manager' => false,
        'token' => env('MOONSHINE_TINYMCE_TOKEN', ''),
        'version' => env('MOONSHINE_TINYMCE_VERSION', '6'),
    ],

    'socialite' => [],
];
