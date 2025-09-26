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
        if (Schema::hasTable('recursos')) {
            Schema::table('recursos', function (Blueprint $table) {
                $table->foreign(['categoria_id'])->references(['id'])->on('catrecursos')->onUpdate('restrict')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recursos')) {
            Schema::table('recursos', function (Blueprint $table) {
                $table->dropForeign('recursos_categoria_id_foreign');
            });
        }
    }
};
