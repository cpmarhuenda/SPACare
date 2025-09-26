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
        if (!Schema::hasTable('paciente_recurso')) {
            Schema::create('paciente_recurso', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('paciente_id')->index();
                $table->unsignedBigInteger('recurso_id')->index();
                $table->timestamps();
                $table->boolean('descargado')->default(false);
                $table->timestamp('fecha_descarga')->nullable();
                $table->boolean('active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente_recurso');
    }
};
