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
        if (!Schema::hasTable('historias_clinicas')) {
            Schema::create('historias_clinicas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('paciente_id')->index();
                $table->unsignedBigInteger('psicologo_id')->index();
                $table->date('fecha');
                $table->text('diagnostico')->nullable();
                $table->text('tratamiento')->nullable();
                $table->text('notas_psicologo')->nullable();
                $table->text('antecedentes_medicos')->nullable();
                $table->text('medicacion_actual')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
