<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Cache\WalletCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheUsername implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public array $wallet)
    {
    }

    public function handle(WalletCache $cache): void
    {
        $username = $this->wallet['attributes']['delegate']['username'];

        $cache->setUsernameByAddress($this->wallet['address'], $username);

        if (! is_null($this->wallet['public_key'])) {
            $cache->setUsernameByPublicKey($this->wallet['public_key'], $username);
        }
    }
}
