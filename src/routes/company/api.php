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
use App\Http\Controllers\Company\ProfileController;
use App\Http\Controllers\Company\NotificationController;
use App\Http\Controllers\Company\MessageController;
use App\Http\Controllers\Company\FleetController;
use App\Http\Controllers\Company\SafetyController;
use App\Http\Controllers\Company\DocumentController;
use App\Http\Controllers\Company\DataqChallengeController;
use App\Http\Controllers\Company\SettingController;
use App\Http\Controllers\Company\EmploymentMenuController;
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
    Route::apiResource('challenges', DataqChallengeController::class)->only(['index','show','store', 'destroy']);;

    Route::get('users', [CompanyController::class, 'getUsers']);


    Route::prefix('drivers')->group(function () {
        Route::get('', [CompanyDriverController::class, 'getDrivers']);
        Route::get('count', [CompanyDriverController::class, 'countDrivers']);
        Route::get('vehicles/{driver_id}', [CompanyDriverController::class, 'getDriverVehicle']);
        Route::get('save-filters', [CompanyDriverController::class, 'saveFilterList']);
        Route::post('save-filters', [CompanyDriverController::class, 'saveFilter']);
        Route::delete('save-filters/{id}', [CompanyDriverController::class, 'saveFilterDelete']);
        Route::put('save-filters/{id}', [CompanyDriverController::class, 'updateFilterName']);
        Route::put('save-filters-name/{id}', [CompanyDriverController::class, 'updateFilterName']);

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
        Route::delete('delete-task/{task_id}', [CompanyDriverController::class, 'deleteTask']);
        Route::post('assign-vehicle', [CompanyDriverController::class, 'assignVehicle']);
        Route::post('add-driver', [CompanyDriverController::class, 'addDriver']);
        Route::post('change-status', [CompanyDriverController::class, 'drivers_change_status']);
        Route::post('review/{id}', [CompanyDriverController::class, 'drivers_review']);
        Route::post('change-profile', [CompanyDriverController::class, 'drivers_change_profile']);

        Route::get('messages/{id}', [MessageController::class, 'messages']);
        Route::post('messages/{id}', [MessageController::class, 'messagePost']);

    });

    Route::prefix('analytics')->group(function () {
        Route::get('', [AnalyticController::class, 'getAnalytics']);
        Route::get('compliance', [AnalyticController::class, 'compliance']);
    });

    Route::prefix('profile')->group(function () {
        Route::put('edit', [ProfileController::class, 'profileEdit']);
        Route::get('companies', [ProfileController::class, 'profileCompanies']);
        Route::get('logout', [ProfileController::class, 'profileLogout']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('', [NotificationController::class, 'notifications']);
        Route::get('markAsRead/{id}', [NotificationController::class, 'markAsRead']);
        Route::get('countNotifications/', [NotificationController::class, 'countNotifications']);
    });

    Route::prefix('fleets')->group(function () {
        Route::get('', [FleetController::class, 'getVehicles']);
        Route::get('details/{id}', [FleetController::class, 'getDetails']);
        Route::get('count', [FleetController::class, 'countVehicles']);
        Route::post('vehicle/add', [FleetController::class, 'vehicleAdd']);
        Route::post('vehicle/document/add/{vehicle_id}', [FleetController::class, 'documentAdd']);
        Route::delete('vehicle/document/delete/{document_id}', [FleetController::class, 'documentDelete']);


        Route::post('vehicle/document-insurance/add/{vehicle_id}', [FleetController::class, 'documentInsuranceAdd']);
        Route::delete('vehicle/document-insurance/delete/{document_id}', [FleetController::class, 'documentInsuranceDelete']);


        Route::post('vehicle/document-maintenance/add/{vehicle_id}', [FleetController::class, 'documentMaintenanceAdd']);
        Route::delete('vehicle/document-maintenance/delete/{document_id}', [FleetController::class, 'documentMaintenanceDelete']);


        Route::post('vehicle/type/add', [FleetController::class, 'vehicleAddType']);
        Route::delete('vehicle/type/delete/{vehicle_id}', [FleetController::class, 'vehicleTypeDelete']);
    });




    Route::prefix('safety')->group(function () {
        Route::get('inspections/roadside', [SafetyController::class, 'getInspections']);
        Route::get('inspections/roadside/details/{incident_id}', [SafetyController::class, 'getInspectionDetails']);
        Route::get('inspections/count', [SafetyController::class, 'getInspectionCounts']);


        Route::get('inspections/challenges', [SafetyController::class, 'getInspectionChallenges']);
        Route::get('inspections/challenges/details/{challange_id}', [SafetyController::class, 'getInspectionChallengeDetails']);


        Route::get('incidents', [SafetyController::class, 'getIncidents']);
        Route::get('incidents/count', [SafetyController::class, 'getIncidentCounts']);
        Route::get('incidents/details/{incident_id}', [SafetyController::class, 'getIncidentDetails']);

        Route::delete('incidents/delete/{incident_id}', [SafetyController::class, 'deleteIncidentDetails']);
        Route::delete('incidents/delete/{incident_id}/evidence/{evidence_id}', [SafetyController::class, 'deleteIncidentEvidence']);

        Route::get('claims', [SafetyController::class, 'getClaims']);
        Route::get('claims/count', [SafetyController::class, 'getClaimCounts']);
        Route::get('claims/details/{claim_id}', [SafetyController::class, 'getClaimsDetails']);

        Route::post('claims/change-status/{claim_id}', [SafetyController::class, 'claimsChangeStatus']);
        Route::delete('claims/delete/{claim_id}/evidence/{evidence_id}', [SafetyController::class, 'deleteClaimEvidence']);

        Route::get('citations', [SafetyController::class, 'getCitations']);
        Route::get('citations/details/{citation_id}', [SafetyController::class, 'getCitationDetails']);
        Route::post('citations/details/{citation_id}/status-change', [SafetyController::class, 'citationStatusChange']);

    });

    Route::prefix('document')->group(function () {
        Route::get('', [DocumentController::class, 'getDocuments']);
        Route::delete('delete/{id}', [DocumentController::class, 'deleteDocuments']);
        Route::post('upload', [DocumentController::class, 'uploadDocument']);
        Route::post('assign-to-asset', [DocumentController::class, 'assignToAsset']);
    });

    Route::prefix('settings')->group(function () {
        Route::get('user', [SettingController::class, 'getUserInformation']);
        Route::post('user', [SettingController::class, 'postUserInformation']);
        Route::post('der_information', [SettingController::class, 'postDerInformation']);
        Route::get('logout-all', [SettingController::class, 'logoutall']);
        Route::get('delete-account', [SettingController::class, 'deleteAccount']);
        Route::post('save-notification', [SettingController::class, 'saveNotificationSettings']);


        Route::get('mvr/drivers', [SettingController::class, 'mvrGetDrivers']);
        Route::post('mvr/drivers', [SettingController::class, 'mvrPostDrivers']);

        Route::post('general', [SettingController::class, 'generalSettings']);
        Route::get('link', [SettingController::class, 'linkView']);

    });

    Route::prefix('employments')->group(function () {
        Route::get('', [EmploymentMenuController::class, 'getEmployments']);
    });


});


