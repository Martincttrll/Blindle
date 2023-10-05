<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\GroupController;


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
      Route::get('/{song}',[SongController::class,'show'])->name('get-song');
      Route::post('/',[SongController::class,'store'])->name('store-repository');
});

Route::group(['prefix' => '/group'],function (){
      Route::get('/',[GroupController::class,'index'])->name('get-groups');
      Route::get('/token/{token}', [GroupController::class, 'showFromToken'])->name('get-group-token');
      Route::get('/{group}',[GroupController::class,'show'])->name('get-group');
      Route::post('/', [GroupController::class,'store'])->name('strore-group');
      Route::put('/{group}', [GroupController::class,'update'])->name('update-group');
      Route::delete('/{group}', [GroupController::class,'destroy'])->name('delete-group');


      Route::post('/test/{token}', [GroupController::class,'join'])->name('test-group');
});

