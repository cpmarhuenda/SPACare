<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Clínico - SPACare UNED</title>
    <link rel="stylesheet" href="{{ asset('vendor/moonshine/assets/main.css') }}">
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
            color: #065f46;
        }

        .form-subtitle {
            font-size: 0.875rem;
            text-align: center;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .logo {
            display: block;
            margin: 0 auto 1rem;
            height: 60px;
        }

        .info-item {
            margin-bottom: 1rem;
            font-size: 0.95rem;
            color: #374151;
        }

        .info-item span {
            font-weight: 600;
            color: #111827;
        }

        .notes {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 1rem;
        }

        .volver-link {
            display: inline-block;
            margin-top: 2rem;
            background-color: #065f46;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            text-align: center;
        }

        .volver-link:hover {
            background-color: #047857;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <img src="{{ asset('images/Logo_SPACare.png') }}" alt="Logo SPACare" class="logo">
        <h1 class="form-title">🩺 Resumen Clínico</h1>
        <p class="form-subtitle">Pilar Fulleda Martínez</p>

        <div class="info-item">
            <span>📌 Estado actual:</span> Evolución positiva
        </div>
        <div class="info-item">
            <span>📅 Última cita:</span> 08-07-2025 18:30
        </div>
        <div class="info-item">
            <span>🗓 Próxima cita:</span> 22-07-2025 17:00
        </div>
        <div class="info-item">
            <span>📂 Recursos asignados:</span> 4
        </div>

        <div class="notes">
            <p><strong>📝 Notas del psicólogo:</strong></p>
            <p>
                La paciente continúa mostrando avances notables en la gestión de su ansiedad. Durante la última sesión, refirió haber afrontado con éxito dos situaciones sociales que previamente evitaba.
                Aplica con regularidad ejercicios de respiración y ha integrado un diario de pensamientos con seguimiento diario.
                Se refuerza el trabajo en prevención de recaídas. Se mantiene la pauta terapéutica actual.
            </p>
        </div>

        <a href="{{ url()->previous() }}" class="volver-link">⬅️ Volver</a>
    </div>
</body>
</html>
