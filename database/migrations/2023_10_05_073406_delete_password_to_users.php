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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password'); // Vous pouvez spécifier les détails de la colonne que vous supprimez ici
        });
    }
};
