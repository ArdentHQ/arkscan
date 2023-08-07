<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkSupply;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\DelegateCache;
use Illuminate\View\View;
use Livewire\Component;

final class DelegateDataBoxes extends Component
{
    public function render(): View
    {
        $delegateCache = new DelegateCache();

        [$missedBlockCount, $delegatesMissed] = $this->missedBlocks($delegateCache);
        [$voterCount, $totalVoted] = $this->voted($delegateCache);

        return view('livewire.delegate-data-boxes', [
            'voterCount'      => $voterCount,
            'totalVoted'      => $totalVoted,
            'currentSupply'   => CacheNetworkSupply::execute() / 1e8,
            'missedBlocks'    => $missedBlockCount,
            'delegatesMissed' => $delegatesMissed,
        ]);
    }

    private function missedBlocks(DelegateCache $delegateCache): array
    {
        return $delegateCache->setMissedBlocks(function () {
            $stats = ForgingStats::where('forged', false)->get();

            return [
                $stats->count(),
                $stats->unique('public_key')->count(),
            ];
        });
    }

    private function voted(DelegateCache $delegateCache): array
    {
        return $delegateCache->setTotalVoted(function () {
            $wallets = Wallet::select('balance')
                ->whereRaw("\"attributes\"->>'vote' is not null")
                ->get();

            $totalVoted = BigNumber::new(0);
            foreach ($wallets as $wallet) {
                $totalVoted->plus($wallet['balance']->valueOf());
            }

            return [
                $wallets->count(),
                $totalVoted->toFloat(),
            ];
        });
    }
}
