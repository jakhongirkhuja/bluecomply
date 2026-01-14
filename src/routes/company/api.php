<?php

use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\CompanyDriverController;
use App\Http\Controllers\Company\DriverDocumentController;
use App\Http\Controllers\Company\DriverTerminationController;
use App\Http\Controllers\Company\DrugTestController;
use App\Http\Controllers\Company\EmploymentVerificationController;
use App\Http\Controllers\Company\LinkGeneratorController;
use App\Http\Controllers\Company\NoteController;
use App\Http\Controllers\Driver\DriverTagController;
use App\Http\Controllers\Company\IncidentController;
use App\Http\Controllers\Company\ClaimController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\DrugTestOrderController;
use App\Http\Controllers\GeneralController;
Route::get('general', [GeneralController::class, 'getData']);



Route::prefix('v1/general')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum
    Route::get('', [GeneralController::class, 'getData']);
});
Route::prefix('v1/company')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum

    Route::apiResource('driver-links', LinkGeneratorController::class);
    Route::apiResource('driver-tags', DriverTagController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('drug-tests', DrugTestController::class)->only(['store', 'destroy']);
    Route::apiResource('driver-terminations', DriverTerminationController::class)->only(['store', 'destroy']);
    Route::apiResource('notes', NoteController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('drivers/claims', ClaimController::class)->only(['show','store', 'destroy']);
    Route::apiResource('drivers/documents', DriverDocumentController::class)->only(['store', 'destroy']);
    Route::apiResource('drivers/incidents', IncidentController::class);  //put for accident and other demage

    Route::put('drivers/incidents/{incident}/other-incidents', [IncidentController::class, 'createOtherIncidents']); // put for other-incidents
    Route::put('drivers/incidents/{incident}/citations', [IncidentController::class, 'createCitation']); // put for citation
    Route::put('drivers/incidents/{incident}/inspections', [IncidentController::class, 'createRoadsideInspection']); // put for citation

    Route::post('drivers/incidents/{incident}/files', [IncidentController::class, 'files']);
    Route::put('drivers/incidents/{incident}/files/{id}', [IncidentController::class, 'fileNameEdit']);
    Route::delete('drivers/incidents/{incident}/files/{id}', [IncidentController::class, 'filesDelete']);

    Route::apiResource('drivers/drug-alcohol', DrugTestOrderController::class)->only(['show', 'store']);

    Route::apiResource('drivers/verifications', EmploymentVerificationController::class)->only(['show', 'store', 'destroy']);


    Route::post('drivers/verifications/{verification}/respond', [EmploymentVerificationController::class, 'respond']);

    Route::post('drivers/documents/files', [DriverDocumentController::class, 'addFiles']);
    Route::delete('drivers/documents/files/{id}', [DriverDocumentController::class, 'deleteFiles']);
    Route::get('drivers', [CompanyDriverController::class, 'getDrivers']);
    Route::get('drivers/details/{id}', [CompanyDriverController::class, 'getDriverDetails']);
    Route::get('drivers/details/{id}/incidents/analytics', [CompanyDriverController::class, 'getDriverIncidentAnalytics']);
    Route::post('drivers/add-task', [CompanyDriverController::class, 'addTask']);
    Route::post('drivers/add-driver', [CompanyDriverController::class, 'addDriver']);
    Route::post('drivers/change-status', [CompanyDriverController::class, 'drivers_change_status']);
    Route::post('drivers/review/{id}', [CompanyDriverController::class, 'drivers_review']);
    Route::post('drivers/change-profile', [CompanyDriverController::class, 'drivers_change_profile']);

});
