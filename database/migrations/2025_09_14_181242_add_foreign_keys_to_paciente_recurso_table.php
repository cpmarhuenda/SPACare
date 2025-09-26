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
        if (Schema::hasTable('paciente_recurso')) {
            Schema::table('paciente_recurso', function (Blueprint $table) {
                $table->foreign(['paciente_id'])->references(['id'])->on('pacientes')->onUpdate('restrict')->onDelete('cascade');
                $table->foreign(['recurso_id'])->references(['id'])->on('recursos')->onUpdate('restrict')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('paciente_recurso')) {
            Schema::table('paciente_recurso', function (Blueprint $table) {
                $table->dropForeign('paciente_recurso_paciente_id_foreign');
                $table->dropForeign('paciente_recurso_recurso_id_foreign');
            });
        }
    }
};
