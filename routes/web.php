<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => '/auth'], function () {
      Route::get('/redirect', [AuthController::class, 'authSpotify'])->name('auth-spotify');
      Route::get('/callback', [AuthController::class, 'callbackSpotify'])->name('callbackSpotify');
});
