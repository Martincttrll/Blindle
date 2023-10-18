<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;



class SpotifyController extends Controller
{
    public static function refreshToken($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            // Gérez le cas où l'utilisateur n'est pas trouvé
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }
        $response = Http::asForm()->withHeaders([
            'Authorization' => 'Basic ' . base64_encode(env("SPOTIFY_CLIENT_ID") . ':' . env("SPOTIFY_CLIENT_SECRET")),
        ])->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->spotifyRefreshToken,
        ]);
        if ($response->successful()) {
            $newAccessToken = $response->json()['access_token'];

            $user->update(['spotifyAccessToken' => $newAccessToken]);
            return $newAccessToken;
        } else {
            return response()->json(['error' => 'Erreur lors de la régénération du jeton d\'accès'], $response->status());
        }
    }


    public static function retrieveLikedTracks($user, $maxRecursion = 3)
    {
        if ($user) {
            if ($maxRecursion <= 0) {
                return response()->json(['error' => 'Limite d\'appels récursifs atteinte.'], 500);
            }

            $userId = $user->id;
            $token = $user->spotifyAccessToken;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->get('https://api.spotify.com/v1/me/tracks');

            if ($response->successful()) {
                $likedTracks = $response->json()['items'];

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

                return $likedTracks;
            } else if ($response->status() === 403) {
                $responseData = $response->json();
                if (isset($responseData['error']) && isset($responseData['error']['message']) && $responseData['error']['message'] === "Forbidden") {
                    $newToken = SpotifyController::refreshToken($userId);

                    return SpotifyController::retrieveLikedTracks($newToken, $maxRecursion - 1);
                }
            } else {
                return response()->json(['error' => $response], 500);
            }
        } else {
            return response()->json(['error' => 'Utilisateur vide.'], 500);
        }
    }
}
