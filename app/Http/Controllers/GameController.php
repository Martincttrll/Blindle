<?php

namespace App\Http\Controllers;

use App\Events\guessAnswer;
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

        $songs = $user->songs;

        if (count($songs) === 0) {
            return response()->json(['message' => "L'utilisateur n'a aucune musique associée."], 400);
        }

        $songIndex = rand(0, count($songs) - 1);
        $song = $songs[$songIndex];

        $isAttached = $group->songs()->where('song_id', $song->id)->exists();

        if (!$isAttached) {
            $group->songs()->attach($song->id);
        }
        return response()->json(['song' => $song->setVisible(["id", "previewUrl"]), 'from' => $user->setVisible(["id", "name"])], 200);
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
            'group_token' => 'int',
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
            $datas = response()->json(["message" => $user->name . " à trouvé le titre !", "guess" => "title", "player" => $user->id, "title_points" => $tiltePoints, "user_points" => intval($validatedData["title_points"])], 200);
            guessAnswer::dispatch($user->id, $validatedData["group_token"], $datas);
        } else if (preg_replace('/[^a-zA-Z0-9]/', '', strtolower($userResponse)) === preg_replace('/[^a-zA-Z0-9]/', '', strtolower($artistAnswer))) {
            $datas = response()->json(["message" => $user->name . " à trouvé l'artiste !", "guess" => "artist", "player" => $user->id, "artist_points" => $artistPoints, "user_points" => intval($validatedData["artist_points"])], 200);
            guessAnswer::dispatch($user->id, $validatedData["group_token"], $datas);
        } else {
            $datas = response()->json(["message" => "Mauvaise réponse.", "guess" => null], 200);
            guessAnswer::dispatch($user->id, $validatedData["group_token"], $datas);
        }
    }
}
