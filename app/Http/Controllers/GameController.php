<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Http\Controllers\GroupController;


class GameController extends Controller
{
    public function getRandomTrack($token)
    {

        $group = GroupController::showFromToken($token);

        $users = $group->users;
        $userIndex = rand(0, count($users) - 1);
        $user = $users[$userIndex];

        $songs = $user->songs->makeHidden(['songs']);
        $songIndex = rand(0, count($songs) - 1);
        $song = $songs[$songIndex];

        return response()->json(['song' => $song, 'from' => $user->setVisible(["id", "name"])], 200);
    }


    public function setWinner(Request $request)
    {
        $validatedData = $request->validate([
            'group_id' => 'int',
            'winner_id' => 'int',
        ]);



        $group = Group::find($validatedData['group_id']);

        if ($group) {
            $group->winner = $validatedData['winner_id'];
            $group->save();
        } else {
            return response()->json(['message' => 'Groupe introuvable.'], 400);
        }

        return response()->json(['message' => 'Gagnant mis à jour avec succès'], 200);
    }
}
