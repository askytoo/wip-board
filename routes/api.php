<?php

use App\Http\Controllers\BoardController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/boards/enqueueTodayTask/{task}', [BoardController::class, 'enqueueTodayTask'])->name('boards.enqueueTodayTask');
    Route::patch('/boards/dequeueTodayTask/{task}', [BoardController::class, 'dequeueTodayTask'])->name('boards.dequeueTodayTask');
    Route::patch('/boards/putInProgressTask/{task}', [BoardController::class, 'putInProgressTask'])->name('boards.putInProgressTask');
    Route::patch('/boards/putOnHoldTask/{task}', [BoardController::class, 'putOnHoldTask'])->name('boards.putOnHoldTask');
    Route::patch('/boards/putCompletedTask/{task}', [BoardController::class, 'putCompletedTask'])->name('boards.putCompletedTask');
});
