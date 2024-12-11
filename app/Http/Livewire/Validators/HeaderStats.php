<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\Models\ForgingStats;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use Illuminate\View\View;
use Livewire\Component;

final class HeaderStats extends Component
{
    public function render(): View
    {
        [$missedBlockCount, $validatorsMissed] = $this->missedBlocks();

        $validatorCache = new ValidatorCache();
        $voterCount     = $validatorCache->getTotalWalletsVoted();
        $totalVoted     = $validatorCache->getTotalBalanceVoted();

        return view('livewire.validators.header-stats', [
            'voterCount'       => $voterCount,
            'totalVoted'       => $totalVoted,
            'votesPercentage'  => (new NetworkCache())->getVotesPercentage(),
            'missedBlocks'     => $missedBlockCount,
            'validatorsMissed' => $validatorsMissed,
        ]);
    }

    public function missedBlocks(): array
    {
        $stats = ForgingStats::where('forged', false)->get();

        return [
            $stats->count(),
            $stats->unique('address')->count(),
        ];
    }
}
