<?php

use App\Http\Controllers\Admin\PlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;

Route::prefix('v1/admin')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum
    Route::apiResource('plans', PlanController::class);
    Route::apiResource('companies', CompanyController::class);

});
