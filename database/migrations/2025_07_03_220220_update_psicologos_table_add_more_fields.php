<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('psicologos', function (Blueprint $table) {
            // Cambiar tipo de name y email si actualmente son TEXT
            $table->string('name')->change();
            $table->string('email')->change();

            // Nuevos campos
            $table->string('apellidos')->after('name');
            $table->string('fotografia')->nullable()->after('email');
            $table->string('numero_colegiado', 50)->nullable()->after('fotografia');
            $table->string('especialidad', 100)->nullable()->after('numero_colegiado');
            $table->text('formacion')->nullable()->after('especialidad');
        });
    }

    public function down(): void
    {
        Schema::table('psicologos', function (Blueprint $table) {
            $table->text('name')->change();
            $table->text('email')->change();

            $table->dropColumn([
                'apellidos',
                'fotografia',
                'numero_colegiado',
                'especialidad',
                'formacion'
            ]);
        });
    }
};
