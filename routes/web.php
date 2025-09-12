<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResetPasswordPacienteController;
use App\Models\PacienteRecurso;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ResumenClinicoController;

use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/pacientes/reset-password/{id}', [ResetPasswordPacienteController::class, 'showForm']);
Route::post('/pacientes/reset-password/{id}', [ResetPasswordPacienteController::class, 'update']);
//Route::get('/calendario', [\App\Http\Controllers\CalendarioController::class, 'index']);

Route::get('/calendario', [App\Http\Controllers\CalendarioController::class, 'index'])
    ->middleware(['web', 'moonshine.auth'])
    ->name('moonshine.calendario');

    //para documentar. esto lo ponemos para gestionar la descarga confirmada

Route::get('/descargar-recurso/{id}', function ($id) {
    $pr = PacienteRecurso::findOrFail($id);
    
    // Solo el paciente asignado puede descargarlo
    if (auth()->id() !== $pr->paciente->user_id) {
        abort(403);
    }

    // Marcar como descargado si no lo está
    if (!$pr->descargado) {
        $pr->update([
            'descargado' => true,
            'fecha_descarga' => now(),
        ]);
    }

   //return Storage::download($pr->recurso->archivo);
   return Storage::disk('public')->download($pr->recurso->archivo);

})->middleware(['web', 'auth'])->name('descargar.recurso');


//para el boton resumen paciente

Route::get('/resumen-clinico/{paciente}', [ResumenClinicoController::class, 'show'])
    ->middleware(['web', 'auth'])
    ->name('resumen.clinico');
/*
Route::get('/test-calendario', function () {
    $citas = \App\Models\Cita::all()->map(function ($cita) {
        return [
            'title' => 'Cita test',
            'start' => $cita->fecha_hora,
            'end' => \Carbon\Carbon::parse($cita->fecha_hora)->addMinutes(60)->toDateTimeString(),
        ];
});

    return view('moonshine.pages.calendario', ['citas' => $citas]);
   
});*/


Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email')
    );
 
    return $status === Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware(['guest'])
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware(['guest'])
    ->name('password.update');

Route::get('/_test_profile', function () {
    // Esto construye la URL real de tu página "profile"
    return redirect()->to(route('moonshine.page', 'profile'));
});