<?php
declare(strict_types=1);

namespace App\MoonShine\Resources;

use MoonShine\Resources\MoonShineProfileResource as BaseProfileResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Text;
use MoonShine\Fields\Email;
use Illuminate\Support\Facades\Log;
use App\MoonShine\Pages\ReadonlyProfilePage;

final class MoonShineProfileResource extends BaseProfileResource
{
    // ⚠️ Mantén tu página personalizada
    public function pages(): array
    {
        return [
            ReadonlyProfilePage::make('readonly-profile-page'),
        ];
    }

    // ✅ Campos de la pestaña principal que usará ProfilePage
    public function mainFields(): array
    {
        Log::info('PROFILE mainFields (custom) called'); // debug opcional

        return [
            Block::make(__('Mi perfil'), [
                Text::make(__('Nombre completo'), 'nombre_completo')->readonly(),
                Email::make(__('Correo electrónico'), 'email')->readonly(),
            ]),
        ];
    }

    // 🔁 Alias por si la plantilla llama a profileFields()
    public function profileFields(): array
    {
        Log::info('PROFILE profileFields (custom) called'); // debug opcional
        return $this->mainFields();
    }

    // ❌ Sin pestaña de contraseña
    public function passwordFields(): array
    {
        Log::info('PROFILE passwordFields (custom) called'); // debug opcional
        return [];
    }
}
