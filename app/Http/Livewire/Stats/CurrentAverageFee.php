<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Http\Livewire\Concerns\ChartNumberFormatters;
use App\Services\Cache\FeeCache;
use App\Services\Forms;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class CurrentAverageFee extends Component
{
    use ChartNumberFormatters;

    public string $transactionType = 'transfer';

    public string $refreshInterval = '';

    public function mount(): void
    {
        $this->refreshInterval = (string) config('arkscan.statistics.refreshInterval', '60');
    }

    public function render(): View
    {
        $transactionOptions = collect(Forms::getTransactionOptions())
            ->except(['all'])
            ->toArray();

        return view('livewire.stats.current-average-fee', [
            'currentAverageFeeTitle' => trans('pages.statistics.information-cards.current-average-fee', [
                'type' => $transactionOptions[$this->transactionType],
            ]),
            'currentAverageFeeValue' => $this->currentAverageFee($this->transactionType),
            'minFeeTitle'            => trans('pages.statistics.information-cards.min-fee'),
            'minFeeValue'            => $this->minFee($this->transactionType),
            'maxFeeTitle'            => trans('pages.statistics.information-cards.max-fee'),
            'maxFeeValue'            => $this->maxFee($this->transactionType),
            'options'                => $transactionOptions,
            'refreshInterval'        => $this->refreshInterval,
        ]);
    }

    private function currentAverageFee(string $transactionType): string
    {
        $fee = $this->getFeesAggregatesPerType($transactionType);

        return $this->asMoney($fee->get('avg', 0));
    }

    private function minFee(string $transactionType): string
    {
        $fee = $this->getFeesAggregatesPerType($transactionType);

        return $this->asMoney($fee->get('min', 0));
    }

    private function maxFee(string $transactionType): string
    {
        $fee = $this->getFeesAggregatesPerType($transactionType);

        return $this->asMoney($fee->get('max', 0));
    }

    private function getFeesAggregatesPerType(string $transactionType): Collection
    {
        $fee = (new FeeCache());

        return collect([
            'avg' => $fee->getAverage($transactionType),
            'min' => $fee->getMinimum($transactionType),
            'max' => $fee->getMaximum($transactionType),
        ]);
    }
}
