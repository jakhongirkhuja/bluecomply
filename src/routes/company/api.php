<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\LinkGeneratorController;
use App\Http\Controllers\Company\CompanyController;
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('driver-links',LinkGeneratorController::class);
});
