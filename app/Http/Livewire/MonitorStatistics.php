<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Aggregates\DailyFeeAggregate;
use App\Aggregates\VoteCountAggregate;
use App\Aggregates\VotePercentageAggregate;
use App\Facades\Network;
use App\Models\Scopes\DelegateRegistrationScope;
use App\Models\Transaction;
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

    private function delegateRegistrations(): int
    {
        return (int) Cache::remember('delegateRegistrations', Network::blockTime(), function (): int {
            return Transaction::withScope(DelegateRegistrationScope::class)->count();
        });
    }

    private function feesCollected(): string
    {
        return Cache::remember('feesCollected', Network::blockTime(), function (): string {
            return (new DailyFeeAggregate())->aggregate();
        });
    }

    private function votes(): string
    {
        return Cache::remember('votes', Network::blockTime(), function (): string {
            return (new VoteCountAggregate())->aggregate();
        });
    }

    private function votesPercentage(): string
    {
        return Cache::remember('votesPercentage', Network::blockTime(), function (): string {
            return (new VotePercentageAggregate())->aggregate();
        });
    }
}
