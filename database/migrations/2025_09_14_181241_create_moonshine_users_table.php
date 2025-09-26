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
        if (!Schema::hasTable('moonshine_users')) {
            Schema::create('moonshine_users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('moonshine_user_role_id')->default(1)->index();
                $table->string('email', 190)->unique();
                $table->string('password');
                $table->string('name');
                $table->string('avatar')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moonshine_users');
    }
};
