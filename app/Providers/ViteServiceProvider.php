<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ViteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('vite', function ($expression) {
            return "<?php echo app('vite')->useHotFile(public_path('vite.hot'))->asset($expression); ?>";
        });
    }
}
