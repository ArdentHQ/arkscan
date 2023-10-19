<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkSupply;
use App\Facades\Network;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\NetworkCache;
use App\Services\NumberFormatter;
use Illuminate\View\View;
use Livewire\Component;

final class StatsHighlights extends Component
{
    public string $refreshInterval = '';

    public string $currency = '';

    public function mount(): void
    {
        $this->currency        = Network::currency();
        $this->refreshInterval = (string) config('arkscan.statistics.refreshInterval', '60');
    }

    public function render(): View
    {
        return view('livewire.stats-highlights', [
            'votingPercent'   => $this->getVotingPercent(),
            'votingValue'     => $this->getVotingValue(),
            'totalSupply'     => $this->getTotalSupply(),
            'delegates'       => $this->getDelegates(),
            'wallets'         => $this->getWallets(),
            'refreshInterval' => $this->refreshInterval,
        ]);
    }

    private function getTotalSupply(): string
    {
        $supply = CacheNetworkSupply::execute() / 1e8;

        return NumberFormatter::number($supply);
    }

    private function getVotingPercent(): string
    {
        $votesPercent = (new NetworkCache())->getVotesPercentage();

        return NumberFormatter::percentage($votesPercent);
    }

    private function getVotingValue(): float
    {
        return (new DelegateCache())->getTotalVoted()[1];
    }

    private function getDelegates(): string
    {
        $registeredDelegates = (new NetworkCache())->getDelegateRegistrationCount();

        return NumberFormatter::number($registeredDelegates);
    }

    private function getWallets(): string
    {
        $wallets = Wallet::count();

        return NumberFormatter::number($wallets);
    }
}
