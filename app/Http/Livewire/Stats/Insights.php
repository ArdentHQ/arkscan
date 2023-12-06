<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsTransactionType;
use App\Services\Cache\TransactionCache;
use Illuminate\View\View;
use Livewire\Component;

final class Insights extends Component
{
    public function render(): View
    {
        return view('livewire.stats.insights', [
            'transactionDetails' => $this->transactionDetails(),
        ]);
    }

    private function transactionDetails(): array
    {
        $transactionCache = new TransactionCache();

        return StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $transactionCache->getHistoricalByType($type)])
            ->toArray();
    }
}
