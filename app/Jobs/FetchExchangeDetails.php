<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Exchange;
use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use App\Contracts\MarketDataProvider;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\RateLimited;

final class FetchExchangeDetails implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The maximum attempts for this job.
     *
     * @var int
     */
    public $tries = 3;

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
        $result = app(MarketDataProvider::class)->exchangeDetails($this->exchange);

        // No information for ark in any usd compatible exchange
        if ($result === null) {
            $this->exchange->price  = null;
            $this->exchange->volume = null;
        } else {
            $this->exchange->price  = Arr::get($result, 'price');
            $this->exchange->volume = Arr::get($result, 'volume');
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
