<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Group;
use App\Models\Song;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_song', function (Blueprint $table) {
            $table->foreignIdFor(Song::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Group::class)->constrained()->onDelete('cascade');
            $table->primary(['song_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups_songs');
    }
};
