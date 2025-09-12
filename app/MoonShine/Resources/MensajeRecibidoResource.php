<?php

namespace App\MoonShine\Resources;

use MoonShine\Resources\ModelResource;
use App\Models\Mensaje;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Fields\Text;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\Raw;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Select;
use MoonShine\Fields\Textarea;


class MensajeRecibidoResource extends ModelResource
{
    public string $model = Mensaje::class;
  //  protected string $title = 'Mensajes Recibidos';
    public function title(): string
{
    return __('menu.bandejaEntrada');  
}
    protected bool $createInModal = false;
    protected bool $editInModal  = false;
    protected bool $detailInModal  = false;
 
protected bool $canCreate = true;
protected bool $canEdit = false;
protected bool $canDelete = false;

    public function fields(): array
    {
        $item = $this->getItem();

        if ($item && $item->exists && !$item->leido && $item->destinatario_id === auth()->id()) {
            $item->leido = true;
            $item->save();
        }

        return [
            Text::make(__('fields.remitente'), 'nombre_remitente')->hideOnForm(),
            Text::make(__('fields.asunto'), 'contenido')->hideOnForm(),
        //    Text::make('Estado', 'estado_leido')->hideOnForm(),
            
            Switcher::make(__('fields.leido'), 'leido')->default(false),
             

        ];
    }

    public function rules($item): array
    {
        return [];
    }

    public function query(): Builder
    {
        return parent::query()->where('destinatario_id', auth()->id());
    }

    public function getCreateButton(?string $componentName = null, bool $isAsync = false): ActionButton
{
    return new class('') extends ActionButton {
        public function render(): string
        {
            return '';
        }
    };
}

public function getEditButton(?string $componentName = null, bool $isAsync = false): ActionButton
{
    return new class('') extends ActionButton {
        public function render(): string
        {
            return '';
        }
    };
}

public function getDeleteButton(?string $componentName = null, string $redirectAfterDelete = '', bool $isAsync = false): ActionButton
{
    return new class('') extends ActionButton {
        public function render(): string
        {
            return '';
        }
    };
}
//ESTO ES PARA QUE NO MUESTRE LOS BOTONES
public function can(string $ability, \Illuminate\Database\Eloquent\Model $item = null): bool
{
    return match ($ability) {
          'update', 'delete' => false,
        default => true,
    };
}

public function components(): array
{
    return [
        Raw::make('<style>
            .moonshine-button-export,
            .moonshine-button-import {
                display: none !important;
            }
        </style>')
    ];
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
            url: '/resource/mensaje-recibido-resource/index-page?filters[leido]=',
        )->icon('heroicons.outline.users')
        ->customAttributes([
            'style' => ($estado === null || $estado === '') ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.leido'),
            url: '/resource/mensaje-recibido-resource/index-page?filters[leido]=1',
        )->icon('heroicons.outline.check-circle')
         ->customAttributes([
            'style' => $estado === '1' ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.noLeido'),
            url: '/resource/mensaje-recibido-resource/index-page?filters[leido]=0',
        )->icon('heroicons.outline.x-circle')
         ->customAttributes([
            'style' => $estado === '0' ? $activeStyle : '',

         ])
        
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

        $groupedOptions[__('menu.-administrativos-')] = $administrativos;
        $groupedOptions[__('menu.-pacientes-')] = $pacientes;
        $groupedOptions[__('menu.-psicologos-')] = $psicologos;
    }
            
                elseif ($user->hasRole(['Super Admin','Administrativo'])) {
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

          public function redirectAfterSave(): string
    {
        return '/resource/mensaje-recibido-resource/index-page';
    }


}
