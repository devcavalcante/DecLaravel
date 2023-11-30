<?php

namespace App\Exceptions;

use Exception;
use HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception): Response|JsonResponse
    {
        $exceptionCode = $exception->getCode();
        $exceptionMessage = $exception->getMessage();

        if ($exception instanceof BaseException) {
            return response(['errors' => $exceptionMessage, 'code' => $exceptionCode], $exceptionCode);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response(['errors' => $exceptionMessage, 'code' => 404], 404);
        }

        if ($exception instanceof UnauthorizedException || $exception instanceof AuthorizationException) {
            return response(['errors' => $exceptionMessage, 'code' => 403], 403);
        }

        if ($exception instanceof QueryException) {
            preg_match('#\[(.*?)]#', $exception->getMessage(), $match);
            if ($match[1] == '23503') {
                return response([
                    'errors' => 'Não foi possível deletar.',
                    'code'   => 400,
                ], 400);
            }
            return response(['errors' => 'Não é possivel executar essa ação.'], 400);
        }

        return parent::render($request, $exception);
    }
}
