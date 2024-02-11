<?php

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

// Create Player Endpoint
Route::post('player', [PlayerController::class, 'store']);

// Update Player Endpoint
Route::put('player/{playerId}', [PlayerController::class, 'update']);

// Delete Player Endpoint
Route::middleware('check-static-bearer-token')->delete('player/{playerId}', [PlayerController::class, 'destroy']);

// List Players Endpoint
Route::get('player', [PlayerController::class, 'index']);

// Select Best Team Endpoint
Route::post('team/process', [PlayerController::class, 'selectBestTeam']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/skills', [SkillController::class, 'store']);
});
