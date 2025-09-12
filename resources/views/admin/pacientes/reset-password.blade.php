@extends('moonshine::layouts.app')

@section('content')
    <h1>Resetear contraseña de {{ $paciente->name }}</h1>

    <form method="POST" action="{{ url("/admin/pacientes/reset-password/{$paciente->id}") }}">
        @csrf
        <div>
            <label>Nueva contraseña</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Repetir contraseña</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit">Actualizar</button>
    </form>
@endsection
