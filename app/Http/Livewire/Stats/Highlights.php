<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Facades\Network;
use App\Http\Controllers\Concerns\WithStatistics;
use App\Services\Cache\NetworkCache;
use App\Services\NumberFormatter;
use Illuminate\View\View;
use Livewire\Component;

final class Highlights extends Component
{
    use WithStatistics;

    public string $refreshInterval = '';

    public string $currency = '';

    public function mount(): void
    {
        $this->currency        = Network::currency();
        $this->refreshInterval = (string) config('arkscan.statistics.refreshInterval', '60');
    }

    public function render(): View
    {
        return view('livewire.stats.highlights', [
            'votingPercent'   => $this->getVotingPercent(),
            'votingValue'     => $this->getVotingValue(),
            'totalSupply'     => $this->getTotalSupply(),
            'validators'      => $this->getValidators(),
            'wallets'         => $this->getWallets(),
            'refreshInterval' => $this->refreshInterval,
        ]);
    }

    private function getValidators(): string
    {
        $registeredValidators = (new NetworkCache())->getValidatorRegistrationCount();

        return NumberFormatter::number($registeredValidators);
    }
}
