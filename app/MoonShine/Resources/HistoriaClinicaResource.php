<?php

namespace App\MoonShine\Resources;

use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\BelongsTo;

use MoonShine\Fields\Date;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Resources\ModelResource;
use App\Models\HistoriaClinica;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Authorization\GatePolicy;
use Sweet1s\MoonshineRBAC\Traits\WithPermissionsFormComponent;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;
use MoonShine\Fields\Select;
use App\Models\Paciente;
use App\Models\Psicologo;
use Illuminate\Support\Facades\DB;
use MoonShine\Filters\SelectFilter;
use MoonShine\Filters\DateFilter;
use MoonShine\Filters\DateRangeFilter;
use MoonShine\Applies\Filters\DateRangeModelApply;
use MoonShine\Fields\DateRange;
use MoonShine\ActionButtons\ActionButton;
use Illuminate\Support\Str;

class HistoriaClinicaResource extends ModelResource
{
    public string $model = HistoriaClinica::class;
    public function title(): string
    {
        return __('menu.historiasClinicas');
    }
    public static string $controller = HistoriaClinicaController::class;


    protected bool $createInModal = false;
    protected bool $editInModal  = false;
    protected bool $detailInModal  = false;
    public function redirectAfterSave(): string
    {

        return '/resource/historia-clinica-resource/index-page';
    }
    public function fields(): array
    {
        $Psicologo = DB::table('Psicologos');
        $Paciente = DB::table('Pacientes');

        return [
            ID::make()->sortable()
                ->hideOnIndex()
                ->hideOnDetail(),
            Select::make(__('fields.paciente'), 'paciente_id')
                ->options($Paciente->pluck('name', 'id')->toArray())
                ->searchable()
                ->sortable(),
            Select::make(__('fields.psicologo'), 'psicologo_id')
                ->options($Psicologo->pluck('name', 'id')->toArray())
                ->searchable()
                ->sortable(),

            Date::make(__('fields.fecha'), 'fecha')
                ->default(now()->format('d-m-Y'))
                ->format('d-m-Y')
                ->sortable(),
            Textarea::make(__('fields.diagnostico'), 'diagnostico')
                ->hideOnIndex()
                ->showOnDetail(),
            Textarea::make(__('fields.tratamiento'), 'tratamiento')
                ->hideOnIndex()
                ->showOnDetail(),
            Textarea::make(__('fields.notasPsicologo'), 'notas_psicologo')
                ->hideonindex()
                ->showOnDetail(),

            Text::make(
                __('fields.notasPsicologo'),
                'notas_psicologo',
                fn($item) =>
                Str::limit(strip_tags($item->notas_psicologo), 100, '...')
            )
                ->hideOnForm()
                ->hideOnDetail(),



            Textarea::make(__('fields.antecedentesMedicos'), 'antecedentes_medicos')
                ->hideOnIndex()
                ->showOnDetail(),
            Textarea::make(__('fields.medicacionActual'), 'medicacion_actual')
                ->hideOnIndex()
                ->showOnDetail(),
        ];
    }


    public function rules(Model $item): array
    {
        return [];
    }

    /**
     * Filtros a aplicar en el recurso
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            // Filtro por Paciente
            Select::make(__('fields.paciente'), 'paciente_id')
                ->options(Paciente::pluck('name', 'id')->toArray())
                ->searchable()
                ->nullable(),

            // Filtro por Psicólogo
            Select::make(__('fields.psicologo'), 'psicologo_id')
                ->options(Psicologo::pluck('name', 'id')->toArray())
                ->searchable()
                ->nullable(),
            // Permite que esté vacío
            // Filtro por fecha de la cita
            DateRange::make(__('fields.fechaHora'), 'fecha')
                ->withTime()
                ->nullable(),
        ];
    }

    public function can(string $ability): bool
    {
        if ($ability === 'create') {
            return !auth()->user()?->hasRole(['Paciente', 'Administrativo']);
        }

        if ($ability === 'update') {
            return !auth()->user()?->hasRole(['Paciente', 'Administrativo']);
        }

        if ($ability === 'delete') {
            return false; // Ocultar eliminar para todos
        }
        return parent::can($ability);
    }

    public function buttons(): array
    {

        //  dd(request()->path());
        $path = request()->path();

        if (
            str_contains($path, 'index-page') ||
            str_contains($path, 'create-page')
        ) {
            return [];
        }

        $resourceUri = $this->uriKey();

        return [
            ActionButton::make(
                label: __('menu.cancelar'),
                url: ("/resource/{$this->uriKey()}/index-page")
            )
                ->icon('heroicons.outline.arrow-left')
                ->customAttributes(['class' => 'btn btn-secondary'])
        ];
    }
}
