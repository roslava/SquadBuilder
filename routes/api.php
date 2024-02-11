<?php

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

Route::post('player', [PlayerController::class, 'store']);
Route::put('player/{playerId}', [PlayerController::class, 'update']);
Route::middleware('check-static-bearer-token')->delete('player/{playerId}', [PlayerController::class, 'destroy']);
Route::get('player', [PlayerController::class, 'index']);
Route::post('team/process', [PlayerController::class, 'selectBestTeam']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/skills', [SkillController::class, 'store']);
});
