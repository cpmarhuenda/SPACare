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
        if (Schema::hasTable('mensajes')) {
            Schema::table('mensajes', function (Blueprint $table) {
                $table->foreign(['destinatario_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
                $table->foreign(['remitente_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
                $table->foreign(['responde_a'])->references(['id'])->on('mensajes')->onUpdate('restrict')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mensajes')) {
            Schema::table('mensajes', function (Blueprint $table) {
                $table->dropForeign('mensajes_destinatario_id_foreign');
                $table->dropForeign('mensajes_remitente_id_foreign');
                $table->dropForeign('mensajes_responde_a_foreign');
            });
        }
    }
};
