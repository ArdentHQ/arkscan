<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\CryptoCompare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheCurrenciesHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 5;

    public function __construct(public string $source, public string $currency)
    {
    }

    public function handle(): void
    {
        $cache = app(NetworkStatusBlockCache::class);

        try {
            $cache->setHistoricalHourly(
                $this->source,
                $this->currency,
                CryptoCompare::historicalHourly($this->source, $this->currency)
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
