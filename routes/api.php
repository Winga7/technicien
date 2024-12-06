<?php

use App\Http\Controllers\InterventionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/interventions/{intervention}/statut', [InterventionController::class, 'changeStatut']);
});
