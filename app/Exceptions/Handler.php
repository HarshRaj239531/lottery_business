<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        ModelNotFoundException::class,
        ThrottleRequestsException::class,
        ValidationException::class,
        UnauthorizedHttpException::class,
    ];

    public function register()
    {
        $this->renderable(function ($exception, $request) {
            // 404 Not Found
            if ($exception instanceof NotFoundHttpException ||
                $exception instanceof ModelNotFoundException)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Route or resource not found',
                    'data' => null
                ], 404);
            }

            // 401 Unauthorized
            if ($exception instanceof UnauthorizedHttpException ||
                $exception instanceof AuthorizationException)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 401);
            }

            // 403 Forbidden
            if ($exception instanceof AuthorizationException)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Access denied',
                    'data' => null
                ], 403);
            }

            // 422 Validation
            if ($exception instanceof ValidationException)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'data' => $exception->errors()
                ], 422);
            }

            // 429 Rate Limit
            if ($exception instanceof ThrottleRequestsException)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many requests',
                    'data' => null
                ], 429);
            }

            // 500 Internal Server Error
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => null
            ], 500);
        });
    }
}