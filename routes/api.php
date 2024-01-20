<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// ini di groupkan karena ini isinya yang user gabisa lakukan kalau dia tidak login dulu
Route::middleware('auth:sanctum')->group(function(){
    Route::post('user',[UserController::class, 'fetch']);
    Route::post('user',[UserController::class, 'updateProfile']);
    Route::post('user/photo',[UserController::class, 'updatePhoto']);
    Route::post('logout',[UserController::class, 'logout']);
    

});

//sedangkan ini bisa langsung nembak (?)
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);


