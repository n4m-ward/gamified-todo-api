<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum');

Route::get('/health-check', function () {
    return response()->json(null);
});
Route::post('/user', [UserController::class, 'createUser']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['user-auth'])->group(function() {
    Route::post('/task', [TaskController::class, 'createTask']);
});
