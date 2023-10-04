<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

	Route::group(['prefix' => '/song'],function (){
      Route::get('/',[SongController::class,'index'])->name('get-songs');
    //   Route::get('/{repository}',[RepositoryController::class,'show'])->name('get-repository');
    //   Route::post('/',[RepositoryController::class,'store'])->name('store-repository');
    //   Route::delete('/{repository}',[RepositoryController::class,'destroy'])->name('delete-repository');
});