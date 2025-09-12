<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Recurso; 
use MoonShine\Enums\JsEvent;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Tab;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Fields\Select;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Fields\Text;
use MoonShine\Fields\date;
use MoonShine\Fields\boolean;
use MoonShine\Support\AlpineJs;
use MoonShine\Metrics\ValueMetric;
use App\Models\Catrecurso;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\File;
use Illuminate\Support\Facades\DB;
use MoonShine\Components\MoonShineComponent;
 //añadimos para gestion roles
use Sweet1s\MoonshineRBAC\Traits\WithPermissionsFormComponent;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;
//fin añadimos para gestion roles
use MoonShine\Fields\Link;
use MoonShine\Fields\Html; 
     use MoonShine\Buttons\EditButton;

use MoonShine\ActionButtons\ActionButton;
use Illuminate\Support\Facades\Storage; 

/**
 * @extends ModelResource<Recurso>
 */
class RecursoResource extends ModelResource
{
    public string $model = Recurso::class;

 //   protected string $title = 'Recursos';
    public function title(): string
{
    return __('menu.recurso');  
}

 

    public function redirectAfterSave():string
    {
 
      return '/resource/recurso-resource/index-page';
 
    

    }
 
   
    public function fields(): array
    {
      $Catrecurso=DB::table('Catrecursos')  ;
        return [
            Block::make([ 
                text::make(__('fields.titulo'),'titulo')->required(),
               
                date::make(__('fields.fechaAlta'),'fecha')
                -> default(now()->toDateString())
                -> format('d/m/Y'), 
                Switcher::make(__('fields.activo'), 'active')->default(true),
              
              File::make(__('fields.archivo'), 'archivo')
                ->disk('public')
                ->dir('recursos')
                ->allowedExtensions(['pdf', 'txt', 'doc', 'docx'])
                ->required()
                ->hideOnIndex()  
                ->showOnDetail(), 


               

              select::make(__('fields.categoria'),'categoria_id')
                
                ->options($Catrecurso->pluck('nombre','id')-> toArray())

                -> searchable()
                ->required(),

 
                ]),
            ];
                
    }

    /**
     * @param Recurso $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }

    
   
public function getEditButton(?string $componentName = null, bool $isAsync = false): ActionButton
{
    // Si el usuario tiene el rol de Paciente, devolvemos un botón que no se renderiza
    if (auth()->user()?->hasRole('Paciente')) {
        return new class('') extends ActionButton {
            public function render(): string
            {
                return ''; // No mostrar nada
            }
        };
    }

    // En caso contrario, se muestra el botón normal de edición
    return EditButton::for(
        $this,
        componentName: $componentName,
        isAsync: $isAsync
    );

}

public function uriKey(): string
{
    return 'recurso-resource';
}

    public function can(string $ability): bool
    {
        if ($ability === 'create') {
            return !auth()->user()?->hasRole('Paciente');

        } 
        
        
    if ($ability === 'delete') {
        return false; // Ocultar eliminar para todos
        //    return !auth()->user()?->hasRole('Paciente');
    }
        if ($ability === 'update') {
      //  return false; // Ocultar eliminar para todos
          return !auth()->user()?->hasRole('Paciente');
    }


        return parent::can($ability);
    }
 
public function query(): \Illuminate\Database\Eloquent\Builder
{
    $query = parent::query();

    if (auth()->user()?->hasRole('Paciente')) {
        $paciente = auth()->user()->paciente;
        if ($paciente) {
            return $query->whereHas('pacientes', function ($q) use ($paciente) {
                $q->where('paciente_id', $paciente->id);
            });
        }
        return $query->whereRaw('1 = 0'); // Si no hay paciente vinculado, no muestra nada
    }

    return $query;
} 



public function filters(): array
{
     return [
        Select::make('Activo', 'active')
            ->options([
               // '' => 'Todos',
                '' =>__('menu.todos'),
                1 => __('menu.activos'),
                0 => __('menu.inactivos'),
            ]),

    ];  
    
}

 public function actions(): array
{
        $estado = request()->input('filters.active');
    $activeStyle = 'background-color: #008542; color: white; border-radius: 0.5rem;';
   return [
        ActionButton::make(
             label:__('menu.todos'),// 'Todos', 
            url: '/resource/recurso-resource/index-page?filters[active]=',
        )->icon('heroicons.outline.users')
        ->customAttributes([
            'style' => ($estado === null || $estado === '') ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.activos'),//'Activos',
            url: '/resource/recurso-resource/index-page?filters[active]=1',
        )->icon('heroicons.outline.check-circle')
         ->customAttributes([
            'style' => $estado === '1' ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.inactivos'),//'Inactivos',
            url: '/resource/recurso-resource/index-page?filters[active]=0',
        )->icon('heroicons.outline.x-circle')
         ->customAttributes([
            'style' => $estado === '0' ? $activeStyle : '',

         ])
        
    ];
} 
 
} 