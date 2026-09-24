<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

final class Authenticate extends Middleware
{
    /**
     * {@inheritdoc}
     */
    protected function redirectTo($request)
    {
        return $request->expectsJson() ? null : route('login');
    }
}
