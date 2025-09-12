<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('moonshine::layouts.shared.meta')
    @include('moonshine::layouts.shared.styles')
    {!! $customCssLink ?? '' !!}
</head>
<body class="antialiased">
    @include('moonshine::layouts.shared.navbar')

    <main class="main relative">
        @include('moonshine::layouts.shared.sidebar')

        <div class="main__content">
            @include('moonshine::layouts.shared.breadcrumbs')
            @include('moonshine::layouts.shared.flash')
            @include('moonshine::layouts.shared.header')

            <div class="main__content-inner">
                @yield('content')
            </div>
        </div>
    </main>

    @include('moonshine::layouts.shared.scripts')

    {{-- 🔽 Aquí puedes incluir tu script de comportamiento dinámico --}}
    @includeIf('moonshine.partials.toggle-cita-script')
</body>
</html>
