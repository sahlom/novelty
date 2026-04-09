<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AreaController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return redirect()->route('tasks.dashboard');
});

Auth::routes();

Route::get('/dashboard', [TaskController::class, 'dashboard'])->name('tasks.dashboard')->middleware('auth');
Route::get('/monitor', [TaskController::class, 'monitor'])->name('tasks.monitor')->middleware('auth');
Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
Route::resource('clients', ClientController::class);
Route::resource('areas', AreaController::class);

// Tus rutas de tareas protegidas por login
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});
