<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;

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

// 1. Ruta raíz
Route::get('/', function () {
    return redirect()->route('tasks.monitor');
});

// 2. Rutas de Autenticación (Login, Register, etc.)
Auth::routes(['register' => false]);

// 3. TODO lo que requiere estar logueado va aquí adentro
Route::middleware(['auth'])->group(function () {
    
    // Dashboards y Monitores
    Route::get('/monitor', [TaskController::class, 'monitor'])->name('tasks.monitor');
    Route::get('/dashboard', [TaskController::class, 'dashboard'])->name('tasks.dashboard');

    // Módulo de Tareas y Comentarios
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');

    // Módulo de Catálogos (Agrupados para orden visual)
    Route::resource('clients', ClientController::class);
    Route::resource('areas', AreaController::class);
    Route::resource('priorities', PriorityController::class);
    Route::resource('statuses', StatusController::class);

    // CRUD de Usuarios - Solo accesible si es admin
    Route::resource('users', UserController::class)->middleware('can:admin-only');
});
