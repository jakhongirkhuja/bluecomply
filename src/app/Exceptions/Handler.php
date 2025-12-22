<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
class Handler extends Exception
{
    public function register(): void
    {
        $this->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $this->renderable(function (InvalidArgumentException $e, $request) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        });
    }
}
