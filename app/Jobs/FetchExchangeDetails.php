<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\MarketDataProvider;
use App\Exceptions\CoinGeckoThrottledException;
use App\Models\Exchange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

final class FetchExchangeDetails implements ShouldQueue
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
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Exchange $exchange)
    {
        //
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new RateLimited('coingecko_api_rate')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $result = app(MarketDataProvider::class)->exchangeDetails($this->exchange);
        } catch (CoinGeckoThrottledException $e) {
            // Release back to the queue
            $this->release(60); // 60 seconds = 1 minute

            return;
        }

        $price = Arr::get($result, 'price');
        if ($price !== null) {
            $this->exchange->price = $price;
        }

        $volume = Arr::get($result, 'volume');
        if ($volume !== null) {
            $this->exchange->volume = $volume;
        }

        $this->exchange->save();
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return int
     */
    public function retryAfter()
    {
        return 60; // 60 seconds = 1 minute
    }
}
