<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\Monitor;
use App\Services\NumberFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorNetwork extends Component
{
    public function render(): View
    {
        $tracking = DelegateTracker::execute(Monitor::activeDelegates());

        $delegates = [];

        for ($i = 0; $i < count($tracking['delegates']); $i++) {
            $delegate = array_values($tracking['delegates'])[$i];

            $delegates[] = [
                'order'         => $i + 1,
                'username'      => Cache::rememberForever($delegate['publicKey'], function () use ($delegate) {
                    return Wallet::where('public_key', $delegate['publicKey'])->firstOrFail()->attributes['delegate']['username'];
                }),
                'forging_at'    => Carbon::now()->addMilliseconds($delegate['time']),
                'last_block'    => Cache::rememberForever('currentBlock', fn () => Block::current()),
                // Status
                'is_success'    => false, // $missedCount === 0,
                'is_warning'    => false, // $missedCount === 1,
                'is_danger'     => false, // $missedCount >= 2,
                'missed_count'  => 0,
            ];
        }

        // // @TODO: cache this with a cronjob
        // $lastBlocks = Block::query()
        //     ->distinct('generator_public_key')
        //     ->whereIn('generator_public_key', $delegates->pluck('public_key'))
        //     ->limit(Network::delegateCount())
        //     ->get();

        // $delegates = $delegates->transform(function ($delegate) use ($monitor, $lastBlocks) {
        //     $missedCount = $monitor
        //         ->status($delegate->public_key)
        //         ->filter(fn ($round) => $round === false)
        //         ->count();

        //     return [
        //         'order'         => 1,
        //         'username'      => $delegate->attributes['delegate']['username'],
        //         'forging_at'    => 0,
        //         'last_block'    => $lastBlock = $lastBlocks->firstWhere('generator_public_key', $delegate->public_key),
        //         // Status
        //         'is_success'    => $missedCount === 0,
        //         'is_warning'    => $missedCount === 1,
        //         'is_danger'     => $missedCount >= 2,
        //         'missed_count'  => NumberFormatter::number(abs(NetworkStatus::height() - $lastBlock->height->toNumber())),
        //     ];
        // });

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
