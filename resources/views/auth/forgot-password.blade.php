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
            <h1 class="title">¿Olvidaste tu contraseña?</h1>
            <p class="description">Introduce tu correo electrónico y te enviaremos un enlace para restablecerla.</p>
        </div>

        {{-- Mensajes de estado/errores --}}
        @if (session('status'))
            <div class="bg-green-600 text-black p-3 rounded mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error text-sm mb-4">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="form-input w-full">
            </div>

            <button type="submit" class="btn btn-primary w-full">
                Enviar enlace de restablecimiento
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
