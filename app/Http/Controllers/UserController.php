<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Song;

class UserController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'string|max:255',
            'avatar' => 'string',
            'idSpotify' => 'string'
        ]);


        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user;
    }
    /**
     * Display the specified resource of current user.
     */
    public function showCurrent()
    {
        return Auth::user();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!$user) {
            return response()->json(['message' => 'user non trouvé'], 404);
        }

        // Valider les données de la requête
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'nbWin' => 'int|max:11',
            'nbGame' => 'int|max:11',
            'email' => 'string|max:255',
        ]);

        // Mettre à jour les données du user
        $user->update($validatedData);

        return response()->json(['message' => 'user mis à jour avec succès'], 200);
    }

    public function getHistory(User $user)
    {
        $history = $user->winningGroup()->latest()->take(10)->get();

        return response()->json($history);
    }


    public function getSongs(User $user)
    {
        return response()->json($user->songs()->orderBy('id', 'desc')->get());
    }

    public static function refreshLikedTracks($userId, $maxRecursion = 3)
    {
        if ($maxRecursion <= 0) {
            return response()->json(['error' => 'Limite d\'appels récursifs atteinte.'], 500);
        }
        $user = User::find($userId);

        if ($user) {
            $songs = SpotifyController::retrieveLikedTracks($user);

            // return $songs;
            if ($songs) {
                foreach ($songs as $trackData) {
                    try {
                        $idSpotify = $trackData['track']['id'];
                        $existingSong = Song::where('idSpotify', $idSpotify)->first();

                        if (!$existingSong) {
                            $artistNames = implode(', ', array_column($trackData['track']['artists'], 'name'));
                            $song = Song::updateOrCreate([
                                'title' => $trackData['track']['name'],
                                'artist' => $artistNames,
                                'idSpotify' => $trackData['track']['id'],
                                'previewUrl' => $trackData['track']['preview_url'],
                            ]);
                            $isAlreadyAttached = $song->users()->wherePivot('user_id', $user->id)->exists();
                            if (!$isAlreadyAttached) {
                                $song->users()->attach($user->id);
                            }
                        }
                    } catch (\Throwable $e) {
                        dd($e);
                        return UserController::refreshLikedTracks($user->id, $maxRecursion - 1);
                    }
                }
            } else {
                return response()->json(['message' => 'La récupération des likes a échouée.', 'songs' => $songs], 500);
            }
            return response()->json(['message' => 'Les likes de l\'utilisateurs ont bien été syncronisé.'], 200);
        } else {
            return response()->json(['message' => 'Aucun utilisateur a été trouvé.'], 400);
        }
    }
}
