<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\StatsHighlights;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\NetworkCache;
use Livewire\Livewire;

it('should render the component', function (): void {
    $wallets = Wallet::factory()->count(10)->create(['balance' => '13628098200000000']);

    Block::factory()->create([
        'generator_public_key' => $wallets->get(0)->public_key,
    ]);

    (new NetworkCache())->setDelegateRegistrationCount(1171);
    (new NetworkCache())->setVotesPercentage('74.08');
    (new DelegateCache())->setTotalVoted([0, 84235364]);

    $currency = Network::currency();

    Livewire::test(StatsHighlights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.highlights.total_supply'),
            '1,362,809,820 '.$currency,
            trans('pages.statistics.highlights.voting', ['percent' => '74.08%']),
            '84,235,364 '.$currency,
            trans('pages.statistics.highlights.delegates'),
            '1,171',
            trans('pages.statistics.highlights.wallets'),
            '10',
        ]);
});
