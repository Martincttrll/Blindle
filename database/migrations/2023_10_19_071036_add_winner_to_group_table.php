<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->unsignedBigInteger('winner')->nullable();
            $table->foreign('winner')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['winner']);
            $table->dropColumn('winner');
        });
    }
};
