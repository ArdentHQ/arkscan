<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsTransactionType;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\BlockCache;
use App\Services\Cache\Statistics;
use App\Services\Cache\TransactionCache;
use App\Services\NumberFormatter;
use App\ViewModels\BlockViewModel;
use App\ViewModels\TransactionViewModel;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

final class Insights extends Component
{
    public function render(): View
    {
        $transactionCache = new TransactionCache();

        return view('livewire.stats.insights', [
            'transactionDetails'  => $this->transactionDetails($transactionCache),
            'transactionAverages' => $this->transactionAverages($transactionCache),
            'transactionRecords'  => $this->transactionRecords($transactionCache),
            'addressHoldings' => $this->addressHoldings(),
        ]);
    }

    private function transactionDetails(TransactionCache $cache): array
    {
        return StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $cache->getHistoricalByType($type)])
            ->toArray();
    }

    private function transactionAverages(TransactionCache $cache): array
    {
        $data = $cache->getHistoricalAverages();

        return [
            'transactions'       => $data['count'],
            'transaction_volume' => NumberFormatter::currency($data['amount'], Network::currency()),
            'transaction_fees'   => NumberFormatter::currency($data['fee'], Network::currency()),
        ];
    }

    private function transactionRecords(TransactionCache $transactionCache): array
    {
        $blockCache = new BlockCache();

        $largestTransaction = Transaction::find($transactionCache->getLargestIdByAmount());
        if ($largestTransaction !== null) {
            $largestTransaction = new TransactionViewModel($largestTransaction);
        }

        $largestBlock = Block::find($blockCache->getLargestIdByAmount());
        if ($largestBlock !== null) {
            $largestBlock = new BlockViewModel($largestBlock);
        }

        $blockWithHighestFees = Block::find($blockCache->getLargestIdByFees());
        if ($blockWithHighestFees !== null) {
            $blockWithHighestFees = new BlockViewModel($blockWithHighestFees);
        }

        $blockWithMostTransactions = Block::find($blockCache->getLargestIdByTransactionCount());
        if ($blockWithMostTransactions !== null) {
            $blockWithMostTransactions = new BlockViewModel($blockWithMostTransactions);
        }

        return [
            'largest_transaction'        => $largestTransaction,
            'largest_block'              => $largestBlock,
            'highest_fee'                => $blockWithHighestFees,
            'most_transactions_in_block' => $blockWithMostTransactions,
        ];
    }

    private function addressHoldings(): array
    {
        $statisticsCache = new Statistics();
        $holdings = $statisticsCache->getAddressHoldings();

        unset($holdings['0']); // Ignore wallets below 1

        // Create new array with summed values instead of pure counts
        $previousValue = 0;
        $summedValues = [];
        foreach(array_reverse($holdings) as $key => $values) {
            array_unshift($summedValues, ['grouped' => $values['grouped'], 'count' => $values['count'] + $previousValue]);
            $previousValue = $values['count'] + $previousValue;
        }

        return $summedValues;
    }
}
