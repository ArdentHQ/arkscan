<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateStatistics;
use App\Services\Cache\NetworkCache;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

// @TODO: make assertions about the visibility of the data
it('should render the component', function () {
    configureExplorerDatabase();

    (new NetworkCache())->setDelegateRegistrationCount(1000);
    (new NetworkCache())->setFeesCollected('1000');
    (new NetworkCache())->setVotesCount('1000');
    (new NetworkCache())->setVotesPercentage('1000');

    Livewire::test(DelegateStatistics::class)
        ->assertSee(trans('pages.delegates.statistics.delegate_registrations'))
        ->assertSee(trans('pages.delegates.statistics.block_reward'))
        ->assertSee(trans('pages.delegates.statistics.fees_collected'))
        ->assertSee(trans('pages.delegates.statistics.votes'));
});
