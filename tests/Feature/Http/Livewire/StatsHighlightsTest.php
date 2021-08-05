<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\StatsHighlights;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Livewire\Livewire;

it('should render the component', function (): void {
    $wallets = Wallet::factory()->count(10)->create(['balance' => '13628098200000000']);

    Block::factory()->create([
        'generator_public_key' => $wallets->get(0)->public_key,
    ]);

    (new NetworkCache())->setDelegateRegistrationCount(1171);
    (new NetworkCache())->setVotesCount('84235364');
    (new NetworkCache())->setVotesPercentage('74.08');

    $currency = Network::currency();

    Livewire::test(StatsHighlights::class)
        ->assertSee(trans('pages.statistics.highlights.total-supply'))
        ->assertSee('1,362,809,820 '.$currency)
        ->assertSee(trans('pages.statistics.highlights.voting', ['percent' => '74.08%']))
        ->assertSee('84,235,364 '.$currency)
        ->assertSee(trans('pages.statistics.highlights.registered-delegates'))
        ->assertSee('1,171')
        ->assertSee(trans('pages.statistics.highlights.wallets'))
        ->assertSee('10');
});
