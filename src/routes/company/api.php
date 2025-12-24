<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\LinkGeneratorController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Driver\DriverTagController;
use App\Http\Controllers\Company\CompanyDriverController;
use App\Http\Controllers\Company\DrugTestController;
Route::prefix('v1/company')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('driver-links',LinkGeneratorController::class);
    Route::apiResource('driver-tags', DriverTagController::class)->only(['store', 'update', 'destroy' ]);
    Route::apiResource('drug-tests', DrugTestController::class)->only(['store', 'update', 'destroy']);


    Route::get('drivers', [CompanyDriverController::class, 'getDrivers']);
});
