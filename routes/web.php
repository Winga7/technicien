<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InterventionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TicketController;

use App\Models\User;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/users', function () {
    return Inertia::render('Users/Index', [
        'users' => User::all(),
    ]);
})->middleware(['auth']);

Route::middleware(['auth'])->group(function () {
    Route::resource('clients', ClientController::class);
    Route::resource('interventions', InterventionController::class);
    Route::post('interventions', [InterventionController::class, 'store'])->name('interventions.store');
});

Route::resource('tickets', TicketController::class)
    ->middleware(['auth:sanctum', 'verified']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');

    // Routes pour les ressources
    Route::resource('clients', ClientController::class);
    Route::resource('users', UserController::class);
});
