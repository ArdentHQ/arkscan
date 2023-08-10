<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Actions\CacheNetworkSupply;
use App\Services\Cache\DelegateCache;
use Illuminate\View\View;
use Livewire\Component;

final class HeaderStats extends Component
{
    public function render(): View
    {
        $delegateCache = new DelegateCache();

        [$missedBlockCount, $delegatesMissed] = $delegateCache->getMissedBlocks();
        [$voterCount, $totalVoted]            = $delegateCache->getTotalVoted();

        return view('livewire.delegates.header-stats', [
            'voterCount'      => $voterCount,
            'totalVoted'      => $totalVoted,
            'currentSupply'   => CacheNetworkSupply::execute() / 1e8,
            'missedBlocks'    => $missedBlockCount,
            'delegatesMissed' => $delegatesMissed,
        ]);
    }
}
