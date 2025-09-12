<?php

namespace App\MoonShine\Resources;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;

use MoonShine\Resources\ModelResource;
use MoonShine\Fields\Text;
use MoonShine\Fields\Select;
use MoonShine\Fields\Textarea;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Badge;
//use App\MoonShine\Fields\HtmlField;


class MensajeEnviadoResource extends ModelResource
{
    public string $model = Mensaje::class;
   // protected string $title = 'Mensajes Enviados';
    public function title(): string
{
    return __('menu.enviados');  
}
    protected bool $createInModal = false;
    protected bool $editInModal = false;
    protected bool $detailInModal = false;

    protected bool $canCreate = true;
    protected bool $canEdit = false;
    protected bool $canDelete = false;

    public function query(): Builder
    {
        return parent::query()->where('remitente_id', Auth::id());
    }

    public function fields(): array
    {
        return [
            Text::make(__('fields.destinatario'), 'nombre_destinatario')->hideOnForm(),
            Text::make(__('fields.mensaje'), 'contenido')->hideOnForm(), 
            
            Switcher::make(__('menu.leido'), 'leido')->default(false),
 
        ];
    }

    public function formFields(): array
    {
        $user = auth()->user();
        $options = [];
        $groupedOptions = [];

        if ($user->hasRole('Paciente')) {
            //pacientes puede enviar a psicologos

                    $psicologos = \App\Models\Psicologo::with('user')->get()->mapWithKeys(function ($psicologo) {
            return [$psicologo->user_id => $psicologo->nombre_completo];
        })->toArray();

        $groupedOptions['-Psicólogos-'] = $psicologos;
           





        } elseif ($user->hasRole('Psicologo')) {
            //psicologo puede enviar a pacientes/psicologos/administradores/Administrativo
  $administrativos = \App\Models\Administrativo::with('user')->get()->mapWithKeys(function ($administrativo) {
            return [$administrativo->user_id => $administrativo->nombre_completo];
               })->toArray();

        $pacientes = \App\Models\Paciente::with('user')->get()->mapWithKeys(function ($paciente) {
            return [$paciente->user_id => $paciente->nombre_completo];
        })->toArray();

        $psicologos = \App\Models\Psicologo::with('user')->get()->mapWithKeys(function ($psicologo) {
            return [$psicologo->user_id => $psicologo->nombre_completo];
        })->toArray();

        $groupedOptions['-Administrativos-'] = $administrativos;
        $groupedOptions['-Pacientes-'] = $pacientes;
        $groupedOptions['-Psicólogos-'] = $psicologos;
    
    }
 
        elseif ($user->hasRole('Super Admin')) {
                    //puede enviar a todo
        $pacientes = \App\Models\Paciente::with('user')->get()->mapWithKeys(function ($paciente) {
            return [$paciente->user_id => $paciente->nombre_completo];
        })->toArray();

        $psicologos = \App\Models\Psicologo::with('user')->get()->mapWithKeys(function ($psicologo) {
            return [$psicologo->user_id => $psicologo->nombre_completo];
        })->toArray();

        $administrativos = \App\Models\Administrativo::with('user')->get()->mapWithKeys(function ($administrativo) {
            return [$administrativo->user_id => $administrativo->nombre_completo];
        })->toArray();


        $groupedOptions[__('menu.-pacientes-')]  = $pacientes;
        $groupedOptions[__('menu.-psicologos-')] = $psicologos;
        $groupedOptions[__('menu.-administrativos-')]= $administrativos;
 
        }
        //administrativos
               elseif ($user->hasRole('Administrativo')) {
                    //puede enviar a todo
        $pacientes = \App\Models\Paciente::with('user')->get()->mapWithKeys(function ($paciente) {
            return [$paciente->user_id => $paciente->nombre_completo];
        })->toArray();

        $psicologos = \App\Models\Psicologo::with('user')->get()->mapWithKeys(function ($psicologo) {
            return [$psicologo->user_id => $psicologo->nombre_completo];
        })->toArray();

        $administrativos = \App\Models\Administrativo::with('user')->get()->mapWithKeys(function ($administrativo) {
            return [$administrativo->user_id => $administrativo->nombre_completo];
        })->toArray();


          $groupedOptions[__('menu.-pacientes-')]  = $pacientes;
        $groupedOptions[__('menu.-psicologos-')] = $psicologos;
        $groupedOptions[__('menu.-administrativos-')]= $administrativos;
        }


        return [
            Select::make(__('fields.destinatario'), 'destinatario_id')
                ->options($groupedOptions)
                ->required()
                  ->nullable() 
                ->searchable(),

            Textarea::make(__('fields.mensaje'), 'contenido')
                ->required(),
        ];
    }

    public function rules($item): array
    {
        return [
            'destinatario_id' => 'required|exists:users,id',
            'contenido' => 'required|string',
        ];
    }

    public function beforeCreating(\Illuminate\Database\Eloquent\Model $item): \Illuminate\Database\Eloquent\Model
    {
        $item->remitente_id = Auth::id();
        return $item;
    }

    public function redirectAfterCreate(\Illuminate\Database\Eloquent\Model $item): string
    {
        return route('moonshine.resource.index-page', ['resource' => 'mensaje-enviado-resource']);
    }

    
    public function getEditButton(?string $componentName = null, bool $isAsync = false): ActionButton
    {
        return new class('') extends ActionButton {
            public function render(): string { return ''; }
        };
    }

    public function getDeleteButton(?string $componentName = null, string $redirectAfterDelete = '', bool $isAsync = false): ActionButton
    {
        return new class('') extends ActionButton {
            public function render(): string { return ''; }
        };
    }

    public function can(string $ability, \Illuminate\Database\Eloquent\Model $item = null): bool
    {
       // return $ability !== 'update, delete';
         return match ($ability) {
        'update', 'delete' => false,
        default => true,
    };
    }

    public function tools(): array
    {
        return [];
    }

    public function redirectAfterSave(): string
    {
        return '/resource/mensaje-enviado-resource/index-page';
    }
  
    public function filters(): array
{
    return [
      Select::make(__('menu.leido'), 'leido')
            ->options([
                '' =>__('menu.todos'),
                1 => __('menu.leido'),
                0 => __('menu.noLeido'),
            ]),
 
 
    ];
}
 
public function actions(): array
{
        $estado = request()->input('filters.leido');
    $activeStyle = 'background-color: #008542; color: white; border-radius: 0.5rem;';
   return [
        ActionButton::make(
            label: __('menu.todos'),
            url: '/resource/mensaje-enviado-resource/index-page?filters[leido]=',
        )->icon('heroicons.outline.users')
        ->customAttributes([
            'style' => ($estado === null || $estado === '') ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.leido'),
            url: '/resource/mensaje-enviado-resource/index-page?filters[leido]=1',
        )->icon('heroicons.outline.check-circle')
         ->customAttributes([
            'style' => $estado === '1' ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.noLeido'),
            url: '/resource/mensaje-enviado-resource/index-page?filters[leido]=0',
        )->icon('heroicons.outline.x-circle')
         ->customAttributes([
            'style' => $estado === '0' ? $activeStyle : '',

         ])
        
    ];
}

}
