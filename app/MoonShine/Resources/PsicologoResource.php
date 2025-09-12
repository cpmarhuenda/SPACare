<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Psicologo;
use App\Models\User;
use MoonShine\Fields\date;
use MoonShine\Resources\ModelResource;
use MoonShine\Resources\Request;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Fields\Email;
use MoonShine\Fields\Email2;
use MoonShine\Fields\Select;
use Moonshine\Fields\Pass;
use Moonshine\Fields\Password;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\SwitchBoolean;
use MoonShine\Components\MoonShineComponent;
use Illuminate\Support\Facades\DB; 
//añadimos para gestion roles
use Sweet1s\MoonshineRBAC\Traits\WithPermissionsFormComponent;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;
use Moonshine\Fields\Password as MoonshinePassword;
use Illuminate\Support\Facades\Log;
use MoonShine\Fields\Html; 
use MoonShine\Fields\Stack;
use MoonShine\Components\Raw;

use MoonShine\Filters\SelectFilter;
use MoonShine\Filters\ModelFilter;
use MoonShine\Filters\Filter;





use MoonShine\ActionButtons\ActionButton;
 
class PsicologoResource extends ModelResource
{
   

   // public string $title = 'Psicologos';
public function title(): string
{
    return __('menu.psicologos');  
}
    protected bool $createInModal = false;
    protected bool $editInModal  = false;
    protected bool $detailInModal  = false; 
    public function redirectAfterSave():string
    {
 
      return '/resource/psicologo-resource/index-page';

    }

    /**
     * @return list<MoonShineComponent|Field>
     */
  

 
    public string $model = Psicologo::class;
 
 public function fields(): array
    {
      
            return [
                 Block::make('Datos generales', [
        Text::make(__('fields.nombre'), 'name')->required(), 
       
       Text::make(__('fields.email'), 'user_email', fn($item) => $item->user?->email ?? '-')
    ->sortable()
        ->hideOnIndex()
    
    ->hideOnForm(),
       Switcher::make(__('fields.activo'), 'active')->default(true),
       
       
        Date::make(__('fields.fechaAlta'), 'falta')->format('Y-m-d')->hideOnIndex(),

        Text::make(__('fields.numeroColegiado'), 'numero_colegiado')->nullable()->hideOnIndex(),
        Text::make(__('fields.especialidad'), 'especialidad')->nullable(),
        Textarea::make(__('fields.formacion'), 'formacion')->nullable()->hideOnIndex(),

        // Campo para subir la foto
        \MoonShine\Fields\Image::make(__('fields.fotografia'), 'fotografia')
            ->disk('public')
            ->dir('psicologos')
            ->nullable()
            ->removable()
            ->customName(fn($file) => uniqid() . '.' . $file->getClientOriginalExtension())
              ]),
     

Block::make(__('fields.resumen'), [
    
      Text::make(__('fields.pacientesAsignados'), 'total_pacientes')
    ->hideOnForm()
    ->showOnDetail(),

   Text::make(__('fields.citasRealizadas'), 'total_citas')
    ->hideOnForm()
    ->showOnDetail()
    ->showOnIndex(), 
  
 
 

   


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
 
    public function formComponents(): array
    {
        return [
            Text::make(__('fields.password'), 'password')->hideOnIndex(),
            Text::make(__('fields.repetirPassword'), 'password_repeat')->hideOnIndex(),
        ];
    } 
    
    public function afterCreating(Model $item): Model
    {
        Log::info('afterCreating: Creando usuario para el Psicologo', ['Psicologo_id' => $item->id]);

      
    DB::transaction(function () use ($item) {
        $email = request()->input('moonshineFormData.user_email');
        $email = data_get(request()->input(), 'moonshineFormData.user_email');

        $password = request()->input('moonshineFormData.password_temp');
 
        if (!$email) {
            throw new \Exception('El email no fue proporcionado');
        }

        $user = User::create([
            'name' => $item->name, 
              'email' => $email, 
            'password' => bcrypt($password),
        ]);

        $user->assignRole('Psicologo');
        $item->user_id = $user->id;
        $item->save();
    });

    return $item;
    }
 
    public function rules($item = null): array
    {
     
        $isEditing = $item && $item->exists;

    return [
        'name' => 'required|string|max:255', 
        'user_email' => $isEditing
            ? 'nullable|email|unique:users,email,' . ($item->user_id ?? 'null')
            : 'required|email|unique:users,email',
       'password_temp' => $isEditing ? 'nullable|min:8|same:password_repeat_temp' : 'required|min:8|same:password_repeat_temp',
        'password_repeat_temp' => $isEditing ? 'nullable|same:password_temp' : 'required_with:password_temp|same:password_temp',

    ];

    }
        public function beforeCreating(Model $item): Model
    {
        // Evitar que MoonShine intente guardar estos campos
        $item->offsetUnset('password_temp');
        $item->offsetUnset('password_repeat_temp');

        return $item;
    }

     public function afterUpdating(Model $item): Model
    {
        $user = $item->user;

        if ($user) {
            $user->name = $item->name;
            $user->email = $item->email;

            if (request()->filled('password')) {
                $user->password = bcrypt(request()->input('password'));
            }

            $user->save();
        }

        return $item;
    }

    public function resourceTitleAttribute(): string
{
    return 'name'; // o el campo que quieras mostrar en el combo
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


    ];  
    
}

 public function actions(): array
{
        $estado = request()->input('filters.active');
    $activeStyle = 'background-color: #008542; color: white; border-radius: 0.5rem;';
   return [
        ActionButton::make(
            label:__('menu.todos'),
            url: '/resource/psicologo-resource/index-page?filters[active]=',
        )->icon('heroicons.outline.users')
        ->customAttributes([
            'style' => ($estado === null || $estado === '') ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.activos'),
            url: '/resource/psicologo-resource/index-page?filters[active]=1',
        )->icon('heroicons.outline.check-circle')
         ->customAttributes([
            'style' => $estado === '1' ? $activeStyle : ''
        ]),

        ActionButton::make(
            label: __('menu.inactivos'),
            url: '/resource/psicologo-resource/index-page?filters[active]=0',
        )->icon('heroicons.outline.x-circle')
         ->customAttributes([
            'style' => $estado === '0' ? $activeStyle : '',

         ])
        
    ];
} 
 

    public function indexButtons(): array
{
    
     
    return [
        ActionButton::make(__('fields.citas'), static fn ($data) => url(
            '/resource/cita-resource/index-page?filters[psicologo_id]=' . $data->getKey()  // Genera la URL completa con el ID dinámico
        ))
        ->icon('heroicons.outline.calendar-days') ,// Icono de Heroicons (puedes cambiarlo a otro si lo prefieres)
            ActionButton::make(__('fields.pacientes'), static fn ($data) => url(
             '/resource/paciente-resource/index-page?filters[psicologo_id]=' . $data->getKey()
))
    ->icon('heroicons.outline.user-circle'),
 
 
    ]; 
}

 
public function buttons(): array
{
    //boton cancelar edición
      $resourceUri = $this->uriKey();  

    return [
       
             ActionButton::make(
           label:__('fields.cancelar'),
             url:("/resource/{$this->uriKey()}/index-page")  
        )
        ->icon('heroicons.outline.arrow-left')
        ->customAttributes(['class' => 'btn btn-secondary'])
 
    ];
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
           Text::make(__('fields.email'), 'user_email')
    ->customAttributes(['type' => 'email'])
    ->required()
    ->hideOnIndex()
    ->hideOnDetail()
    ->hideOnUpdate(),

    
             
          
    Text::make(__('fields.numeroColegiado'), 'numero_colegiado')->nullable()->hideOnIndex(),
        Text::make(__('fields.especialidad'), 'especialidad')->nullable(),
        Textarea::make(__('fields.formacion'), 'formacion')->nullable()->hideOnIndex(),

        // Campo para subir la foto
        \MoonShine\Fields\Image::make(__('fields.fotografia'), 'fotografia')
            ->disk('public')
            ->dir('psicologos')
            ->nullable()
            ->removable()
            ->customName(fn($file) => uniqid() . '.' . $file->getClientOriginalExtension()),
              


            Date::make(__('fields.fechaAlta'), 'falta')->default(now()->toDateString())->hideOnIndex()
              ->format('d-m-Y'), 
        ]),

       
    ];

    
    if (! $isEditing) {
        $fields[] = Block::make('Contraseña', [
            Text::make('Contraseña temporal', 'password_temp')
                ->customAttributes(['type' => 'password'])
                ->hideOnIndex()
                ->hideOnDetail(),

            Text::make('Repetir contraseña', 'password_repeat_temp')
                ->customAttributes(['type' => 'password'])
                ->hideOnIndex()
                ->hideOnDetail(),
        ]);
    } 

    return $fields;
}
 

    public function can(string $ability): bool
    {
        
        
    if ($ability === 'delete') {
        return false; // Ocultar eliminar para todos
    }
      

        return parent::can($ability);
    }
} 
