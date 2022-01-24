<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\EntityNotFoundInterface;
use App\Http\Kernel;
use App\Http\Middleware\SubstituteBindings;
use Closure;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     *
     * @throws Exception
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) : void {
            if ($this->shouldReport($e) && app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Throwable $exception
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function render($request, Throwable $exception)
    {
        if ($this->shouldShowEntity404Page($request, $exception)) {
            return $this->getNotFoundEntityResponse($exception);
        }

        if ($this->sessionAlreadyStarted()) {
            return parent::render($request, $exception);
        }

        return $this->applyWebMiddlewares($request, fn ($request) => parent::render($request, $exception));
    }

    private function applyWebMiddlewares(Request $request, Closure $next): Response
    {
        $except = [
            SubstituteBindings::class,
        ];

        $middlewares = collect(app(Kernel::class)->getMiddlewareGroups()['web'])
            ->filter(fn ($middleware) => ! in_array($middleware, $except, true));

        return $this->applyMiddlewares($middlewares, $request, $next);
    }

    private function applyMiddlewares(Collection $middlewares, Request $request, Closure $next): Response
    {
        if ($middlewares->count() === 0) {
            return $next($request);
        }

        $middleware = $middlewares->shift();

        return app($middleware)
            ->handle($request, fn ($req) => $this->applyMiddlewares($middlewares, $req, $next));
    }

    private function shouldShowEntity404Page(Request $request, Throwable $exception): bool
    {
        $expectedException     = $this->prepareException($this->mapException($exception));
        $mainNotFoundException = $expectedException->getPrevious();

        return $this->isARegularGetRequest($request)
            && $mainNotFoundException !== null
            && is_a($mainNotFoundException, EntityNotFoundInterface::class);
    }

    private function getNotFoundEntityResponse(Throwable $exception): HttpResponse
    {
        $expectedException = $this->prepareException($this->mapException($exception));

        return response()->view('errors.404_entity', [
            'exception' => $expectedException,
        ], 404);
    }

    private function isARegularGetRequest(Request $request): bool
    {
        return $request->method() === 'GET' && ! $request->expectsJson();
    }

    private function sessionAlreadyStarted(): bool
    {
        return app(SessionManager::class)->driver()->isStarted();
    }
}
