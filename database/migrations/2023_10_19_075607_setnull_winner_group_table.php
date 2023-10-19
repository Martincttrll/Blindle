<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign('groups_winner_foreign'); // Supprime la contrainte de clé étrangère existante
            $table->foreign('winner')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE') // Mettez à jour le comportement de mise à jour comme vous le souhaitez
                ->onDelete('SET NULL'); // Modifiez le comportement de suppression comme vous le souhaitez
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign('groups_winner_foreign');
            $table->foreign('winner')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
        });
    }
};
