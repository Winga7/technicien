<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/storage/{path}', function ($path) {
        return response()->file(storage_path('app/public/' . $path));
    })->where('path', '.*');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('tickets', TicketController::class);
    Route::resource('interventions', InterventionController::class);
});
