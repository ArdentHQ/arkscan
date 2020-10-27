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
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorStatistics extends Component
{
    public function render(): View
    {
        return view('livewire.monitor-statistics', [
            'delegateRegistrations' => $this->delegateRegistrations(),
            'blockReward'           => Network::blockReward(),
            'feesCollected'         => $this->feesCollected(),
            'votes'                 => $this->votes(),
            'votesPercentage'       => $this->votesPercentage(),
        ]);
    }

    private function delegateRegistrations(): string
    {
        return Cache::remember('delegateRegistrations', Network::blockTime(), function (): string {
            return NumberFormatter::number(Transaction::withScope(DelegateRegistrationScope::class)->count());
        });
    }

    private function feesCollected(): string
    {
        return Cache::remember('feesCollected', Network::blockTime(), function (): string {
            return NumberFormatter::currency((new DailyFeeAggregate())->aggregate(), Network::currency());
        });
    }

    private function votes(): string
    {
        return Cache::remember('votes', Network::blockTime(), function (): string {
            return NumberFormatter::currency((new VoteCountAggregate())->aggregate(), Network::currency());
        });
    }

    private function votesPercentage(): string
    {
        return Cache::remember('votesPercentage', Network::blockTime(), function (): string {
            return NumberFormatter::percentage((new VotePercentageAggregate())->aggregate());
        });
    }
}
