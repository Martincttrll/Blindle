<?php

namespace App\Http\Controllers;

use App\Events\guessAnswer;
use App\Events\startGame;
use App\Events\throwRandomSong;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\Song;
use App\Http\Controllers\GroupController;


class GameController extends Controller
{

    public function startGame($groupToken)
    {
        try {
            startGame::dispatch($groupToken);
            $this->getRandomTrack($groupToken);
            return response()->json(["message" => "Evenement envoyé."], 200);
        } catch (\Throwable $th) {

            return response()->json(["message" => "Erreur : " . $th], 500);
        }
    }


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


        $song = response()->json(['song' => $song->setVisible(["id", "previewUrl"]), 'from' => $user->setVisible(["id", "name"])], 200);
        throwRandomSong::dispatch($token, $song);
        return $song;
    }



    public function setWinner(Request $request)
    {
        $validatedData = $request->validate([
            'group_id' => 'int',
            'winner_id' => 'int',
        ]);



        $group = Group::find($validatedData['group_id']);
        $user = User::find($validatedData['winner_id']);

        if ($group && empty($group->winner)) {
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
            'group_token' => 'string',
            'title_points' => 'int',
            'artist_points' => 'int',
            'song_id' => 'int',
            'alreadyGuess' => 'array'
        ]);

        $song = Song::find($validatedData['song_id']);
        $user = User::find($validatedData['player_id']);

        $userResponse = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validatedData["input"]));
        $titleAnswer = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($song->title));
        $artistsAnswer = array_map('trim', explode(',', preg_replace('/[^a-zA-Z0-9,]/', '', strtolower($song->artist))));

        $tiltePoints = intval(($validatedData["title_points"] - 15 * $validatedData["title_points"] / 100));
        $artistPoints = intval(($validatedData["artist_points"] - 15 * $validatedData["artist_points"] / 100));

        if ($userResponse === $titleAnswer && !in_array("title", $validatedData["alreadyGuess"])) {
            $datas = response()->json(["message" => $user->name . " à trouvé le titre !", "guess" => "title", "player" => $user->id, "title_points" => $tiltePoints, "user_points" => intval($validatedData["title_points"])], 200);
            guessAnswer::dispatch($user->id, $validatedData["group_token"], $datas);
            return $datas;
        } elseif (in_array($userResponse, $artistsAnswer) && !in_array("artist", $validatedData["alreadyGuess"])) {
            $datas = response()->json(["message" => $user->name . " à trouvé l'artiste !", "guess" => "artist", "player" => $user->id, "artist_points" => $artistPoints, "user_points" => intval($validatedData["artist_points"])], 200);
            guessAnswer::dispatch($user->id, $validatedData["group_token"], $datas);
            return $datas;
        } else {
            $datas = response()->json(["message" => $validatedData["input"], "player" => $user->id, "guess" => null], 200);
            guessAnswer::dispatch($user->id, $validatedData["group_token"], $datas);
            return $datas;
        }
    }
}
