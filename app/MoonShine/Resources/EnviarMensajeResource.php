<?php

namespace App\MoonShine\Resources;

use App\Models\Mensaje;
use App\Models\User;
use MoonShine\Fields\Select;
use MoonShine\Fields\Textarea;
use MoonShine\Decorations\Block;
use MoonShine\Resources\ModelResource;
use Illuminate\Support\Facades\Auth;

class EnviarMensajeResource extends ModelResource
{
    public string $model = Mensaje::class;
    protected string $title = 'Enviar mensaje';


    protected bool $editInModal  = false;
    protected bool $detailInModal  = false;

    protected bool $createInModal = false;

    protected bool $canEdit = false;
    protected bool $canDelete = false;
    protected bool $canView = false;
    protected bool $canViewAny = false;

    public function fields(): array
    {
        return [
            Block::make([
                Select::make('destinatario_id', 'Destinatario')
                    ->options($this->getDestinatarioOptions())
                    ->searchable()
                    ->required(),

                Textarea::make('contenido', 'Mensaje')->required(),
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
}
