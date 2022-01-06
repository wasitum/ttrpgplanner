<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PageController, GameController};


Route::get('/', function () {
    return redirect()->route('login');
    /* return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]); */
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::match(['get', 'post'], '/new-game', [PageController::class, 'newGame'])->name('newGame');
    Route::match(['get', 'post'], '/game/{game}', [PageController::class, 'editGame'])->name('editGame');
    Route::post('/game/{game}/signup', [PageController::class, 'signup'])->name('signupGame');
    Route::post('/game/{game}/signout', [PageController::class, 'signout'])->name('signoutGame');
    Route::post('/date/{game}', [PageController::class, 'addDate'])->name('addDate');
    Route::post('/toggledate/{date}', [PageController::class, 'toggleDate'])->name('toggleDate');
});
