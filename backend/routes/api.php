<?php

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('jwt.verify')->get('/user', [ LoginController::class, 'getAuthenticatedUser' ]);

// Login Route
Route::post('register', [ RegisterController::class , 'register']);

// Register Route
Route::post('login', [ LoginController::class , 'authenticate']);


// Issues Routes

Route::middleware('jwt.verify')->group(function () {
    
    Route::get('/issue/{issue}', [ IssueController::class, 'show' ]);

    Route::post('issue/{issue}/join',[ IssueController::class, 'join' ]);
    
    Route::post('issue/{issue}/vote',[ IssueController::class, 'vote' ]);

});
