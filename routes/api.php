<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\SetUserInteractionMiddleware;
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

Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::group(['middleware' => [SetUserInteractionMiddleware::class, 'auth:sanctum']], function (){
    Route::get('/user', [LoginController::class, 'userDetails']);
    Route::get('/onlines', [ChatController::class, 'onlines']);
    Route::get('/chats', [ChatController::class, 'chats']);
    Route::post('/chats', [ChatController::class, 'sendChat'])->name('sendChat');
    Route::post('/update', [ChatController::class, 'updateUser']);
    Route::get('/logout', [ChatController::class, 'logout']);
    Route::post('/dalateChat', [ChatController::class, 'deleteChat']);
});
