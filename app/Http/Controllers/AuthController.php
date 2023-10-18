<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SpotifyController;

class AuthController extends Controller
{
    public function authSpotify()
    {
        return Socialite::driver('spotify')
            ->setScopes(['user-library-read', 'playlist-read-private'])
            ->redirect();
    }

    public function callbackSpotify()
    {
        $spotifyUser = Socialite::driver('spotify')->user();
        $user = User::updateOrCreate([
            'idSpotify' => $spotifyUser->id,
        ], [
            'name' => $spotifyUser->name,
            'email' => $spotifyUser->email,
            'avatar' => $spotifyUser->avatar,
            'spotifyAccessToken' => $spotifyUser->token,
            'spotifyRefreshToken' => $spotifyUser->refreshToken,
            'spotifyExpiresIn' => $spotifyUser->expiresIn
        ]);

        if ($user) {
            $result =  SpotifyController::retrieveLikedTracks($user);

            //Insert bdd
            foreach ($result as $trackData) {
                $artistNames = implode(', ', array_column($trackData['track']['artists'], 'name'));
                try {
                    $song = Song::create([

                        'title' => $trackData['track']['name'],
                        'artist' => $artistNames,
                        'idSpotify' => $trackData['track']['id'],
                        'previewUrl' => $trackData['track']['preview_url'],
                    ]);
                    $song->users()->attach($user->id);
                } catch (\PDOException $e) {
                    if ($e->errorInfo[1] === 1062) {
                        // Le code 1062 correspond à une violation de contrainte unique
                        $songId = Song::where('idSpotify', $trackData['track']['id'])->first()->id;
                        $isAlreadyAttached = $user->songs()->wherePivot('song_id', $songId)->exists();
                        if (!$isAlreadyAttached) {
                            $user->songs()->attach($songId);
                        }
                    }
                }
            }
            if (is_array($result) && count($result) > 1) {
                return response()->json(['songs' => $result], 200);
            } elseif (isset($result['error'])) {
                // Gestion de l'erreur renvoyée par retrieveLikedTracks
                return response()->json(['error' => $result['error']], 500);
            } else {
                return response()->json(['error' => 'Erreur inattendue', 'result' => $result], 500);
            }
        } else {
            return response()->json(['error' => 'Erreur lors de la création/mise à jour de l\'utilisateur'], 500);
        }
    }
}
