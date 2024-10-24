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
     * @template TCacheValue
     *
     * @param  string  $key
     * @param  \Closure(): TCacheValue  $callback
     * @return TCacheValue
     */
    public static function remember(string $key, callable $callback): mixed
    {
        return Cache::driver('array')->rememberForever($key, $callback);
    }
}
