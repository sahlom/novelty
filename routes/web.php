<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController; // <--- ESTA LÍNEA ES VITAL
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

// Tus rutas de tareas protegidas por login
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});
