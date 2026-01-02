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
use App\Http\Controllers\Company\EmploymentVerificationController;

Route::prefix('v1/company')->middleware(['auth:sanctum'])->group(function () { //auth:sanctum
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('driver-links',LinkGeneratorController::class);
    Route::apiResource('driver-tags', DriverTagController::class)->only(['store', 'update', 'destroy' ]);
    Route::apiResource('drug-tests', DrugTestController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('driver-terminations', DriverTerminationController::class)->only(['store','destroy']);
    Route::apiResource('notes', NoteController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('drivers/documents', DriverDocumentController::class)->only(['store', 'destroy']);
    Route::apiResource('drivers/verifications', EmploymentVerificationController::class)->only(['show','store', 'destroy']);
    Route::post('drivers/documents/files', [DriverDocumentController::class, 'addFiles']);
    Route::delete('drivers/documents/files/{id}', [DriverDocumentController::class, 'deleteFiles']);
    Route::get('drivers', [CompanyDriverController::class, 'getDrivers']);
    Route::get('drivers/details/{id}', [CompanyDriverController::class, 'getDriverDetails']);
    Route::post('drivers/add-task', [CompanyDriverController::class, 'addTask']);
    Route::post('drivers/add-driver', [CompanyDriverController::class, 'addDriver']);
    Route::post('drivers/change-status', [CompanyDriverController::class, 'drivers_change_status']);
    Route::post('drivers/change-profile', [CompanyDriverController::class, 'drivers_change_profile']);


//    Route::prefix('drivers/verifications')->group(function () {
//        // Create verification
//        Route::post('/', [EmploymentVerificationController::class, 'store'])->name('verifications.store');
//
//        // Send verification
//        Route::post('{verification}/send', [EmploymentVerificationController::class, 'send'])->name('verifications.send');
//
//        // Follow-up
//        Route::post('{verification}/follow-up', [EmploymentVerificationController::class, 'followUp'])->name('verifications.followUp');
//
//        // Provide detailed response
//        Route::post('{verification}/respond', [EmploymentVerificationController::class, 'respond'])->name('verifications.respond');
//
//        // Complete verification
//        Route::post('{verification}/complete', [EmploymentVerificationController::class, 'complete'])->name('verifications.complete');
//
//        // Delete verification
//        Route::delete('{verification}', [EmploymentVerificationController::class, 'destroy'])->name('verifications.destroy');
//
//        // Optional: list all verifications for a driver
//        Route::get('/', [EmploymentVerificationController::class, 'index'])->name('verifications.index');
//
//        // Optional: show single verification with events/responses
//        Route::get('{verification}', [EmploymentVerificationController::class, 'show'])->name('verifications.show');
//    });
});
