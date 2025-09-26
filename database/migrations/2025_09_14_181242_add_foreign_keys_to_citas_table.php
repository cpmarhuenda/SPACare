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
        if (Schema::hasTable('citas')) {
            Schema::table('citas', function (Blueprint $table) {
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
        if (Schema::hasTable('citas')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->dropForeign('citas_paciente_id_foreign');
                $table->dropForeign('citas_psicologo_id_foreign');
            });
        }
    }
};
