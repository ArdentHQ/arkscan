<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\MarketDataProvider;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheCurrenciesHistory implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 5;

    public function __construct(public string $source, public string $currency)
    {
    }

    public function handle(NetworkStatusBlockCache $cache, MarketDataProvider $marketDataProvider): void
    {
        try {
            $cache->setHistoricalHourly(
                $this->source,
                $this->currency,
                $marketDataProvider->historicalHourly($this->source, $this->currency)
            );
        } catch (ConnectionException $e) {
            $cache->setHistoricalHourly($this->source, $this->currency, null);

            throw $e;
        }
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}
