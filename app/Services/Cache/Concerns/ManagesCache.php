<?php

declare(strict_types=1);

namespace App\Services\Cache\Concerns;

use Carbon\Carbon;
use Closure;

trait ManagesCache
{
    /**
     * @param null|mixed $default
     *
     * @return mixed
     */
    private function get(string $key, $default = null)
    {
        return $this->getCache()->get(md5($key), $default);
    }

    /**
     * @param Carbon|int $ttl
     *
     * @return mixed
     */
    private function remember(string $key, $ttl, Closure $callback)
    {
        return $this->getCache()->remember(md5($key), $ttl, $callback);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function put(string $key, $value)
    {
        return $this->getCache()->put(md5($key), $value);
    }
}
