<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use App\ViewModels\WalletViewModel;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

/**
 * @codeCoverageIgnore
 */
final class MonitorCache implements Contract
{
    use ManagesCache;

    public function setBlockCount(Closure $callback): string
    {
        return $this->remember('block_count', $this->blockTimeTTL(), $callback);
    }

    public function setNextValidator(Closure $callback): ? WalletViewModel
    {
        return $this->remember('next_validator', $this->blockTimeTTL(), $callback);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('monitor');
    }
}
