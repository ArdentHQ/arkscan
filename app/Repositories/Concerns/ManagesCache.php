<?php

declare(strict_types=1);

namespace App\Repositories\Concerns;

use Closure;

trait ManagesCache
{
    /**
     * @return mixed
     */
    private function remember(Closure $callback, int $seconds = 60)
    {
        return $this->getCache()->remember($this->cacheKey(), $seconds, $callback);
    }

    private function cacheKey(): string
    {
        return sprintf(
            '%s/%s-%s',
            class_basename($this),
            debug_backtrace()[2]['function'],
            serialize(debug_backtrace()[2]['args'] ?? '')
        );
    }
}
