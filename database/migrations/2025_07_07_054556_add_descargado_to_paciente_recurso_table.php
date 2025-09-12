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
            Schema::table('paciente_recurso', function (Blueprint $table) {
        $table->boolean('descargado')->default(false);
        $table->timestamp('fecha_descarga')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
 Schema::table('paciente_recurso', function (Blueprint $table) {
        $table->dropColumn(['descargado', 'fecha_descarga']);            //
        });
    }
};
