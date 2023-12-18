<?php

use App\Http\Controllers\AchievementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpotifyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {

      Route::group(['prefix' => '/song'], function () {
            Route::get('/', [SongController::class, 'index'])->name('get-songs');
            Route::get('/{song}', [SongController::class, 'show'])->name('get-song');
            Route::post('/', [SongController::class, 'store'])->name('store-song');
      });

      Route::group(['prefix' => '/group'], function () {
            Route::get('/', [GroupController::class, 'index'])->name('get-groups');
            Route::get('/token/{token}', [GroupController::class, 'showFromToken'])->name('get-group-token');
            Route::get('/{group}', [GroupController::class, 'show'])->name('get-group');
            Route::post('/', [GroupController::class, 'store'])->name('strore-group');
            Route::put('/{group}', [GroupController::class, 'update'])->name('update-group');
            Route::delete('/{group}', [GroupController::class, 'destroy'])->name('delete-group');
            Route::get('/join/{token}', [GroupController::class, 'join'])->name('join-group');
      });

      Route::group(['prefix' => '/game'], function () {
            Route::get('/random-song/{token}', [GameController::class, 'getRandomTrack'])->name('get-random');
            Route::post('/set-winner', [GameController::class, 'setWinner'])->name('set-winner');
            Route::post('/try', [GameController::class, 'handleTry'])->name('handle-try');
      });

      Route::group(['prefix' => '/user'], function () {
            Route::get('/', [UserController::class, 'showCurrent'])->name('get-current');
            Route::get('/history/{user}', [UserController::class, 'getHistory'])->name('get-history');
            Route::get('/songs/{user}', [UserController::class, 'getSongs'])->name('get-user-songs');
            Route::get('/refresh/{user}', [UserController::class, 'refreshLikedTracks'])->name('refresh-songs');
      });
      Route::group(['prefix' => '/achievement'], function () {
            Route::get('/', [AchievementController::class, 'showFromUser'])->name('get-user-achievements');
            Route::post('/attach/{achievement}', [AchievementController::class, 'attachToUser'])->name('attach-user-achievements');
      });


      Route::get('/logout', [AuthController::class, 'logOut'])->name('logout');
});
