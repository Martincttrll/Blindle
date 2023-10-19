<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\Song;
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

        return response()->json(['song_id' => $song->setVisible(["id", "previewUrl"]), 'from' => $user->setVisible(["id", "name"])], 200);
    }


    public function setWinner(Request $request)
    {
        $validatedData = $request->validate([
            'group_id' => 'int',
            'winner_id' => 'int',
        ]);



        $group = Group::find($validatedData['group_id']);
        $user = User::find($validatedData['winner_id']);

        if ($group) {
            $group->winner = $validatedData['winner_id'];
            $group->save();


            foreach ($group->users as $user) {
                $user->nbGame++;
                $user->save();
            }

            $user->nbWin++;
            $user->save();
        } else {
            return response()->json(['message' => 'Groupe introuvable.'], 400);
        }

        return response()->json(['message' => 'Gagnant mis à jour avec succès'], 200);
    }

    public function handleTry(Request $request)
    {
        $validatedData = $request->validate([
            'input' => 'string',
            'player_id' => 'int',
            'title_points' => 'int',
            'artist_points' => 'int',
            'song_id' => 'int'
        ]);

        $song = Song::find($validatedData['song_id']);
        $user = User::find($validatedData['player_id']);

        $userResponse = $validatedData["input"];
        $titleAnswer = $song->title;
        $artistAnswer = $song->artist;

        $tiltePoints = ($validatedData["title_points"] - 15 * $validatedData["title_points"] / 100);
        $artistPoints = ($validatedData["artist_points"] - 15 * $validatedData["artist_points"] / 100);


        if (preg_replace('/[^a-zA-Z0-9]/', '', strtolower($userResponse)) === preg_replace('/[^a-zA-Z0-9]/', '', strtolower($titleAnswer))) {
            // La réponse de l'utilisateur est correcte
            // Effectuez les actions appropriées (comptabiliser les points, etc.)
            return response()->json(["message" => $user->name . " à trouvé le titre !", "player" => $user->id, "title_points" => $tiltePoints, "user_points" => intval($validatedData["title_points"])], 200);
        } else if (preg_replace('/[^a-zA-Z0-9]/', '', strtolower($userResponse)) === preg_replace('/[^a-zA-Z0-9]/', '', strtolower($artistAnswer))) {
            // La réponse de l'utilisateur est incorrecte
            // Donnez une rétroaction à l'utilisateur
            return response()->json(["message" => $user->name . " à trouvé l'artiste !", "player" => $user->id, "artist_points" => $artistPoints, "user_points" => intval($validatedData["artist_points"])], 200);
        } else {
            return response()->json(["message" => "Mauvaise réponse."], 400);
        }
    }
}
