<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Exchange;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class LoadExchanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchanges:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load ark exchanges';

    public function handle(): int
    {
        $response = Http::get($this->getUrl());

        $this->validateResponse($response);

        /**
         * @var array{
         *     name: string,
         *     url: string,
         *     isExchange: bool,
         *     isAggregator: bool,
         *     btc: bool,
         *     eth: bool,
         *     stablecoins: bool,
         *     other: bool,
         *     icon: string,
         *     providerExchangeId: string | null,
         *     price: float | null,
         *     volume: float | null,
         * }[]
         */
        $exchanges = $response->json('data');

        $this->validateResponseData($exchanges);

        $items = collect($exchanges)->map(function ($item) {
            return [
                'name'                 => $item['name'],
                'url'                  => $item['url'],
                'is_exchange'          => $item['isExchange'],
                'is_aggregator'        => $item['isAggregator'],
                'btc'                  => $item['btc'],
                'eth'                  => $item['eth'],
                'stablecoins'          => $item['stablecoins'],
                'other'                => $item['other'],
                'provider_exchange_id' => $item['providerExchangeId'],
                'icon'                 => $item['icon'],
                'price'                => $item['price'],
                'volume'               => $item['volume'],
            ];
        });

        // Sync exchange data
        Exchange::upsert($items->toArray(), 'name');

        // Remove the ones that are no longer part of the list
        Exchange::whereNotIn('name', $items->pluck('name')->toArray())->delete();

        return Command::SUCCESS;
    }

    /**
     * @param array<mixed> $response
     */
    private function validateResponseData(array $response): void
    {
        $expectedKeys = [
            'name',
            'url',
            'isExchange',
            'isAggregator',
            'btc',
            'eth',
            'stablecoins',
            'other',
            'icon',
            'providerExchangeId',
        ];

        // check that keys are the same
        if (count(array_diff($expectedKeys, array_keys($response[0]))) > 0) {
            throw new Exception('Unexpected response format');
        }
    }

    private function validateResponse(Response $response): void
    {
        if (! $response->ok()) {
            throw new Exception('Failed to load exchanges list');
        }
    }

    private function getUrl(): string
    {
        $baseUrl = config('arkscan.market_data.ark_pricing.url');

        if (! is_string($baseUrl) || $baseUrl === '') {
            throw new Exception('ARK_PRICING_URL is not configured');
        }

        return rtrim($baseUrl, '/').'/api/v1/exchanges';
    }
}
