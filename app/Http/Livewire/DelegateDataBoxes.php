<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkSupply;
use App\Http\Livewire\Concerns\DelegateData;
use App\Models\ForgingStats;
use App\Services\Cache\DelegateCache;
use Illuminate\View\View;
use Livewire\Component;

final class DelegateDataBoxes extends Component
{
    use DelegateData;

    private array $delegates = [];

    private array $statistics = [];

    public function render(): View
    {
        $delegateCache   = new DelegateCache();
        $this->delegates = $this->fetchDelegates();

        [$missedBlockCount, $delegatesMissed] = $delegateCache->setMissedBlocks(function () {
            $stats = ForgingStats::where('forged', false)->get();

            return [$stats->count(), $stats->unique('public_key')->count()];
        });

        return view('livewire.delegate-data-boxes', [
            'voterCount'      => $delegateCache->getVoterCount(),
            'totalVoted'      => $delegateCache->getTotalVoted(),
            'currentSupply'   => CacheNetworkSupply::execute() / 1e8,
            'missedBlocks'    => $missedBlockCount,
            'delegatesMissed' => $delegatesMissed,
        ]);
    }
}
