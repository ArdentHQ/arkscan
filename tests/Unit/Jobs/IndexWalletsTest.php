<?php

declare(strict_types=1);

use App\Jobs\IndexWallets;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Scout\Events\ModelsImported;

beforeEach(function () {
    // Default value, overriden in phpunit.xml for the tests
    Config::set('scout.driver', 'meilisearch');
});

it('should index new Wallets', function () {
    Event::fake();

    $lastIndexedWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $lastIndexedWallet->address,
        'timestamp'    => 5,
    ]);

    $newWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $newWallet->address,
        'timestamp'    => 10,
    ]);

    $oldWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $oldWallet->address,
        'timestamp'    => 1,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'wallets'
    );

    Http::fake([
        $url => Http::response([
            'hits' => [
                ['timestamp' => 5],
            ],
        ]),
    ]);

    IndexWallets::dispatch();

    Event::assertDispatched(ModelsImported::class, function ($event) use ($newWallet) {
        return $event->models->count() === 1
            && $event->models->first()->is($newWallet);
    });
});

it('should not index anything if meilisearch is empty', function () {
    Event::fake();

    $lastIndexedWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $lastIndexedWallet->address,
        'timestamp'    => 5,
    ]);

    $newWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $newWallet->address,
        'timestamp'    => 10,
    ]);

    $oldWallet = Wallet::factory()->create();
    Transaction::factory()->create([
        'recipient_id' => $oldWallet->address,
        'timestamp'    => 1,
    ]);

    $url = sprintf(
        '%s/indexes/%s/search',
        config('scout.meilisearch.host'),
        'wallets'
    );

    Http::fake([
        $url => Http::response([
            // Empty results
            'hits' => [],
        ]),
    ]);

    IndexWallets::dispatch();

    Event::assertNotDispatched(ModelsImported::class);
});
