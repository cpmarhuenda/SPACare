<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('moonshine.title', 'MoonShine') }}</title>

    @moonshineStyles
    @stack('styles')
</head>
<body class="moonshine-body">

    @include('moonshine::partials.sidebar')

    <main class="main">
        @include('moonshine::partials.header')

        <div class="content">
            @yield('content')
        </div>
    </main>

    @moonshineScripts
    @stack('scripts')

    {{-- Script personalizado para menú móvil --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.querySelector('[data-toggle-sidebar]');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    document.body.classList.toggle('sidebar-open');
                });
            }
        });
    </script>
</body>
</html>
