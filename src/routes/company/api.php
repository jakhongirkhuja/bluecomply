<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\LinkGeneratorController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Driver\DriverTagController;
use App\Http\Controllers\Company\CompanyDriverController;
use App\Http\Controllers\Company\DrugTestController;
use App\Http\Controllers\Company\DriverTerminationController;
use App\Http\Controllers\Company\NoteController;
use App\Http\Controllers\Company\DriverDocumentController;
Route::prefix('v1/company')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('driver-links',LinkGeneratorController::class);
    Route::apiResource('driver-tags', DriverTagController::class)->only(['store', 'update', 'destroy' ]);
    Route::apiResource('drug-tests', DrugTestController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('driver-terminations', DriverTerminationController::class)->only(['store','destroy']);
    Route::apiResource('notes', NoteController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('drivers/documents', DriverDocumentController::class)->only(['store', 'destroy']);
    Route::post('drivers/documents/files', [DriverDocumentController::class, 'addFiles']);
    Route::delete('drivers/documents/files/{id}', [DriverDocumentController::class, 'deleteFiles']);
    Route::get('drivers', [CompanyDriverController::class, 'getDrivers']);
    Route::get('drivers/details/{id}', [CompanyDriverController::class, 'getDriverDetails']);
    Route::post('drivers/add-task', [CompanyDriverController::class, 'addTask']);
    Route::post('drivers/add-driver', [CompanyDriverController::class, 'addDriver']);
    Route::post('drivers/change-status', [CompanyDriverController::class, 'drivers_change_status']);
    Route::post('drivers/change-profile', [CompanyDriverController::class, 'drivers_change_profile']);
});
