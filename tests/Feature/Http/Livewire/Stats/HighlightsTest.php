<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\Stats\Highlights;
use App\Models\State;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use Livewire\Livewire;

it('should render the component', function (): void {
    State::factory()->create(['supply' => 13628098200000000 * 10]);

    Wallet::factory()->count(10)->create();

    (new NetworkCache())->setValidatorRegistrationCount(1171);
    (new NetworkCache())->setVotesPercentage('74.08');
    (new ValidatorCache())->setTotalWalletsVoted(0);
    (new ValidatorCache())->setTotalBalanceVoted(84235364);

    $currency = Network::currency();

    Livewire::test(Highlights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.highlights.total_supply'),
            '1,362,809,820 '.$currency,
            trans('pages.statistics.highlights.voting', ['percent' => '74.08%']),
            '84,235,364 '.$currency,
            trans('pages.statistics.highlights.validators'),
            '1,171',
            trans('pages.statistics.highlights.addresses'),
            '10',
        ]);
});
