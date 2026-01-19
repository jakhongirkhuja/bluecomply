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
use App\Http\Controllers\Company\AnalyticController;

Route::get('general', [GeneralController::class, 'getData']);



Route::prefix('v1/general')->group(function () { //auth:sanctum
    Route::get('', [GeneralController::class, 'getData']);
});
Route::prefix('v1/company/{company_id}')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum

    Route::apiResource('driver-links', LinkGeneratorController::class);
    Route::apiResource('driver-tags', DriverTagController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('drug-tests', DrugTestController::class)->only(['store', 'destroy']);
    Route::apiResource('driver-terminations', DriverTerminationController::class)->only(['store', 'destroy']);
    Route::apiResource('notes', NoteController::class)->only(['store', 'update', 'destroy']);



    Route::prefix('drivers')->group(function () {
        Route::get('', [CompanyDriverController::class, 'getDrivers']);
        Route::apiResource('claims', ClaimController::class)->only(['show','store', 'destroy']);
        Route::apiResource('documents', DriverDocumentController::class)->only(['store', 'destroy']);
        Route::apiResource('incidents', IncidentController::class);  //put for accident and other demage
        Route::put('incidents/{incident}/other-incidents', [IncidentController::class, 'createOtherIncidents']); // put for other-incidents
        Route::put('incidents/{incident}/citations', [IncidentController::class, 'createCitation']); // put for citation
        Route::put('incidents/{incident}/inspections', [IncidentController::class, 'createRoadsideInspection']); // put for citation
        Route::post('incidents/{incident}/files', [IncidentController::class, 'files']);
        Route::put('incidents/{incident}/files/{id}', [IncidentController::class, 'fileNameEdit']);
        Route::delete('incidents/{incident}/files/{id}', [IncidentController::class, 'filesDelete']);
        Route::apiResource('drug-alcohol', DrugTestOrderController::class)->only(['show', 'store']);
        Route::apiResource('verifications', EmploymentVerificationController::class)->only(['show', 'store', 'destroy']);
        Route::post('verifications/{verification}/respond', [EmploymentVerificationController::class, 'respond']);
        Route::post('documents/files', [DriverDocumentController::class, 'addFiles']);
        Route::delete('documents/files/{id}', [DriverDocumentController::class, 'deleteFiles']);
        Route::get('details/{id}', [CompanyDriverController::class, 'getDriverDetails']);
        Route::get('details/{id}/incidents/analytics', [CompanyDriverController::class, 'getDriverIncidentAnalytics']);
        Route::post('add-task', [CompanyDriverController::class, 'addTask']);
        Route::post('add-driver', [CompanyDriverController::class, 'addDriver']);
        Route::post('change-status', [CompanyDriverController::class, 'drivers_change_status']);
        Route::post('review/{id}', [CompanyDriverController::class, 'drivers_review']);
        Route::post('change-profile', [CompanyDriverController::class, 'drivers_change_profile']);
    });



    Route::prefix('analytics')->middleware(['auth:sanctum'])->group(function () {
        Route::get('', [AnalyticController::class, 'getAnalytics']);
    });

});


