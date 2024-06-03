<?php

declare(strict_types=1);

namespace App\Livewire\Controllers;

use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware as Base;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Livewire made a change a few months ago which introduced issues. It tries to generate a request
// based on the route/path of the livewire message, but if that page is a 404 (e.g. route does not exist),
// livewire throws an exception. This handles it by using the route path.
final class HttpConnectionHandler extends Base
{
    public function applyPersistentMiddleware(): void
    {
        try {
            parent::applyPersistentMiddleware();

            return;
        } catch (NotFoundHttpException $e) {
            $originalUrl = request()->root();

            $request = $this->makeRequestFromUrlAndMethod(
                $originalUrl,
                Livewire::originalMethod()
            );
        }

        // Below is taken from the original HttpConnectionHandler#applyPersistentMiddleware method
        // @codeCoverageIgnoreStart
        $originalRouteMiddleware = app('router')->gatherRouteMiddleware($request->route());

        $persistentMiddleware = Livewire::getPersistentMiddleware();

        $filteredMiddleware = collect($originalRouteMiddleware)->filter(function ($middleware) use ($persistentMiddleware) {
            if (! is_string($middleware)) {
                return false;
            }

            return in_array(Str::before($middleware, ':'), $persistentMiddleware, true);
        })->toArray();

        (new Pipeline(app()))
            ->send($request)
            ->through($filteredMiddleware)
            ->then(function () {
                return new Response();
            });
        // @codeCoverageIgnoreEnd
    }
}
