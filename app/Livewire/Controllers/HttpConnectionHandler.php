<?php

declare(strict_types=1);

namespace App\Livewire\Controllers;

use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Livewire\Controllers\HttpConnectionHandler as Base;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HttpConnectionHandler extends Base
{
    public function applyPersistentMiddleware()
    {
        try {
            return parent::applyPersistentMiddleware();
        } catch (NotFoundHttpException $e) {
            $originalUrl = request()->root();

            $request = $this->makeRequestFromUrlAndMethod(
                $originalUrl,
                Livewire::originalMethod()
            );
        }

        // Gather all the middleware for the original route, and filter it by
        // the ones we have designated for persistence on Livewire requests.
        $originalRouteMiddleware = app('router')->gatherRouteMiddleware($request->route());

        $persistentMiddleware = Livewire::getPersistentMiddleware();

        $filteredMiddleware = collect($originalRouteMiddleware)->filter(function ($middleware) use ($persistentMiddleware) {
            // Some middlewares can be closures.
            if (! is_string($middleware)) {
                return false;
            }

            return in_array(Str::before($middleware, ':'), $persistentMiddleware, true);
        })->toArray();

        // Now run the faux request through the original middleware with a custom pipeline.
        (new Pipeline(app()))
            ->send($request)
            ->through($filteredMiddleware)
            ->then(function () {
                return new Response();
            });
    }
}
