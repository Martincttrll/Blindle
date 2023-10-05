<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
 ///////AuthO : Spotify
Route::get('/auth/redirect', function () {
    return Socialite::driver('spotify')->redirect();
});
 
Route::get('/auth/callback', function () {
    $spotifyUser = Socialite::driver('spotify')->stateless()->user();

    // dd($spotifyUser);
    // $user->token
    $user = User::updateOrCreate([
        'idSpotify' => $spotifyUser->id,
    ], [
        'name' => $spotifyUser->name,
        'email' => $spotifyUser->email,
        'avatar' => $spotifyUser->avatar,
    ]);
});