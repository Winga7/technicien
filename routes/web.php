<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\TicketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Debut

Route::get('/users', function () {
    return Inertia::render('Users/Index', [
        'users' => User::all(),
    ]);
})->middleware(['auth']);

Route::middleware(['auth'])->group(function () {
    Route::post('interventions', [InterventionController::class, 'store'])->name('interventions.store');
});

Route::resource('tickets', TicketController::class)
    ->middleware(['auth:sanctum', 'verified']);


// Fin


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/storage/{path}', function ($path) {
        return response()->file(storage_path('app/public/' . $path));
    })->where('path', '.*');
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('tickets', TicketController::class);
    Route::resource('interventions', InterventionController::class);
});
