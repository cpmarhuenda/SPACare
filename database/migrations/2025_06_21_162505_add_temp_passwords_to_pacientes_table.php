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
    Schema::table('pacientes', function (Blueprint $table) {
        $table->string('password_temp')->nullable();
        $table->string('password_repeat_temp')->nullable();
    });
}

public function down(): void
{
    Schema::table('pacientes', function (Blueprint $table) {
        $table->dropColumn(['password_temp', 'password_repeat_temp']);
    });
}
};
