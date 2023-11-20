<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Models\ForgingStats;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\NetworkCache;
use Illuminate\View\View;
use Livewire\Component;

final class HeaderStats extends Component
{
    public function render(): View
    {
        [$missedBlockCount, $delegatesMissed] = $this->missedBlocks();

        $delegateCache = new DelegateCache();
        $voterCount = $delegateCache->getTotalWalletsVoted();
        $totalVoted = $delegateCache->getTotalBalanceVoted();

        return view('livewire.delegates.header-stats', [
            'voterCount'      => $voterCount,
            'totalVoted'      => $totalVoted,
            'votesPercentage' => (new NetworkCache())->getVotesPercentage(),
            'missedBlocks'    => $missedBlockCount,
            'delegatesMissed' => $delegatesMissed,
        ]);
    }

    public function missedBlocks(): array
    {
        $stats = ForgingStats::where('forged', false)->get();

        return [
            $stats->count(),
            $stats->unique('public_key')->count(),
        ];
    }
}
