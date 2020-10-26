<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Aggregates\DailyFeeAggregate;
use App\Aggregates\VoteCountAggregate;
use App\Aggregates\VotePercentageAggregate;
use App\Facades\Network;
use App\Models\Scopes\DelegateRegistrationScope;
use App\Models\Transaction;
use App\Services\NumberFormatter;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorStatistics extends Component
{
    public function render(): View
    {
        return view('livewire.monitor-statistics', [
            'delegateRegistrations' => NumberFormatter::number(Transaction::withScope(DelegateRegistrationScope::class)->count()), // @TODO: cache
            'blockReward'           => Network::blockReward(),
            'feesCollected'         => NumberFormatter::currency((new DailyFeeAggregate())->aggregate(), Network::currency()), // @TODO: cache
            'votes'                 => NumberFormatter::currency((new VoteCountAggregate())->aggregate(), Network::currency()), // @TODO: cache
            'votesPercentage'       => NumberFormatter::percentage((new VotePercentageAggregate())->aggregate()), // @TODO: cache
        ]);
    }
}
