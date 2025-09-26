<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->json('role_priority')->nullable();
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        }

        // Insertar roles por defecto (si no existen)
        $roles = [
            ['name' => 'Super Admin',    'guard_name' => 'Moonshine', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paciente',       'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrativo', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Psicologo',      'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
  
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']],
                $role
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
