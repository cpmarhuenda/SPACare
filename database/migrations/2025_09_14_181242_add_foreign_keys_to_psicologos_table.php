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
        if (Schema::hasTable('psicologos')) {
            Schema::table('psicologos', function (Blueprint $table) {
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('psicologos')) {
            Schema::table('psicologos', function (Blueprint $table) {
                $table->dropForeign('psicologos_user_id_foreign');
            });
        }
    }
};
