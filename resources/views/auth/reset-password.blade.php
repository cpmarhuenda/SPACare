@extends('moonshine::layouts.login')

@section('content')
<div class="authentication">
    <div class="authentication-logo">
        <a href="{{ url('/') }}" rel="home">
            <img class="h-16"
                 src="{{ config('moonshine.logo') ?: asset('vendor/moonshine/logo.svg') }}"
                 alt="{{ config('moonshine.title') }}">
        </a>
    </div>

    <div class="authentication-content">
        <div class="authentication-header">
            <h1 class="title">Restablecer contraseña</h1>
            <p class="description">Introduce tu nueva contraseña</p>
        </div>

        {{-- Mensajes de estado/errores con estilos MoonShine --}}
        @if (session('status'))
            <div class="alert alert-success text-sm mb-4">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error text-sm mb-4">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $email ?? '') }}" required
                       class="form-input w-full">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Nueva contraseña</label>
                <input id="password" name="password" type="password" required
                       class="form-input w-full">
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="form-input w-full">
            </div>

            <button type="submit" class="btn btn-primary w-full">
                Restablecer contraseña
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('moonshine.login') }}" class="text-2xs underline">
                Volver al inicio de sesión
            </a>
        </div>

        <p class="text-center text-2xs mt-4">
            {!! config('moonshine.auth.footer', '') !!}
        </p>
    </div>
</div>
@endsection
