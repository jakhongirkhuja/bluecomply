<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data = null, $status = 200) {
            return Response::json([
                'status' => 'success',
                'data'   => $data,
            ], $status);
        });

        Response::macro('error', function ($message, $status = 400) {
            return Response::json([
                'status'  => 'error',
                'message' => $message,
            ], $status);
        });
    }
}
