<?php

declare(strict_types=1);

namespace App\Providers;


use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\RoleResource;
use App\MoonShine\Resources\PermissionResource;
use App\MoonShine\Resources\PacienteResource;
use App\MoonShine\Resources\PsicologoResource;
use App\MoonShine\Resources\MensajeResource;
use App\MoonShine\Resources\MensajeEnviadoResource;
use App\MoonShine\Resources\MensajeNuevoResource;
use App\MoonShine\Resources\MensajeRecibidoResource;
use App\MoonShine\Resources\PacienteRecursoResource;
use App\MoonShine\Resources\AdministrativoResource;
use App\MoonShine\Resources\HistoriaClinicaResource;
use App\MoonShine\Resources\CitaResource;
use App\MoonShine\Pages\CalendarioPage;
use MoonShine\Pages\Page;
use App\Models\Mensaje;
use MoonShine\Providers\MoonShineApplicationServiceProvider;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Menu\MenuDivider;
use App\MoonShine\Resources\NewRecursoResource;
use App\MoonShine\Resources\CatrecursoResource;
use App\MoonShine\Resources\RecursoResource;
use App\MoonShine\Resources\NewCatrecursoResource;
use MoonShine\Components\MoonShineComponent;
use Illuminate\Support\Facades\Auth;
use MoonShine\Assets\Css;
use MoonShine\Assets\Js;
use App\MoonShine\Pages\MiPerfilPage;

//use MoonShine\MoonShine;
use MoonShine\Facades\MoonShineUi;
use App\MoonShine\Layouts\CustomLayout;
use MoonShine\MoonShine;
use Illuminate\Support\Facades\App;
use App\MoonShine\Pages\ReadonlyProfilePage;



class MoonShineServiceProvider extends MoonShineApplicationServiceProvider
{
    protected function resources(): array
    {
        return [
            new UserResource(),
            new RoleResource(),
            new PermissionResource(),
        ];
    }

    protected function pages(): array
    {
        return [

            // new MiPerfilPage(),
        ];
    }

    protected function menu(): array
    {
        $user = moonshineRequest()->user();

        if ($user) {
            $name = $user->name;
            $role = optional($user->roles->first())->name ?? 'Sin rol';
        } else {
            $name = 'Invitado';
            $role = 'Sin rol';
        }




        return [



            MenuItem::make(fn() => (
                ($user = moonshineRequest()->user())
                ? "{$user->nombre_completo} · " . optional($user->roles->first())->name
                : "Invitado · Sin rol"
            ), '#')
                ->icon('heroicons.outline.user-circle')
                ->canSee(fn() => true),
            /*
MenuItem::make(fn () => (
    ($user = moonshineRequest()->user())
        ? "{$user->nombre_completo} · " . optional($user->roles->first())->name
        : "Invitado · Sin rol"
), new MiPerfilPage())   // ⬅️ en vez de '#', pásale la página
->icon('heroicons.outline.user-circle')
->canSee(fn () => true),*/

            MenuDivider::make(),

            MenuGroup::make(fn() => __('menu.gestionUsuarios'), [
                MenuItem::make(fn() => __('menu.usuarios'), new \App\MoonShine\Resources\UserResource())->icon('heroicons.outline.users')
                    ->canSee(fn() => auth()->user()?->hasRole(['Super Admin', 'Administrativo'])),
                MenuItem::make(fn() => __('menu.roles'), new \App\MoonShine\Resources\RoleResource())->icon('heroicons.outline.shield-check')
                    ->canSee(fn() => auth()->user()?->hasRole('Super Admin')),
                MenuItem::make(fn() => __('menu.permisos'), new \App\MoonShine\Resources\PermissionResource())->icon('heroicons.outline.key')
                    ->canSee(fn() => auth()->user()?->hasRole('Super Admin')),

                MenuItem::make(fn() => __('menu.administrativos'),  new AdministrativoResource)
                    ->icon('heroicons.outline.briefcase')
                    ->canSee(fn() => auth()->user()?->hasRole(['Super Admin', 'Administrativo'])),

            ])
                ->canSee(fn() => auth()->user()?->hasRole(['Super Admin', 'Administrativo'])),



            MenuGroup::make(fn() => __('menu.recursosCompartidos'), [
                MenuItem::make(fn() => __('menu.categorias'), new CatrecursoResource)
                    ->canSee(fn() => auth()->user()?->hasRole(['Super Admin', 'Administrativo', 'Psicologo']))
                    ->icon('heroicons.outline.tag'),
                MenuItem::make(fn() => __('menu.recurso'), new RecursoResource())
                    ->canSee(fn() => auth()->user()?->hasAnyRole(['Super Admin', 'Psicologo', 'Administrativo']))
                    ->icon('heroicons.outline.folder'),

                MenuItem::make(fn() => __('menu.recursosPaciente'), new PacienteRecursoResource())
                    ->icon('heroicons.outline.document'),
            ]),
            MenuDivider::make(),

            MenuGroup::make(fn() => __('menu.atencionClinica'), [

                MenuItem::make(fn() => __('menu.pacientes'), new PacienteResource)
                    ->canSee(fn() => auth()->user()?->hasAnyRole(['Super Admin', 'Psicologo', 'Administrativo']))
                    ->icon('heroicons.outline.user'),

                MenuItem::make(fn() => __('menu.psicologos'), new PsicologoResource)
                    ->canSee(fn() => auth()->user()?->hasAnyRole(['Super Admin', 'Administrativo']))
                    ->icon('heroicons.outline.academic-cap'),

                MenuItem::make(fn() => __('menu.historiasClinicas'), new HistoriaClinicaResource())

                    ->canSee(fn() => auth()->user()?->hasAnyRole(['Super Admin', 'Psicologo', 'Administrativo']))
                    ->icon('heroicons.outline.clipboard-document'),



                MenuItem::make(fn() => __('menu.citas'), new CitaResource())
                    ->icon('heroicons.outline.clock'),


            ]),



            MenuGroup::make(fn() => __('menu.mensajeria'), [
                MenuItem::make(fn() => __('menu.bandejaEntrada'), new MensajeRecibidoResource())
                    ->icon('heroicons.outline.inbox')
                    ->badge(function () {
                        return (string) Mensaje::where('destinatario_id', auth()->id())
                            ->where('leido', false)
                            ->count();
                    }),

                MenuItem::make(fn() => __('menu.enviados'), new MensajeEnviadoResource())
                    ->icon('heroicons.outline.paper-airplane'),
            ]),

            MenuGroup::make(fn() => __('menu.herramientas'), [
                MenuItem::make(fn() => __('menu.calendario'), new CalendarioPage())
                    ->icon('heroicons.outline.calendar'),
            ]),

        ];
    }


    protected function theme(): array
    {
        return [
            'colors' => [
                'primary' => '#004a29', // Verde UNED
                'secondary' => '#00361e', // Hover / fondo de grupo


                //  'body' => '#206c52' ,
                'body' => '#F5F7FA',

                'dark' => [
                    'DEFAULT' => '#00361e', // Fondo del sidebar incluso cuando no está activo
                    50 => '#d6f3e8',
                    50 => '#dc3545',
                    100 => '#b0e7d3',
                    200 => '#8bdcbe',
                    300 => '#66d0a9',
                    400 => '#40c594',
                    500 => '#33a67e',
                    600 => '#298968',
                    700 => '#206c52',
                    800 => '#174f3c',
                    900 => '#0e3226',
                ],


                'success-bg' => '#198754',
                'success-text' => '#ffffff',
                'success-text' => '#dc3545',
                'warning-bg' => '#ffc107',
                'warning-text' => '#000000',
                'error-bg' => '#dc3545',
                'error-text' => '#ffffff',
                'info-bg' => '#0d6efd',
                'info-text' => '#ffffff',
                'info-text' => '#dc3545',
            ],
            'darkColors' => [
                'body' => '#00361e', // Sidebar en modo oscuro
                'success-bg' => '#198754',
                'success-text' => '#d1f7e2',
                'success-text' => '#dc3545',
                'warning-bg' => '#ffc107',
                'warning-text' => '#000000',
                'error-bg' => '#dc3545',
                'error-text' => '#ffffff',
                'info-bg' => '#0d6efd',
                'info-text' => '#ffffff',
                'info-text' => '#dc3545',
            ]
        ];
    }


    public function boot(): void
    {
 

        $msLocales = config('moonshine.locales', []);
        $msLocale  = request()->cookie('moonshine_locale');
        if ($msLocale && in_array($msLocale, $msLocales, true)) {
            \Illuminate\Support\Facades\App::setLocale($msLocale);
        }

        parent::boot();
        \Log::info(
            'PROFILE RESOURCE => ' . get_class(app(\MoonShine\Resources\MoonShineProfileResource::class))
        );

        moonShineAssets()->add([
            '/css/custom.css?v=40',
            '/js/custom.js?v=40',
        ]);
    }

    //para cambiar la pagina de usuario
    public function register(): void
    {
        parent::register();

        // Fuerza que SIEMPRE se use tu recurso
        $this->app->singleton(
            \MoonShine\Resources\MoonShineProfileResource::class,
            fn() => new \App\MoonShine\Resources\MoonShineProfileResource()
        );

        // Alias por si MoonShine resuelve por la clase app
        $this->app->alias(
            \App\MoonShine\Resources\MoonShineProfileResource::class,
            \MoonShine\Resources\MoonShineProfileResource::class
        );
    }
}
