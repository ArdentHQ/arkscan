<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Cache\MarketSquareCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

final class CacheMarketSquareProfileByAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $wallet)
    {
    }

    public function handle(MarketSquareCache $cache): void
    {
        $response = Http::baseUrl(config('explorer.marketsquare_host'))
            ->get(sprintf('api/delegates/%s', $this->wallet['address']))
            ->json();

        if (is_null($response)) {
            return;
        }

        if (Arr::has($response, 'data')) {
            $cache->setProfile($this->wallet['address'], $response['data']);
        }
    }
}
