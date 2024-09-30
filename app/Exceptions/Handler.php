<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse|Response|SymfonyResponse
    {
        if (!$request->is('api/*')) {
            return parent::render($request, $e);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json(
                [
                    'status' => false,
                    'error' => 'Invalid route',
                ],
                404,
            );
        } else if ($e instanceof ModelNotFoundException) {
            return response()->json(
                [
                    'status' => false,
                    'error' => 'Resource with input ids not found',
                ],
                400,
            );
        } else if ($e instanceof AuthenticationException) {
            return response()->json(
                [
                    'status' => false,
                    'error' => 'Invalid token',
                ],
                401,
            );
        }

        return response()->json(
            [
                'status' => false,
                'error' => $e->getMessage()
            ],
            400,
        );
    }
}
