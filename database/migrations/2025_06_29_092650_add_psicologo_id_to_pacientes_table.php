<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 /*  public function up()
{
  //  Schema::table('pacientes', function (Blueprint $table) {
   //     $table->foreignId('psicologo_id')->constrained('psicologos')->onDelete('cascade')->after('user_id');
    });
}
*/
public function down()
{
   /* Schema::table('pacientes', function (Blueprint $table) {
        $table->dropForeign(['psicologo_id']);
        $table->dropColumn('psicologo_id');
    });*/
}
};
