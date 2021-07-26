<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Http\Livewire\Concerns\ChartNumberFormatters;
use App\Services\Cache\FeeCache;
use App\Services\Forms;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class InsightCurrentAverageFee extends Component
{
    use ChartNumberFormatters;

    public string $transactionType = 'transfer';

    public string $refreshInterval = '';

    public function mount(): void
    {
        $this->refreshInterval = (string) config('explorer.statistics.refreshInterval', '60');
    }

    public function render(): View
    {
        $transactionOptions = collect(Forms::getTransactionOptions())->except(['all', 'timelockClaim', 'timelockRefund'])->toArray();

        return view('livewire.stats.insight-current-average-fee', [
            'currentAverageFeeTitle' => trans('pages.statistics.insights.current-average-fee', [
                'type' => $transactionOptions[$this->transactionType],
            ]),
            'currentAverageFeeValue' => $this->currentAverageFee($this->transactionType),
            'minFeeTitle'            => trans('pages.statistics.insights.min-fee'),
            'minFeeValue'            => $this->minFee($this->transactionType),
            'maxFeeTitle'            => trans('pages.statistics.insights.max-fee'),
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
