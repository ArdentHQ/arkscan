<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Services\Cache\NetworkCache;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorStatistics extends Component
{
    public function render(): View
    {
        return view('livewire.monitor-statistics', [
            'delegateRegistrations' => (new NetworkCache())->getDelegateRegistrationCount(),
            'blockReward'           => Network::blockReward(),
            'feesCollected'         => (new NetworkCache())->getFeesCollected(),
            'votes'                 => (new NetworkCache())->getVotesCount(),
            'votesPercentage'       => (new NetworkCache())->getVotesPercentage(),
        ]);
    }
}
