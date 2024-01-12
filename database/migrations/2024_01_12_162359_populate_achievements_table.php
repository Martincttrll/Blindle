<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class PopulateAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insertion des données
        DB::table('achievements')->insert([
            ['id' => 1, 'name' => 'Première Victoire', 'description' => 'Gagne ta première partie'],
            ['id' => 2, 'name' => 'Dominateur', 'description' => 'Gagne en ayant 300pts de plus que le 2ème'],
            ['id' => 3, 'name' => 'Créateur', 'description' => 'Crée un groupe et invite un ami'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer les données lors de la réversion
        DB::table('achievements')->truncate();
    }
}
