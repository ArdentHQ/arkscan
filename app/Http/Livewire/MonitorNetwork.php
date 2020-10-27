<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Block;
use App\Services\Blockchain\NetworkStatus;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\Monitor;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorNetwork extends Component
{
    public function render(): View
    {
        // $tracking = DelegateTracker::execute(Monitor::roundDelegates(112168));
        $tracking = DelegateTracker::execute(Monitor::activeDelegates(Monitor::roundNumber()));

        $delegates = [];

        for ($i = 0; $i < count($tracking); $i++) {
            $delegate = array_values($tracking)[$i];

            $delegates[] = [
                'order'         => $i + 1,
                'wallet'        => ViewModelFactory::make(Cache::tags(['delegates'])->get($delegate['publicKey'])),
                'forging_at'    => Carbon::now()->addMilliseconds($delegate['time']),
                'last_block'    => Cache::get('lastBlock:'.$delegate['publicKey']),
                // Status
                'is_success'    => false, // $missedCount === 0,
                'is_warning'    => false, // $missedCount === 1,
                'is_danger'     => false, // $missedCount >= 2,
                'missed_count'  => 0,
            ];
        }

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
}
