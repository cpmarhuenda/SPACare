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
        if (Schema::hasTable('moonshine_users')) {
            Schema::table('moonshine_users', function (Blueprint $table) {
                $table->foreign(['moonshine_user_role_id'])->references(['id'])->on('moonshine_user_roles')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('moonshine_users')) {
            Schema::table('moonshine_users', function (Blueprint $table) {
                $table->dropForeign('moonshine_users_moonshine_user_role_id_foreign');
            });
        }
    }
};
