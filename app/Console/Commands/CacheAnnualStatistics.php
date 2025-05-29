<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Events\Statistics\AnnualData;
use App\Facades\Network;
use App\Models\Scopes\MultiPaymentTotalAmountScope;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class CacheAnnualStatistics extends Command
{
    use DispatchesStatisticsEvents;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-annual-statistics {--all}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive annual statistics, use --all for the first run';

    public function handle(StatisticsCache $cache): void
    {
        $this->cacheAllYears($cache);
        $this->cacheCurrentYear($cache);

        $this->dispatchEvent(AnnualData::class);
    }

    private function hasCachedAll(StatisticsCache $cache): bool
    {
        return $cache->getAnnualData(Network::epoch()->year) !== null;
    }

    private function cacheAllYears(StatisticsCache $cache): void
    {
        if ($this->hasCachedAll($cache) && $this->option('all') === false) {
            return;
        }

        $transactionData = Transaction::select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP((transactions.timestamp) / 1000)) AS year'),
                DB::raw('COUNT(DISTINCT(transactions.hash)) AS transactions'),
                DB::raw(sprintf('(SUM(value) FILTER (WHERE COALESCE(is_multipayment, FALSE) != TRUE)) / 1e%d AS value', config('currencies.decimals.crypto', 18))),
                DB::raw(sprintf('SUM(gas_price * COALESCE(receipts.gas_used, 0)) AS fees')),
                DB::raw('COALESCE(SUM(recipient_amount), 0) as recipient_value'),
            ])
            ->join('receipts', 'transactions.hash', '=', 'receipts.transaction_hash')
            ->withScope(MultiPaymentTotalAmountScope::class)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $blocksData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP((blocks.timestamp) / 1000)) AS year'),
                DB::raw('COUNT(*) as blocks'),
            ])
            ->from('blocks')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $transactionData->each(function ($item, $key) use ($blocksData, $cache) {
            if (! $this->hasChanges) {
                $existingData = $cache->getAnnualData((int) $item->year) ?? [];
                if (Arr::get($existingData, 'transactions') !== $item->transactions) {
                    $this->hasChanges = true;
                }

                if (! $this->hasChanges && Arr::get($existingData, 'blocks') !== $blocksData->get($key)?->blocks) {
                    $this->hasChanges = true;
                }
            }

            $cache->setAnnualData(
                (int) $item->year,
                (int) $item->transactions,
                (string) $item->value->plus(UnitConverter::formatUnits($item->recipient_value, 'ark')),
                (string) BigNumber::new(UnitConverter::formatUnits($item->fees, 'ark')),
                $blocksData->get($key)?->blocks, // We assume to have the same value of entries for blocks and transactions (years)
            );
        });
    }

    private function cacheCurrentYear(StatisticsCache $cache): void
    {
        $startOfYear = Carbon::now()->startOfYear()->getTimestampMs();
        $year        = Carbon::now()->year;

        /** @var ?object{transactions: int, value: int, volume: string, fees: float} $transactionData */
        $transactionData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('COUNT(*) as transactions'),
                DB::raw(sprintf('SUM(value) / 1e%d as value', config('currencies.decimals.crypto', 18))),
                DB::raw(sprintf('SUM(gas_price * COALESCE(receipts.gas_used, 0)) as fees')),
            ])
            ->from('transactions')
            ->join('receipts', 'transactions.hash', '=', 'receipts.transaction_hash')
            ->where('timestamp', '>=', $startOfYear)
            ->first();

        $blocksData = DB::connection('explorer')
            ->query()
            ->from('blocks')
            ->where('timestamp', '>=', $startOfYear)
            ->count();

        $transactionCount = (int) $transactionData?->transactions;
        $volume           = (string) BigNumber::new($transactionData?->value ?? '0');
        $fees             = (string) ($transactionData?->fees ?? '0');

        if (! $this->hasChanges) {
            $existingData = $cache->getAnnualData($year) ?? [];
            if (Arr::get($existingData, 'transactions', 0) !== $transactionCount) {
                $this->hasChanges = true;
            }

            if (! $this->hasChanges && Arr::get($existingData, 'blocks', 0) !== $blocksData) {
                $this->hasChanges = true;
            }
        }

        $cache->setAnnualData(
            $year,
            $transactionCount,
            $volume,
            (string) BigNumber::new(UnitConverter::formatUnits($fees, 'ark')),
            $blocksData,
        );
    }
}
