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
        if (!Schema::hasTable('recursos')) {
            Schema::create('recursos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('titulo');
                $table->date('fecha');
                $table->string('archivo');
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->unsignedBigInteger('categoria_id')->nullable()->default(1)->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recursos');
    }
};
