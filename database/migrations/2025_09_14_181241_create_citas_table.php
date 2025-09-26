<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('citas')) {
            Schema::create('citas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('paciente_id')->index();
                $table->unsignedBigInteger('psicologo_id')->index();
                $table->dateTime('fecha_hora');
                $table->time('duracion')->default('01:00:00');
                $table->enum('tipo', ['puntual', 'recurrente'])->default('puntual');
                $table->string('enlace_videollamada')->nullable();
                $table->dateTime('fecha_inicio')->nullable();
                $table->dateTime('fecha_fin')->nullable();
                $table->integer('periodicidad')->nullable();
                $table->timestamps();
                $table->string('hora_recurrente')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
