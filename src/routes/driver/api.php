<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Driver\ApplicationController;
use Illuminate\Support\Facades\Route;
Route::prefix('v1/driver')->group(function () {
    Route::post('login', [ApplicationController::class, 'driverLogin']);
    Route::post('login/confirm', [ApplicationController::class, 'driverLoginConfirm']);
    Route::middleware(['auth:sanctum'])->prefix('cabinet')->group(function () {
        Route::get('logout', [ApplicationController::class, 'driverLogout']);
        Route::get('status', [ApplicationController::class, 'applicationStatus']);
        Route::get('details', [ApplicationController::class, 'applicationDetails']);
        Route::post('details', [ApplicationController::class, 'applicationDetailPost']);;
    });

});

