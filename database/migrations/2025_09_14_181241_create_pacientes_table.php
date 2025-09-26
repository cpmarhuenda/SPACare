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
        if (!Schema::hasTable('pacientes')) {
            Schema::create('pacientes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->text('name');
                $table->string('telefono', 15)->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->string('genero')->nullable();
                $table->string('fotografia')->nullable();
                $table->text('direccion')->nullable();
                $table->date('falta')->nullable()->useCurrent();
                $table->boolean('active')->default(true);
                $table->unsignedBigInteger('user_id')->index();
                $table->text('historial_medico')->nullable();
                $table->text('estado_salud_actual')->nullable();
                $table->date('fecha_primera_consulta')->nullable();
                $table->text('observaciones')->nullable();
                $table->string('_password_temp')->nullable();
                $table->string('_password_repeat_temp')->nullable();
                $table->unsignedBigInteger('psicologo_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
