<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\EntityNotFoundInterface;
use App\Exceptions\TransactionNotFoundException;
use App\Exceptions\BlockNotFoundException;
use App\Http\Kernel;
use App\Http\Middleware\SubstituteBindings;
use ARKEcosystem\Foundation\UserInterface\Exceptions\Concerns\OverridesExceptionView;
use Closure;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class Handler extends ExceptionHandler
{
    use OverridesExceptionView;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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
        $this->registerErrorViewPaths();

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

        /** @var array<int, string|class-string> */
        $middlewares = app(Kernel::class)->getMiddlewareGroups()['web'];

        $middlewares = collect($middlewares)->filter(fn ($middleware) => ! in_array($middleware, $except, true));

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

        $type = 'wallet';
        if ($expectedException->getPrevious() instanceof TransactionNotFoundException) {
            $type = 'transaction';
        } elseif ($expectedException->getPrevious() instanceof BlockNotFoundException) {
            $type = 'block';
        }

        /** @var EntityNotFoundInterface $previousException */
        $previousException = $expectedException->getPrevious();

        return Inertia::renderWithMeta('Error/NotFound', '404', [
            'error' => (string) $previousException->getCustomMessage(),
            'id'    => collect($previousException->getIds())->first(),
            'type'  => $type,
        ], [
            'error' => trans('ui::errors.404'),
        ])->toResponse(request());
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
