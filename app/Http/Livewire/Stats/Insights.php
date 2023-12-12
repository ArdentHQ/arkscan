<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats;

use App\Enums\StatsTransactionType;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\BlockCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\NumberFormatter;
use App\ViewModels\BlockViewModel;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;
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
            'delegateDetails'     => $this->delegateDetails(),
            'addressHoldings'     => $this->addressHoldings(),
            'uniqueAddresses'     => $this->uniqueAddresses(),
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

    private function delegateDetails(): array
    {
        $cache = new StatisticsCache();

        $mostUniqueVoters = Wallet::firstWhere('public_key', $cache->getMostUniqueVoters());
        if ($mostUniqueVoters !== null) {
            $mostUniqueVoters = ViewModelFactory::make($mostUniqueVoters);
        }

        $leastUniqueVoters = Wallet::firstWhere('public_key', $cache->getLeastUniqueVoters());
        if ($leastUniqueVoters !== null) {
            $leastUniqueVoters = ViewModelFactory::make($leastUniqueVoters);
        }

        $oldestActiveDelegateData = $cache->getOldestActiveDelegate();
        if (count($oldestActiveDelegateData) > 0) {
            $oldestActiveDelegate = Wallet::firstWhere('public_key', $oldestActiveDelegateData['publicKey']);
            $oldestActiveDelegate = [
                'delegate' => ViewModelFactory::make($oldestActiveDelegate),
                'value' => Carbon::createFromTimestamp(Network::epoch()->timestamp + $oldestActiveDelegateData['timestamp'])->format(DateFormat::DATE)
            ];
        }

        $newestActiveDelegateData = $cache->getNewestActiveDelegate();
        if (count($newestActiveDelegateData) > 0) {
            $newestActiveDelegate = Wallet::firstWhere('public_key', $newestActiveDelegateData['publicKey']);
            $newestActiveDelegate = [
                'delegate' => ViewModelFactory::make($newestActiveDelegate),
                'value' => Carbon::createFromTimestamp(Network::epoch()->timestamp + $newestActiveDelegateData['timestamp'])->format(DateFormat::DATE)
            ];
        }

        $mostBlocksForged = Wallet::firstWhere('public_key', $cache->getMostBlocksForged());
        if ($mostBlocksForged !== null) {
            $mostBlocksForged = ViewModelFactory::make($mostBlocksForged);
        }

        return [
            'most_unique_voters'     => $mostUniqueVoters,
            'least_unique_voters'    => $leastUniqueVoters,
            'oldest_active_delegate' => $oldestActiveDelegate,
            'newest_active_delegate' => $newestActiveDelegate,
            'most_blocks_forged'     => $mostBlocksForged,
        ];
    }

    private function addressHoldings(): array
    {
        $statisticsCache = new StatisticsCache();
        $holdings        = $statisticsCache->getAddressHoldings();

        unset($holdings['0']); // Ignore wallets below 1

        // Create new array with summed values instead of pure counts
        $previousValue = 0;
        $summedValues  = [];
        foreach (array_reverse($holdings) as $key => $values) {
            array_unshift($summedValues, ['grouped' => $values['grouped'], 'count' => $values['count'] + $previousValue]);
            $previousValue = $values['count'] + $previousValue;
        }

        return $summedValues;
    }

    private function uniqueAddresses(): array
    {
        $statisticsCache = new StatisticsCache();

        $genesis          = $statisticsCache->getGenesisAddress();
        $newest           = $statisticsCache->getNewestAddress();
        $mostTransactions = $statisticsCache->getMostTransactions();
        $largest          = $statisticsCache->getLargestAddress();

        return [
            'genesis'           => $genesis,
            'newest'            => $newest,
            'most_transactions' => $mostTransactions,
            'largest'           => $largest,
        ];
    }
}
