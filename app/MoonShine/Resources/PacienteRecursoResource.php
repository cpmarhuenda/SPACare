<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\PacienteRecurso;

use MoonShine\Filters\SelectFilter;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\BelongsTo;  
use MoonShine\Fields\Select;
use App\Models\Paciente;
use App\Models\Recurso;
use MoonShine\Fields\Text; 
use App\Models\Psicologo;  
use MoonShine\Components\Raw;
use MoonShine\ActionButtons\ActionButton;
use Illuminate\Support\Facades\DB;
use App\Models\CatRecurso;
use MoonShine\Fields\Date;
use MoonShine\Fields\Switcher;


class PacienteRecursoResource extends ModelResource
{
    protected string $model = PacienteRecurso::class;

  //  protected string $title = 'PacienteRecursos';
    public function title(): string
{
    return __('menu.recursosPaciente');  
}

    public function fields(): array
    {
        return [
            // BelongsTo::make('Paciente', 'paciente', fn($item) => $item->paciente?->nombre_completo ?? 'Sin nombre'),

                Select::make(__('fields.paciente'), 'paciente_id')
                ->options(Paciente::all()->pluck('nombre_completo', 'id')->toArray())
                ->searchable()
                  ->nullable()                 // añade opción vacía
            ->placeholder('— Seleccione paciente —')
            ->default(null)        
            ->required(),      // no preseleccionar nada,

                 Select::make(__('fields.recurso'), 'recurso_id')
                // Text::make(__('fields.nombre'), 'nombre')
                
                    ->options(DB::table('recursos')->pluck('titulo', 'id')->toArray())
                    ->searchable()
                         ->nullable()                 // añade opción vacía
            ->placeholder('— Seleccione un recurso —')
            ->default(null)        
            ->required()      // no preseleccionar nada,
                   /// ->hideOnDetail(), 
, 
Select::make(__('fields.categoria'), 'recurso.categoria_id')
    ->options(CatRecurso::pluck('nombre', 'id')->toArray())
    ->readonly()
    ->hideOnForm()
    ->nullable(),

Switcher::make(__('fields.descargado'), 'descargado')
    ->readonly()
    ->hideOnForm(),

Date::make(__('fields.fechaDescarga'), 'fecha_descarga')
    ->hideOnForm()
    ->format('d-m-Y H:i')
    ->sortable(),
    Switcher::make(__('fields.activo'), 'active')->default(true),


          //      select::make('Fichero','titulo'),
/*
              Text::make('Nombre del archivo', 'archivo_nombre')
    ->value(static fn($item) => $item->recurso?->titulo ?? 'Sin archivo')
    ->hideOnForm()
    ->hideOnDetail(),
*/

/*    BelongsTo::make('Recurso', 'recurso', 'titulo')
        ->hideOnIndex(),*/

        /*
Text::make('Descargar', fn($item) => 
    $item->recurso && $item->recurso->archivo
        ? '📎 ' . $item->recurso->titulo . ' (ver abajo)'
        : 'Sin archivo'
)->hideOnForm()->hideOnDetail()*/

        // Mostrar el recurso
      //  BelongsTo::make('Recurso', 'recurso', 'titulo'),
    ];
    }

    public function rules(Model $item): array
    {
        return [];
    }

     public function filters(): array                                   
    {         
        return [         
           // Filtro por Paciente
           Select::make(__('fields.paciente'), 'paciente_id')
           ->options(Paciente::pluck('name', 'id')->toArray())
           ->searchable()
           ->nullable(),
           
   
        Select::make(__('fields.activo'), 'active')
            ->options([
                '' =>__('menu.todos'),
                1 => __('menu.activos'),
                0 => __('menu.inactivos'),
            ]),

           
    
          
        ]; 
    }

        public function redirectAfterSave():string
    {
     //   $referer= Request::header('referer');
      //  return $referer ?:'/';
      return '/resource/paciente-recurso-resource/index-page';
    //return to_page($this->uriKey()); //  
    

    }

    public function indexButtons(): array
{
    return [
        ActionButton::make(__('fields.verArchivo'), function ($item) {
            return \Storage::url($item->recurso->archivo);
        })
        ->icon('heroicons.outline.paper-clip')
       // ->canSee(fn($item) => $item->recurso && $item->recurso->archivo),

->canSee(fn($item) =>
    auth()->check()
    && auth()->user()->hasAnyRole(['Super Admin', 'Administrativo', 'Psicólogo']) // roles permitidos
    && $item->recurso && $item->recurso->archivo
    ),



        ActionButton::make(__('fields.descargarArchivo'), fn($item) =>
    route('descargar.recurso', $item->id)
)
->icon('heroicons.outline.arrow-down-tray')
->canSee(fn($item) =>
    auth()->check()
    && auth()->user()->id === $item->paciente?->user_id
    && $item->recurso && $item->recurso->archivo
),
    ];
}


public function buttons(): array
{
    //boton cancelar edición
      $resourceUri = $this->uriKey();  

    return [
       
             ActionButton::make(
            label: 'Cancelar',
             url:("/resource/{$this->uriKey()}/index-page")  
        )
        ->icon('heroicons.outline.arrow-left')
        ->customAttributes(['class' => 'btn btn-secondary'])
 
    ];
}

//para que vea solo los asignados a él

public function query(): \Illuminate\Database\Eloquent\Builder
{
    $query = parent::query();

    if (auth()->user()?->hasRole('Paciente')) {
        // Filtrar solo recursos del paciente actual
        $paciente = \App\Models\Paciente::where('user_id', auth()->id())->first();

        if ($paciente) {
            $query->where('paciente_id', $paciente->id);
        } else {
            // Si no tiene paciente asociado, no mostrar nada
            $query->whereRaw('0 = 1');
        }
    }

    return $query;
}

    //IMPORTANTE, PERMISOS PERSONALIZADOS POR ROL MOSTRAR BOTON
    public function can(string $ability): bool
    {
        if ($ability === 'create') {
            return !auth()->user()?->hasRole(['Paciente','Administrativo']);

        } 
        
   
        
    if ($ability === 'delete') {
         return false; // Ocultar eliminar para todos
       //   return !auth()->user()?->hasRole('Paciente');
    }
    if ($ability === 'update') {
        //return false; // Ocultar eliminar para todos
          return !auth()->user()?->hasRole(['Paciente','Administrativo']);
    }


        return parent::can($ability);
    }

   

 public function actions(): array
{
        $estado = request()->input('filters.active');
    $activeStyle = 'background-color: #008542; color: white; border-radius: 0.5rem;';
   return [
        ActionButton::make(
           label:__('menu.todos'),
            url: '/resource/paciente-recurso-resource/index-page?filters[active]=',
        )->icon('heroicons.outline.users')
        ->customAttributes([
            'style' => ($estado === null || $estado === '') ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.activos'),
            url: '/resource/paciente-recurso-resource/index-page?filters[active]=1',
        )->icon('heroicons.outline.check-circle')
         ->customAttributes([
            'style' => $estado === '1' ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.inactivos'),
            url: '/resource/paciente-recurso-resource/index-page?filters[active]=0',
        )->icon('heroicons.outline.x-circle')
         ->customAttributes([
            'style' => $estado === '0' ? $activeStyle : '',

         ])
        
    ];
} 


}
