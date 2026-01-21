<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
Route::get('/', function () {
    Artisan::call('migrate:fresh --seed');
    Artisan::call('storage:link');

//    echo Artisan::output();
    return view('welcome');
});
