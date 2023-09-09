<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'verified')->group(function () {
    Route::resource('tasks', TaskController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::patch('/boards/enqueueTodayTask/{task}', [BoardController::class, 'enqueueTodayTask'])->name('boards.enqueueTodayTask');
    Route::patch('/boards/dequeueTodayTask/{task}', [BoardController::class, 'dequeueTodayTask'])->name('boards.dequeueTodayTask');
    Route::patch('/boards/putInProgressTask/{task}', [BoardController::class, 'putInProgressTask'])->name('boards.putInProgressTask');
    Route::patch('/boards/putOnHoldTask/{task}', [BoardController::class, 'putOnHoldTask'])->name('boards.putOnHoldTask');
    Route::patch('/boards/putCompletedTask/{task}', [BoardController::class, 'putCompletedTask'])->name('boards.putCompletedTask');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
});
require __DIR__.'/auth.php';
