<?php

use App\Http\Controllers\Admin\PlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Admin\AdminController;
Route::prefix('v1/admin')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum
    Route::apiResource('plans', PlanController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::post('companies/{companies}/add-user', [CompanyController::class, 'addUser']);
    Route::put('companies/{companies}/edit-user/{id}', [CompanyController::class, 'editUser']);
    Route::delete('companies/{companies}/delete-user/{id}', [CompanyController::class, 'deleteUser']);

    Route::get('analytics', [AdminController::class, 'analytics']);


});
