<?php

declare(strict_types=1);

namespace App\Services\Cache\Concerns;

use App\Facades\Network;
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
        // @phpstan-ignore-next-line
        return $this->getCache()->remember(md5($key), $ttl, $callback);
    }

    /**
     * @param mixed $value
     * @param Carbon|int $ttl
     *
     * @return mixed
     */
    private function put(string $key, $value, $ttl = null)
    {
        return $this->getCache()->put(md5($key), $value, $ttl = null);
    }

    /**
     * @return bool
     */
    private function forget(string $key)
    {
        // @codeCoverageIgnoreStart
        // This method is currently not used in the application, keeping it for
        // completeness and potential future use.
        return $this->getCache()->forget(md5($key));
        // @codeCoverageIgnoreEnd
    }

    private function blockTimeTTL(): int
    {
        return (int) ceil(Network::blockTime() / 2);
    }
}
