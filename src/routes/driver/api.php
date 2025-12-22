<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Driver\ApplicationController;
use Illuminate\Support\Facades\Route;
Route::prefix('v1')->prefix('driver')->group(function () {
    Route::post('login', [ApplicationController::class, 'driverLogin']);
    Route::post('login/confirm', [ApplicationController::class, 'driverLoginConfirm']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', [ApplicationController::class, 'driverLogout']);
        Route::get('steps/{step}', [ApplicationController::class, 'driverSteps']);
        Route::post('steps/{step}', [ApplicationController::class, 'driverStepsSubmit']);

    });

});

