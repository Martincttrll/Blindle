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
        ////ENLEVER STATELESS() QUAND EN PROD, C BIEN POUR DEV
        $spotifyUser = Socialite::driver('spotify')->stateless()->user();
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
            // foreach ($likedTracks as $trackData) {

            //     $trackName = $trackData['track']['name'];
            //     $artistNames = implode(', ', array_column($trackData['track']['artists'], 'name'));

            //     Song::create([

            //         'title' => $trackName,
            //         'artist' => $artistNames,
            //         'idSpotify' => $artistNames,
            //         'previewUrl' => $artistNames,
            //     ]);
            // }
            if (is_array($result) && isset($result['songs'])) {
                return response()->json(['songs' => $result['songs']], 200);
            } elseif (is_array($result) && isset($result['error'])) {
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
