<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;



class AuthController extends Controller
{
    public function authSpotify(){
        return Socialite::driver('spotify')->redirect();
    }

    public function callbackSpotify(){
        $spotifyUser = Socialite::driver('spotify')->stateless()->user();
        $user = User::updateOrCreate([
            'idSpotify' => $spotifyUser->id,
        ], [
            'name' => $spotifyUser->name,
            'email' => $spotifyUser->email,
            'avatar' => $spotifyUser->avatar,
        ]);
        return response()->json(['token' => $user->createToken("API TOKEN")->plainTextToken], 200);    
    } 
}
