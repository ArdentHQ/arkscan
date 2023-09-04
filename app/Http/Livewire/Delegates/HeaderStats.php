<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Actions\CacheNetworkSupply;
use App\Models\ForgingStats;
use App\Services\Cache\DelegateCache;
use Illuminate\View\View;
use Livewire\Component;

final class HeaderStats extends Component
{
    public function render(): View
    {
        [$missedBlockCount, $delegatesMissed] = $this->missedBlocks();
        [$voterCount, $totalVoted]            = (new DelegateCache())->getTotalVoted();

        return view('livewire.delegates.header-stats', [
            'voterCount'      => $voterCount,
            'totalVoted'      => $totalVoted,
            'currentSupply'   => CacheNetworkSupply::execute() / 1e8,
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
