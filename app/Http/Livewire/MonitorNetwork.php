<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\Monitor\Monitor;
use App\Services\Monitor\RoundCalculator;
use App\Services\NumberFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorNetwork extends Component
{
    public function render(): View
    {
        // dd(RoundCalculator::calculate(5717587));

        $monitor = new Monitor();

        $delegates = $monitor->activeDelegates();

        // @TODO: cache this with a cronjob
        $lastBlocks = Block::query()
            ->distinct('generator_public_key')
            ->whereIn('generator_public_key', $delegates->pluck('public_key'))
            ->limit(Network::delegateCount())
            ->get();

        $delegates = $delegates->transform(function ($delegate) use ($monitor, $lastBlocks) {
            $missedCount = $monitor
                ->status($delegate->public_key)
                ->filter(fn ($round) => $round === false)
                ->count();

            return [
                'order'         => 1,
                'username'      => $delegate->attributes['delegate']['username'],
                'forging_at'    => 0,
                'last_block'    => $lastBlock = $lastBlocks->firstWhere('generator_public_key', $delegate->public_key),
                // Status
                'is_success'    => $missedCount === 0,
                'is_warning'    => $missedCount === 1,
                'is_danger'     => $missedCount >= 2,
                'missed_count'  => NumberFormatter::number(abs(NetworkStatus::height() - $lastBlock->height->toNumber())),
            ];
        });

        return view('livewire.monitor-network', [
            'delegates' => $delegates,
        ]);
    }

    public function activeQuery(): Builder
    {
        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->whereRaw("(\"attributes\"->'delegate'->>'rank')::numeric <= ?", [Network::delegateCount()])
            ->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ASC")
            ->get();
    }
}
