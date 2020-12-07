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
Route::post('register', [ RegisterController::class , 'register'])->name('api.register');

// Register Route
Route::post('login', [ LoginController::class , 'authenticate'])->name('api.authenticate');


// Issues Routes

Route::middleware('jwt.verify')->group(function () {
    
    Route::get('/issue/{issue}', [ IssueController::class, 'show' ])->name('api.show');

    Route::post('issue/{issue}/join',[ IssueController::class, 'join' ])->name('api.join');
    
    Route::post('issue/{issue}/vote',[ IssueController::class, 'vote' ])->name('api.vote');
    
    Route::post('issue/{issue}/end-vote',[ IssueController::class, 'endVote' ])->name('api.end.vote');

});
