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
        if (!Schema::hasTable('psicologos')) {
            Schema::create('psicologos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->string('name');
                $table->string('fotografia')->nullable();
                $table->string('numero_colegiado', 50)->nullable();
                $table->string('especialidad', 100)->nullable();
                $table->text('formacion')->nullable();
                $table->date('falta')->default('CURRENT_DATE');
                $table->boolean('active')->default(true);
                $table->unsignedBigInteger('user_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psicologos');
    }
};
