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
        if (Schema::hasTable('historias_clinicas')) {
            Schema::table('historias_clinicas', function (Blueprint $table) {
                $table->foreign(['paciente_id'])->references(['id'])->on('pacientes')->onUpdate('restrict')->onDelete('restrict');
                $table->foreign(['psicologo_id'])->references(['id'])->on('psicologos')->onUpdate('restrict')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('historias_clinicas')) {
            Schema::table('historias_clinicas', function (Blueprint $table) {
                $table->dropForeign('historias_clinicas_paciente_id_foreign');
                $table->dropForeign('historias_clinicas_psicologo_id_foreign');
            });
        }
    }
};
