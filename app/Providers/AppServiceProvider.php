<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event; 
use Illuminate\Support\Facades\Request;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    
    public function boot(): void
    {
        if (Request::is('admin*')) {
            View::composer('*', function () {
               // echo '<link rel="stylesheet" href="/css/custom.css?v=' . time() . '">';
                echo '<script>
                const css = document.createElement("link");
                css.rel = "stylesheet";
                css.href = "/css/custom.css?v=' . time() . '";
                document.head.appendChild(css);
            </script>';
            });
        }
    }
    /*
public function boot(): void
{
      if (Request::is('admin*')) {
        View::composer('*', function ($view) {
            $view->with('customCssLink', '<link rel="stylesheet" href="' . asset('css/custom.css') . '?v=' . time() . '">');
        });
    }
}*/
}
