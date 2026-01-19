<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\I3StatusWebhookController;
use App\Http\Controllers\Company\I3ResultWebhookController;
use App\Http\Controllers\Auth\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/webhooks/i3/status', I3StatusWebhookController::class);

Route::post('/webhooks/i3/results', I3ResultWebhookController::class);


Route::prefix('v1/auth')->group(function () {
    Route::post('login', [AuthController::class, 'authLogin']);
    Route::post('login/confirm', [AuthController::class, 'authLoginConfirm']);
});
