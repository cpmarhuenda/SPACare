<?php

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Paciente;
use App\Models\User;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;

use MoonShine\Fields\Number;
use MoonShine\Fields\Date;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Select;
use MoonShine\Decorations\Block;
use MoonShine\Resources\ModelResource;
use Illuminate\Support\Facades\DB;
use MoonShine\ActionButtons\ActionButton;
use App\MoonShine\Resources\RecursoResource;
use MoonShine\Fields\Relationships\BelongsTo;
//use MoonShine\ActionButtons\ResourceAction;
use MoonShine\Actions\ResourceAction;
use MoonShine\Pages\Components\Modal; 
use MoonShine\Fields\Html;





class PacienteResource extends ModelResource
{
  //  public string $title = 'Pacientes';
public function title(): string
{
    return __('menu.pacientes');  
}

    public string $model = Paciente::class;

   public function fields(): array
{

           \Log::info('entra a fields PacienteResource');
     $request = request();
    $isEditing = str_contains($request->path(), 'edit-page');

    $fields = [
        Block::make(__('fields.datosPersonales'), [
            Text::make(__('fields.nombre'), 'name')->sortable(),
        //    Text::make('Apellidos', 'apellidos'),
            Number::make(__('fields.telefono'), 'telefono')->hideOnIndex(),
            textarea::make(__('fields.direccion'), 'direccion')->hideOnIndex(),
            Switcher::make(__('fields.activo'), 'active')->default(true)
            ->sortable(),

        
            //
            Date::make(__('fields.fechaNacimiento'), 'fecha_nacimiento')
             ->format('d-m-Y')->hideOnIndex(), 
            //Text::make('Email', fn($item) => $item->user?->email)->sortable(),
            Text::make(__('fields.email'), 'user_email', fn($item) => $item->user?->email ?? '-')
    ->sortable()
    ->hideOnIndex()
    ->hideOnForm(),

            // Campo para subir la foto
        \MoonShine\Fields\Image::make(__('fields.fotografia'), 'fotografia')
            ->disk('public')
            ->dir('psicologos')
            ->nullable()
            ->removable()
            ->customName(fn($file) => uniqid() . '.' . $file->getClientOriginalExtension())
        ,
     

            //Text::make('Email', 'email'),
            Date::make(__('fields.fechaAlta'), 'falta')->default(now()->toDateString())->hideOnIndex()
            ->format('d-m-Y'),  
        ]),

        Block::make(__('fields.datosClinicos'), [
            BelongsTo::make(__('fields.psicologoAsignado'), 'psicologo', 'name')
                ->required()
                ->searchable()
                ->sortable(),
            textarea::make(__('fields.historialMedico'), 'historial_medico')->hideOnIndex(),
            Text::make(__('fields.estadoSalud'), 'estado_salud_actual')->hideOnIndex(),
            Date::make(__('fields.primeraConsulta'), 'fecha_primera_consulta')->hideOnIndex()
            ->format('d-m-Y'), 
            textarea::make(__('fields.observaciones'), 'observaciones')->hideOnIndex(),
        ]),
    ];

    // ✅ Solo añadimos el bloque de contraseña si estamos creando
    if (! $isEditing) {
        $fields[] = Block::make(__('fields.contraseña'), [
            Text::make(__('fields.contraseñaTemporal'), 'password_temp')
                ->customAttributes(['type' => 'password'])
                ->hideOnIndex()
                ->hideOnDetail(),

            Text::make(__('fields.repetirContraseña'), 'password_repeat_temp')
                ->customAttributes(['type' => 'password'])
                ->hideOnIndex()
                ->hideOnDetail(),
        ]);
    }

    return $fields;
}
   
    //boton ver historia clinica
    public function indexButtons(): array
{
    
     
    return [
        ActionButton::make(__('fields.historiaClinica'), static fn ($data) => url(
            '/resource/historia-clinica-resource/index-page?filters[paciente_id]=' . $data->getKey()  // Genera la URL completa con el ID dinámico
        ))
        ->icon('heroicons.outline.document-text') ,// Icono de Heroicons (puedes cambiarlo a otro si lo prefieres)
          ActionButton::make(__('fields.recursos'), static fn ($data) => url(
             '/resource/paciente-recurso-resource/index-page?filters[paciente_id]=' . $data->getKey()
))
    ->icon('heroicons.outline.folder'),

    ActionButton::make(__('fields.resumenClinico'), fn($paciente) =>
    route('resumen.clinico', $paciente->id)
)->icon('heroicons.outline.clipboard-document'),
 

 
    ]; 
}

public function filters(): array
{
    return [
               Select::make(__('fields.activo'), 'active')
            ->options([
                '' =>__('menu.todos'),
                1 => __('menu.activos'),
                0 => __('menu.inactivos'),
            ]),
                    Select::make(__('fields.psicologoAsignado'), 'psicologo_id')
            ->options(
                \App\Models\Psicologo::orderBy('name')->get()->pluck('nombre_completo', 'id')->toArray()
            )
            ->nullable(),
    ];
}

public function actions(): array
{
        $estado = request()->input('filters.active');
    $activeStyle = 'background-color: #008542; color: white; border-radius: 0.5rem;';
   return [
        ActionButton::make(
            label:__('menu.todos'),
            url: '/resource/paciente-resource/index-page?filters[active]=',
        )->icon('heroicons.outline.users')
        ->customAttributes([
            'style' => ($estado === null || $estado === '') ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.activos'),
            url: '/resource/paciente-resource/index-page?filters[active]=1',
        )->icon('heroicons.outline.check-circle')
         ->customAttributes([
            'style' => $estado === '1' ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.inactivos'),
            url: '/resource/paciente-resource/index-page?filters[active]=0',
        )->icon('heroicons.outline.x-circle')
         ->customAttributes([
            'style' => $estado === '0' ? $activeStyle : '',

         ])
        
    ];
}
public function rules($item = null): array
{
  //  dd(request()->all());

        $isEditing = $item && $item->exists;

    return [
        'name' => 'required|string|max:255',
       // 'user_email' => 'required|email|unique:users,email,' . ($item?->user_id ?? 'null'),
        'user_email' => $isEditing
            ? 'nullable|email|unique:users,email,' . ($item->user_id ?? 'null')
            : 'required|email|unique:users,email',
       'password_temp' => $isEditing ? 'nullable|min:8|same:password_repeat_temp' : 'required|min:8|same:password_repeat_temp',
        'password_repeat_temp' => $isEditing ? 'nullable|same:password_temp' : 'required_with:password_temp|same:password_temp',

    ];
}

    public function redirectAfterSave():string
    {
     //   $referer= Request::header('referer');
      //  return $referer ?:'/'; 
      return '/resource/paciente-resource/index-page';

    }


    public function beforeCreating(Model $item): Model
    {
        // Evitar que MoonShine intente guardar estos campos
        $item->offsetUnset('password_temp');
        $item->offsetUnset('password_repeat_temp');

        return $item;
    }

    public function afterCreating(Model $item): Model
{
    
    dd(request()->all());

    DB::transaction(function () use ($item) {
        $email = request()->input('moonshineFormData.user_email');
        $email = data_get(request()->input(), 'moonshineFormData.user_email');

        $password = request()->input('moonshineFormData.password_temp');

        // Verifica que email no sea null antes de continuar
        if (!$email) {
            throw new \Exception('El email no fue proporcionado');
        }

        $user = User::create([
            'name' => $item->name,
           //'email' => $email,
              'email' => $email, 
            'password' => bcrypt($password),
        ]);

        $user->assignRole('Paciente');
        $item->user_id = $user->id;
        $item->save();
    });

    return $item;
}
//ponemos para que salga el boton create solo en el admin
public function getCreateButton(?string $componentName = null, bool $isAsync = false): \MoonShine\ActionButtons\ActionButton
{
    $user = auth()->user();

    if (!$user) {
        // Si no está logado, ocultamos
        return new class('') extends \MoonShine\ActionButtons\ActionButton {
            public function render(): string
            {
                return '';
            }
        };
    }

    // Obtenemos los IDs de roles asignados al usuario
    $userRoleIds = $user->roles->pluck('id')->toArray();

    // Si tiene rol de Paciente (2) o Psicologo (4), no dejamos crear
    if (in_array(2, $userRoleIds) || in_array(4, $userRoleIds)) {
        return new class('') extends \MoonShine\ActionButtons\ActionButton {
            public function render(): string
            {
                return '';
            }
        };
    }

    // Si no, permitimos el botón normal de crear
    return parent::getCreateButton($componentName, $isAsync);
}
public function afterUpdating(Model $item): Model
{
    if (request()->filled('password_temp')) {
        $password = request('password_temp');

        if ($item->user) {
            $item->user->update([
                'password' => bcrypt($password),
            ]);
        }
    }

    return $item;
}

    public function can(string $ability): bool
    {
        if ($ability === 'create') {
            return !auth()->user()?->hasRole('Paciente');

        } 
        
        
    if ($ability === 'delete') {
        return false; // Ocultar eliminar para todos
    }


        return parent::can($ability);
    }

    
public function formFields(): array
{
       \Log::info('entra a formFields PacienteResource');

    $item = $this->getItem();
    $isEditing = $item?->exists ?? false;

      $user = auth()->user();
    $rolesAutorizados = [1, 3]; // Super Admin y Administrativo

 
    $fields = [
        Block::make(__('fields.datosPersonales'), [
            Text::make(__('fields.nombre'), 'name'),
         //   Text::make('Apellidos', 'apellidos'),
            Number::make(__('fields.telefono'), 'telefono'),
            Text::make(__('fields.direccion'), 'direccion')->hideOnIndex(),
 
            //
            Date::make(__('fields.fechaNacimiento'), 'fecha_nacimiento')
              ->format('d-m-Y'),  
           Text::make(__('fields.email'), 'user_email')
    ->customAttributes(['type' => 'email'])
    ->required()
    ->hideOnIndex()
    ->hideOnDetail()
    ->hideOnUpdate(),

             
          



            Date::make(__('fields.fechaAlta'), 'falta')->default(now()->toDateString())->hideOnIndex()
              ->format('d-m-Y'), 

                 // Campo para subir la foto
        \MoonShine\Fields\Image::make(__('fields.fotografia'), 'fotografia')
            ->disk('public')
            ->dir('psicologos')
            ->nullable()
            ->removable()
            ->customName(fn($file) => uniqid() . '.' . $file->getClientOriginalExtension()),
  
        ]),

        Block::make(__('fields.datosClinicos'), [
            BelongsTo::make(__('fields.psicologoAsignado'), 'psicologo', 'name')
                ->required()
                ->searchable(),
            Textarea::make(__('fields.historialMedico'), 'historial_medico')->hideOnIndex(),
            Text::make(__('fields.estadoSalud'), 'estado_salud_actual')->hideOnIndex(),
            Date::make(__('fields.primeraConsulta'), 'fecha_primera_consulta')->hideOnIndex()->format('d-m-Y'), 
            textarea::make(__('fields.observaciones'), 'observaciones')->hideOnIndex(),
        ]),
    ];

    
    if (! $isEditing) {
        $fields[] = Block::make(__('fields.contraseña'), [
            Text::make(__('fields.contraseñaTemporal'), 'password_temp')
                ->customAttributes(['type' => 'password'])
                ->hideOnIndex()
                ->hideOnDetail(),

            Text::make(__('fields.repetirContraseña'), 'password_repeat_temp')
                ->customAttributes(['type' => 'password'])
                ->hideOnIndex()
                ->hideOnDetail(),
        ]);
    } 

    return $fields;
}

protected function resetPassword(Model $item, array $data): Model
{
       \Log::info('entra a resetPassword');
    validator($data, [
        'password_reset' => ['required', 'min:8', 'same:password_reset_repeat'],
        'password_reset_repeat' => ['required'],
    ])->validate();

    if ($item->user) {
        $item->user->update([
            'password' => bcrypt($data['password_reset']),
        ]);
    }

    return $item;
}

public function buttons(): array
{
    //boton cancelar edición
      $resourceUri = $this->uriKey();  

    return [
       
             ActionButton::make(
            label:__('menu.cancelar'),
             url:("/resource/{$this->uriKey()}/index-page")  
        )
        ->icon('heroicons.outline.arrow-left')
        ->customAttributes(['class' => 'btn btn-secondary'])
 
    ];
}


public function detailButtons(): array
{
    return [
        ActionButton::make(__('fields.resumenClinico'), fn($paciente) =>
            route('resumen.clinico', $paciente->id)
        )->icon('heroicons.outline.clipboard-document'),
      ActionButton::make(__('fields.cancelar'), fn() =>
            url("/resource/{$this->uriKey()}/index-page")
        )->icon('heroicons.outline.arrow-left')
         ->customAttributes(['class' => 'btn btn-secondary']),
    ];
}

}
