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
        if (!Schema::hasTable('mensajes')) {
            Schema::create('mensajes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('remitente_id')->index();
                $table->unsignedBigInteger('destinatario_id')->index();
                $table->text('contenido');
                $table->unsignedBigInteger('responde_a')->nullable()->index();
                $table->boolean('leido')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
