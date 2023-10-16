<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    public function authSpotify()
    {
        return Socialite::driver('spotify')->redirect();
    }

    public function callbackSpotify()
    {
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

        /////A TESTER

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $spotifyUser->token,
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


            return response()->json(['token' => $user->createToken("API TOKEN")->plainTextToken, 'songs' => $likedTracks], 200);
        } else {
            return $response;
        }
    }
}
