<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\MarketDataProvider;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
        $data = $marketDataProvider->historicalHourly($this->source, $this->currency);
        if (! $data->isEmpty()) {
            $cache->setHistoricalHourly(
                $this->source,
                $this->currency,
                $data,
            );
        }
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}
