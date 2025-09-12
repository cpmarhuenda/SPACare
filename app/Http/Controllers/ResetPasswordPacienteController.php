<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class ResetPasswordPacienteController extends Controller
{
    public function showForm($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('admin.pacientes.reset-password', compact('paciente'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8|same:password_confirmation',
            'password_confirmation' => 'required',
        ]);

        $paciente = Paciente::findOrFail($id);
        if ($paciente->user) {
            $paciente->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        return redirect('/admin/resource/paciente-resource/index-page')->with('success', 'Contraseña actualizada');
    }
}
