<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use MoonShine\MoonShineRequest;
use MoonShine\Http\Controllers\MoonShineController;
use Symfony\Component\HttpFoundation\Response;
use App\Models\HistoriaClinica;
use App\Models\Paciente;


final class HistoriaClinicaController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): Response
    {
        // Obtener el ID del paciente desde la solicitud (por ejemplo, parámetro de la URL)
        $pacienteId = $request->get('paciente_id');

        // Buscar la historia clínica del paciente
        $historiaClinica = HistoriaClinica::where('paciente_id', $pacienteId)->first();

        // Si no se encuentra, retornar un mensaje de error
        if (!$historiaClinica) {
            return $this->toast('No se encontró la historia clínica del paciente.', 'error');
        }

        // Renderizar la vista con los datos de la historia clínica
        return $this->view('moonshine.historia_clinica.index', [
            'historiaClinica' => $historiaClinica, // Pasa los datos de la historia clínica a la vista
        ])->setLayout('custom_layout')->render();

        // Recuperar el paciente por ID
        $paciente = Paciente::findOrFail($paciente_id);

        // Pasar el paciente a la vista
        return $this->view('moonshine.historia_clinica.index', [
            'paciente' => $paciente
        ]);
    }
}
