<?php

declare(strict_types=1);

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * Used to cache values for the duration of a single request.
 */
final class RequestScopedCache
{
    /**
     * @param  string           $key
     * @param  \Closure():mixed $callback
     * @return mixed
     */
    public static function remember(string $key, callable $callback): mixed
    {
        return Cache::driver('array')->rememberForever($key, $callback);
    }
}
