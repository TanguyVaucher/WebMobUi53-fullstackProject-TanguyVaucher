<?php

use App\Http\Controllers\Api\v1\ApiPostController;
use App\Http\Controllers\Api\v1\ApiFooController;
use App\Http\Controllers\Api\v1\ApiPollController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('v1/posts', ApiPostController::class)
    ->middlewareFor(['index', 'show'], ['auth:sanctum', 'abilities:posts:read'])
    ->middlewareFor(['store'], ['auth:sanctum', 'abilities:posts:create'])
    ->middlewareFor(['update'], ['auth:sanctum', 'abilities:posts:update'])
    ->middlewareFor(['destroy'], ['auth:sanctum', 'abilities:posts:delete']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/v1/foo', [ApiFooController::class, 'show']);
    Route::post('/v1/foo', [ApiFooController::class, 'store']);
});

// Routes polls — IMPORTANT : déclarer /token/{token} AVANT /{poll}
// pour éviter que Laravel ne tente de résoudre "token" comme un Poll
Route::prefix('v1/polls')->group(function () {

    // Routes publiques via token (auth optionnelle pour détecter le user)
    Route::get('/token/{token}', [ApiPollController::class, 'showByToken']);
    Route::get('/token/{token}/results', [ApiPollController::class, 'results']);

    // Vote : authentification requise
    Route::post('/token/{token}/vote', [ApiPollController::class, 'vote'])
        ->middleware('auth:sanctum');

    // CRUD propriétaire : authentification requise
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [ApiPollController::class, 'index']);
        Route::post('/', [ApiPollController::class, 'store']);
        Route::get('/{poll}', [ApiPollController::class, 'show']);
        Route::patch('/{poll}', [ApiPollController::class, 'update']);
        Route::delete('/{poll}', [ApiPollController::class, 'destroy']);
    });
});
