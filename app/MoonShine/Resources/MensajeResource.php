<?php

namespace App\MoonShine\Resources;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Fields\Text;
use MoonShine\Fields\Select;
use MoonShine\Fields\Textarea;
use MoonShine\Decorations\Tabs;
use MoonShine\Decorations\Tab;
use MoonShine\Resources\ModelResource;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Decorations\Block;
use Illuminate\Support\Facades\Auth;
use MoonShine\Fields\Boolean;

class MensajeResource extends ModelResource
{
    public string $model = Mensaje::class;
    protected string $title = 'Mensajes';

    protected bool $canEdit = false;
    protected bool $canDelete = false;

    public function fields(): array
    {
        return [
            Tabs::make([
                Tab::make('Recibidos', [
                    Block::make([
                        Text::make('Remitente', 'nombre_remitente')->hideOnForm(),
                        Text::make('Mensaje', 'contenido')->hideOnForm(),
                        Text::make('Estado', 'estado_leido')->hideOnForm(),
                        Boolean::make('Estado', 'leido')
    ->trueValue(true)
    ->falseValue(false)
    ->trueLabel('Leído')
    ->falseLabel('No leído')
    ->sortable(),
                    ]),
                ]),
                Tab::make('Enviados', [
                    Block::make([
                        // Estos campos SÍ estarán en el formulario de creación
                        Select::make('destinatario_id', 'Destinatario')
                            ->options($this->getDestinatarioOptions())
                            ->searchable()
                            ->required(),

                        Textarea::make('contenido', 'Mensaje')->required(),
                    ]),
                ]),
            ]),
        ];
    }

    protected function getDestinatarioOptions(): array
    {
        $user = auth()->user();
        $options = [];

        if ($user->hasRole('Paciente')) {
            $options = User::role('Super Admin')->pluck('name', 'id')->toArray()
                     + User::role('Psicologo')->pluck('name', 'id')->toArray();
        }

        if ($user->hasRole('Psicologo')) {
            $options = User::role('Super Admin')->pluck('name', 'id')->toArray()
                     + User::role('Paciente')->pluck('name', 'id')->toArray();
        }

        if ($user->hasRole('Super Admin')) {
            $options = User::role('Psicologo')->pluck('name', 'id')->toArray()
                     + User::role('Paciente')->pluck('name', 'id')->toArray();
        }

        return $options;
    }

    public function query(): Builder
    {
        $userId = auth()->id();

        return parent::query()->where(function ($q) use ($userId) {
            $q->where('remitente_id', $userId)
              ->orWhere('destinatario_id', $userId);
        });
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
        $item->remitente_id = auth()->id();
        return $item;
    }

    // Ocultar botón global "Create" para que solo se use desde la pestaña
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
}
