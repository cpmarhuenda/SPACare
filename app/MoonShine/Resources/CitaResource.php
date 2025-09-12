<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cita;
use MoonShine\Fields\Date;
use MoonShine\Resources\ModelResource;
use MoonShine\Resources\Request;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Fields\Text;
use MoonShine\Filters\DateRangeFilter;
use MoonShine\Applies\Filters\DateRangeModelApply;
use MoonShine\Fields\DateRange;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Select;
use Illuminate\Support\Facades\DB;
use App\Models\Paciente;
use App\Models\Psicologo;
use MoonShine\ActionButtons\ActionButton;
use App\MoonShine\Fields\HtmlField;
use App\MoonShine\Resources\Time;
use MoonShine\Components\Raw;
use MoonShine\Filters\SelectFilter;
use MoonShine\Filters\ModelFilter;
use Illuminate\Support\Facades\Auth;
use MoonShine\Fields\Url;
use MoonShine\Components\Link;
 use MoonShine\Components\Html;
 



class CitaResource extends ModelResource
{
    public string $model = Cita::class;
    public function title(): string
    {
        return __('menu.citas');
    }

    protected bool $editInModal  = false;
    protected bool $detailInModal  = false;
    protected bool $createInModal = false;

    public function redirectAfterSave(): string
    {
        return '/resource/cita-resource/index-page';
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        // Clonamos el query base
        $query = parent::query();
        $query = parent::query();

        $filters = request()->input('filters', []);


        $estado = request()->input('filters._estado_fecha');
        if ($estado === 'proximas') {
            $query->where('fecha_hora', '>=', now());
        } elseif ($estado === 'pasadas') {
            $query->where('fecha_hora', '<', now());
        }

        $user = auth()->user();

        if ($user->roles->contains('id', 1)) {
            return $query;
        }
        //los administrativos ven todas las citas
            if ($user->hasRole('Administrativo')) {
            return $query;
        }

        if ($user->hasRole('Psicologo')) {
            $psicologo = \App\Models\Psicologo::where('user_id', $user->id)->first();
            if ($psicologo) {
                return $query->where('psicologo_id', $psicologo->id);
            }
        }

        if ($user->hasRole('Paciente')) {
            $paciente = $user->paciente;
            if ($paciente) {
                return $query->where('paciente_id', $paciente->id);
            }
        }

        return $query->where('id', -1);
    }

    public function fields(): array
    {
        //comprobamos si es psicólogo, para devolver su propio id pro defecto
            $user = Auth::user();

    // Si el usuario es psicólogo, obtengo su id
    $miPsicologoId = null;
    if ($user && $user->hasRole('Psicologo')) {
        $miPsicologoId = Psicologo::where('user_id', $user->id)->value('id');
    }
        



        return [
            Block::make([
                Select::make(__('fields.tipoCita'), 'tipo')
                    ->options([
                        'puntual' => __('menu.puntual'),
                        'recurrente' => __('menu.recurrente')
                    ])

                    ->default('puntual')
                    ->sortable(),


                Select::make(__('fields.paciente'), 'paciente_id')

                    ->options(['' => __('fields.seleccionaPaciente')] + Paciente::all()->pluck('nombre_completo', 'id')->toArray())
                    ->searchable()
                    ->nullable()
                    ->sortable(),


                Select::make(__('fields.psicologo'), 'psicologo_id')


                    ->options(['' => __('fields.seleccionaPsicologo')] + Psicologo::all()->pluck('nombre_completo', 'id')->toArray())
                    //      ->options(['' => __('fields.seleccionaPsicologo')] + $opcionesPsicologos)
             //   ->default($miPsicologoId)   // <<— clave: precarga su id
              ->default($miPsicologoId)
                    ->searchable()
                    ->nullable()
                    ->sortable(),

                Date::make(__('fields.fechaHora'), 'fecha_hora')
                    ->customAttributes(['data-puntual' => 'true'])

                    ->default(now()->setTime(9, 0))
                    ->withTime()
                    ->format('d-m-Y H:i')
                    ->sortable(),

                Text::make(__('fields.horaCita'), 'hora_recurrente')
                    ->customAttributes(['type' => 'time', 'data-recurrente' => 'true'])
                    ->nullable()

                    ->hideOnIndex(),


                Text::make(__('fields.duracion'), 'duracion')

                    ->default('01:00')
                    ->customAttributes(['type' => 'time'])
                    ->sortable(),

             /* esta forma muestra con hipervinculo plano   
             Text::make(__('fields.enlaceVideollamada'), 'enlace_videollamada')
                    ->hideOnCreate()
                    ->hideOnIndex()
                    ->showOnDetail(),*/
 
         
Text::make(__('fields.enlaceVideollamada'), 'enlace_videollamada', function ($item) {
    $raw = (string) data_get($item, 'enlace_videollamada', '');
    $raw = trim($raw);
    if ($raw === '') {
        return '—';
    }

    // Asegura esquema y normaliza barras
    if (!preg_match('~^https?://~i', $raw)) {
        $raw = 'https://' . ltrim($raw, '/');
    }
    $url = preg_replace('~^https?:\/\/+~i', 'https://', $raw); // https://// -> https://
    $url = preg_replace('~(?<!:)\/\/+~', '/', $url);           // colapsa // en el resto

    $safe = e($url);

    return '<a href="'.$safe.'" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();">
                Entrar a la videollamada
            </a>';
})
->hideOnForm(),
 
                Date::make(__('fields.fechaInicio'), 'fecha_inicio')

                    ->customAttributes(['data-recurrente' => 'true'])
                    ->hideOnIndex()
                    ->showOnDetail(),

                Text::make(__('fields.periodicidadDiasEntreCitas'), 'periodicidad')

                    ->customAttributes(['data-recurrente' => 'true'])
                    ->hideOnIndex()
                    ->showOnDetail(),

                Date::make(__('fields.fechaFinalizacion'), 'fecha_fin')
                    ->nullable()
                    ->customAttributes(['data-recurrente' => 'true'])
                    ->hideOnIndex()
                    ->showOnDetail(),
            ]),

            view('moonshine.scripts.citas-toggle'),
        ];
    }

    public function rules(Model $item): array
    {
        $tipo = request('tipo');

        $rules = [
            'duracion' => ['required'],
            'paciente_id' => ['required'],
            'psicologo_id' => ['required'],
        ];

        if ($tipo === 'puntual') {
            $rules['fecha_hora'] = ['required', 'date'];
        }

        if ($tipo === 'recurrente') {
            $rules['fecha_inicio'] = ['required', 'date'];
            $rules['fecha_fin'] = ['required', 'date'];
            $rules['periodicidad'] = ['required', 'numeric', 'min:1'];
            $rules['hora_recurrente'] = ['required', 'date_format:H:i'];
        }

        return $rules;
    }

    public function filters(): array
    {

        return [
            Select::make(__('fields.estadoCita'), 'estado_fecha')
                ->options([
                    '' => __('menu.todas'),
                    'proximas' => __('menu.proximas'),
                    'pasadas' => __('menu.pasadas'),
                ]),
            DateRange::make(__('fields.fechaHora'), 'fecha_hora')->withTime(),

            Select::make(__('fields.paciente'), 'paciente_id')
                ->options(
                    \App\Models\Paciente::all()->pluck('nombre_completo', 'id')->toArray()
                )
                ->nullable(),

            Select::make(__('fields.psicologo'), 'psicologo_id')
                ->options(
                    \App\Models\Psicologo::all()->pluck('nombre_completo', 'id')->toArray()
                )
                ->nullable(),
        ];
    }

    public function beforeCreating(Model $item): Model
    {
        $request = request();
        unset($item->hora_recurrente);

        if ($request->get('tipo') === 'puntual') {
            $item->tipo = 'puntual';
            $item->paciente_id = $request->get('paciente_id');
            $item->psicologo_id = $request->get('psicologo_id');
            $item->fecha_hora = $request->get('fecha_hora');
            $item->duracion = $request->get('duracion');
            $item->enlace_videollamada = $request->get('enlace_videollamada');
            return $item;
        }
        if ($request->get('tipo') === 'recurrente') {
            $fecha_inicio = \Carbon\Carbon::parse($request->get('fecha_inicio'))->startOfDay();
            $fecha_fin = \Carbon\Carbon::parse($request->get('fecha_fin'))->endOfDay();
            $horaSeleccionada = $request->input('hora_recurrente') ?? '09:00:00';
            $periodicidad = (int) $request->get('periodicidad');
            while ($fecha_inicio <= $fecha_fin) {
                $cita = new Cita();
                $cita->tipo = 'recurrente';
                $cita->paciente_id = $request->get('paciente_id');
                $cita->psicologo_id = $request->get('psicologo_id');
                $cita->fecha_hora = $fecha_inicio->copy()->setTimeFromTimeString($horaSeleccionada);
                $cita->duracion = $request->get('duracion');
                $cita->enlace_videollamada = $request->get('enlace_videollamada');
                $cita->save();
                $fecha_inicio->addDays($periodicidad);
            }

            $dummy = new Cita();
            $dummy->exists = true;
            return $dummy;
        }

        return $item;
    }


    public function can(string $ability): bool
    {
        if ($ability === 'create') {
            return !auth()->user()?->hasRole('Paciente');
        }

        if ($ability === 'update') {
            return !auth()->user()?->hasRole('Paciente');
        }


        if ($ability === 'delete') {
            //return false; // Ocultar eliminar para todos
            return !auth()->user()?->hasRole('Paciente');
        }
        return parent::can($ability);
    }

    public function actions(): array
    {
        $estado = request()->input('filters._estado_fecha');
        return [
            ActionButton::make(
                label: __('menu.proximas'),
                url: ('/resource/cita-resource/index-page?filters[_estado_fecha]=proximas')
            )->icon('heroicons.outline.clock')
                ->customAttributes([
                    'style' => $estado === 'proximas'
                        ? 'background-color: #008542; color: white; border-radius: 0.5rem;'
                        : ''
                ]),

            ActionButton::make(
                label: __('menu.todas'),
                url: ('/resource/cita-resource/index-page?filters[_estado_fecha]=')
            )->icon('heroicons.outline.calendar-days')
                ->customAttributes([
                    'style' => ($estado === null || $estado === '')
                        ? 'background-color: #008542; color: white; border-radius: 0.5rem;'
                        : ''
                ]),


            ActionButton::make(
                label: __('menu.pasadas'),
                url: ('/resource/cita-resource/index-page?filters[_estado_fecha]=pasadas'),
            )
                ->icon('heroicons.outline.archive-box')
                ->customAttributes([
                    'style' => $estado === 'pasadas'
                        ? 'background-color: #008542; color: white; border-radius: 0.5rem;'
                        : ''
                ]),


        ];
    }

    public function buttons(): array
    {

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
