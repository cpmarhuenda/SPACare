<?php

namespace App\MoonShine\Resources;

use App\Models\User;
use Illuminate\Validation\Rule;
use MoonShine\Fields\{ID, Text, Email, Password, PasswordRepeat, Image, Date};
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\{Block, Column, Grid};
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions; 
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Select;
 use MoonShine\Filters\SelectFilter;
 use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Switcher;
use App\MoonShine\Filters\UserTypeFilter;



class UserResource extends ModelResource
{
     

    protected string $model = User::class; 
    protected string $titleField = 'nombre_completo';


    //public function title(): string { return 'Usuarios'; }
        public function title(): string
{
    return __('menu.usuarios');  
}

 
 

 public function fields(): array
{
    // dd('Cargando fields');
    return [
        Grid::make([
            Column::make([
                Block::make('Información básica', [
                    ID::make()->sortable()
                    ->hideOnIndex(),
               

Text::make('Nombre completo', 'nombre_completo')
 
    ->hideOnForm(),



                    Email::make('Email', 'email')->sortable()->required(),
                
                    Date::make('Creado el', 'created_at')->format('d.m.Y')
                          ->default(now()->toDateTimeString())
                          ->sortable()->hideOnForm(),


                      Text::make('Roles', 'roles_list')
                    
    ->hideOnForm(),
    Switcher::make('Activo', 'active')->default(true),


                ]),
                Block::make('Contraseña', [
                    Password::make('Contraseña', 'password')
                            ->customAttributes(['autocomplete'=>'new-password'])->eye()
                            ->hideOnIndex(),
                    PasswordRepeat::make('Repetir contraseña', 'password_repeat')
                            ->customAttributes(['autocomplete'=>'confirm-password'])->eye()
                            ->hideOnIndex(),
                ]),
          
            ]),
        ]),
    ];
}

    public function rules($item): array
    {
        return [
          //  'name'     => 'required',
            'email'    => ['required','email', Rule::unique('users','email')->ignoreModel($item)],
            'password' => $item->exists
                          ? 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat'
                          : 'required|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }
 
    
    public function search(): array 
    { 
                
        return ['id','email']; 
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
            label: 'Cancelar',
             url:("/resource/{$this->uriKey()}/index-page")  
        )
        ->icon('heroicons.outline.arrow-left')
        ->customAttributes(['class' => 'btn btn-secondary'])
    ];
}

    public function redirectAfterSave(): string
    {
        return '/resource/user-resource/index-page';
    }


    public function filters(): array
{
     return [
        Select::make('Activo', 'active')
            ->options([
                '' => 'Todos',
                1 => 'Activos',
                0 => 'Inactivos',
            ]) ,
        Select::make('Tipo de usuario', 'tipo_usuario')
    ->options([
        '' => 'Todos',
        1 => 'Super Admin',
        2 => 'Paciente',
        3 => 'Administrativo',
        4 => 'Psicólogo',
    ])
    ->nullable(),
    ];
    
}
 public function actions(): array
{
  
$estado = (string) request('filters.active', '');
    $tipo = (string) request('filters.tipo_usuario', '');

    $activeStyle = 'background-color: #008542; color: white; border-radius: 0.5rem;';
    $defaultStyle = '';

    $actions = [];

    // Botón "Todos" (sin filtros)
    $isTodos = ($estado === '' && $tipo === '');
    $actions[] = ActionButton::make('Todos', '/resource/user-resource/index-page')
        ->icon('heroicons.outline.users')
        ->customAttributes([
            'style' => $isTodos ? $activeStyle : $defaultStyle
        ]);

    // Botones por estado
    foreach (['1' => 'Activos', '0' => 'Inactivos'] as $val => $label) {
        $isActive = ($estado === (string) $val);
        $url = "/resource/user-resource/index-page?filters[active]=$val&filters[tipo_usuario]=$tipo";
        $actions[] = ActionButton::make($label, $url)
            ->icon($val === '1' ? 'heroicons.outline.check-circle' : 'heroicons.outline.x-circle')
            ->customAttributes([
                'style' => $isActive ? $activeStyle : $defaultStyle
            ]);
    }

    // Botones por tipo
    foreach ([
        '2' => 'Paciente',
        '3' => 'Administrativo',
        '4' => 'Psicólogo',
    ] as $val => $label) {
        $isActive = ($tipo === (string) $val);
        $url = "/resource/user-resource/index-page?filters[active]=$estado&filters[tipo_usuario]=$val";
        $actions[] = ActionButton::make($label, $url)
            ->icon('heroicons.outline.user')
            ->customAttributes([
                'style' => $isActive ? $activeStyle : $defaultStyle
            ]);
    }

    return $actions;
} 

    public function can(string $ability): bool
    {
        if ($ability === 'create') {
             return false; // Ocultar eliminar para todos

        } 
        
        
    if ($ability === 'delete') {
        return false; // Ocultar eliminar para todos
    }
        if ($ability === 'update') {
         return !auth()->user()?->hasRole('Paciente');
    }


        return parent::can($ability);
    }
 

}