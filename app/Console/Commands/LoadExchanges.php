<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Exchange;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class LoadExchanges extends Command
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get($this->getUrl());

        $this->validateResponse($response);

        $exchanges = $response->json();

        $this->validateResponseData($exchanges);

        $items = collect($exchanges)->map(function ($item) {
            return [
                'name'          => $item['exchangeName'],
                'url'           => $item['baseURL'],
                'is_exchange'   => $item['exchange'],
                'is_aggregator' => $item['aggregator'],
                'btc'           => $item['BTC'],
                'eth'           => $item['ETH'],
                'stablecoins'   => $item['stablecoins'],
                'other'         => $item['other'],
                'coingecko_id'  => $item['coingeckoId'],
            ];
        });

        // Sync exchange data
        Exchange::upsert($items->toArray(), 'name');

        // Remove the ones that are no longer part of the list
        Exchange::whereNotIn('name', $items->pluck('name')->toArray())->delete();

        return Command::SUCCESS;
    }

    /**
     * @var array<mixed>
     */
    private function validateResponseData(array $response): void
    {
        $expectedKeys = [
            'exchangeName',
            'baseURL',
            'exchange',
            'aggregator',
            'BTC',
            'ETH',
            'stablecoins',
            'other',
            'coingeckoId',
        ];

        // check that keys are the same
        if (array_diff($expectedKeys, array_keys($response[0]))) {
            throw new Exception('Unexpected response format');
        }
    }

    /**
     * @var array<mixed>
     */
    private function validateResponse(Response $response): void
    {
        if (! $response->ok()) {
            throw new Exception('Failed to load exchanges list');
        }
    }

    private function getUrl(): string
    {
        $url = config('explorer.exchanges.list_src');

        if (! $url) {
            throw new Exception('No exchanges list source configured');
        }

        return $url;
    }
}
