<?php

namespace App\Exceptions;

use App\Util\Constants;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

    // En el método unauthenticated
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => Constants::USER_CHECK_TOKEN_INVALID], Constants::CODE_UNAUTHORIZED);
    }
    
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                "error" => Constants::RESOURCE_NOT_FOUND
            ], Constants::CODE_NOT_FOUND);
        }

        return parent::render($request, $exception);
    }
}
