<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Enums\CoreTransactionTypeEnum;
use App\Events\Statistics\AnnualData;
use App\Facades\Network;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
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
    protected $signature = 'explorer:cache-annual-statistics {--all}}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache expensive annual statistics, use --all for the first run';

    public function handle(StatisticsCache $cache): void
    {
        if ($this->option('all')) {
            $this->cacheAllYears($cache);
        } else {
            $this->cacheCurrentYear($cache);
        }
    }

    private function cacheAllYears(StatisticsCache $cache): void
    {
        $epoch           = Network::epoch()->timestamp;
        $transactionData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP(transactions.timestamp + '.$epoch.')) AS year'),
                DB::raw('COUNT(DISTINCT(transactions.id)) AS transactions'),
                DB::raw('SUM(amount) / 1e8 AS amount'),
                DB::raw('SUM(fee) / 1e8 AS fees'),
            ])
            ->from('transactions')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $multipaymentData = DB::connection('explorer')
                ->query()
                ->select([
                    DB::raw('DATE_PART(\'year\', TO_TIMESTAMP(transactions.timestamp + '.$epoch.')) AS year'),
                    DB::raw('SUM((payment->>\'amount\')::bigint) / 1e8 AS amount'),
                ])
                ->fromRaw('transactions LEFT JOIN LATERAL jsonb_array_elements(asset->\'payments\') AS payment on true')
                ->where('transactions.type', '=', CoreTransactionTypeEnum::MULTI_PAYMENT)
                ->groupBy('year')
                ->orderBy('year')
                ->get();

        $blocksData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP(blocks.timestamp + '.$epoch.')) AS year'),
                DB::raw('COUNT(*) as blocks'),
            ])
            ->from('blocks')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $transactionData->each(function ($item, $key) use ($blocksData, $multipaymentData, $cache) {
            // Find corresponding multipayment amount
            $multipaymentAmount = $multipaymentData->first(function ($value) use ($item) {
                return $value->year === $item->year;
            })?->amount ?? '0';

            $volume = BigNumber::new($item->amount)->plus($multipaymentAmount)->__toString();

            if (! $this->hasChanges) {
                $existingData = $cache->getAnnualData((int) $item->year) ?? [];
                if (Arr::get($existingData, 'transactions') !== $item->transactions) {
                    $this->hasChanges = true;
                }

                if (! $this->hasChanges && Arr::get($existingData, 'volume') !== $volume) {
                    $this->hasChanges = true;
                }

                if (! $this->hasChanges && Arr::get($existingData, 'fees') !== $item->fees) {
                    $this->hasChanges = true;
                }

                if (! $this->hasChanges && Arr::get($existingData, 'blocks') !== $blocksData->get($key)->blocks) {
                    $this->hasChanges = true;
                }
            }

            $cache->setAnnualData(
                (int) $item->year,
                (int) $item->transactions,
                $volume,
                $item->fees,
                $blocksData->get($key)->blocks, // We assume to have the same amount of entries for blocks and transactions (years)
            );
        });

        $this->dispatchEvent(AnnualData::class);
    }

    private function cacheCurrentYear(StatisticsCache $cache): void
    {
        $epoch       = (int) Network::epoch()->timestamp;
        $startOfYear = (int) Carbon::now()->startOfYear()->timestamp;
        $year        = Carbon::now()->year;

        /** @var ?object{transactions: int, amount: int, volume: string, fees: float} $transactionData */
        $transactionData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(amount) / 1e8 as amount'), // TODO: include multipayments
                DB::raw('SUM(fee) / 1e8 as fees'),
            ])
            ->from('transactions')
            ->where('timestamp', '>=', $startOfYear - $epoch)
            ->first();

        $multipaymentAmount = DB::connection('explorer')
            ->query()
            ->select(DB::raw('SUM((payment->>\'amount\')::bigint) / 1e8 AS amount'))
            ->fromRaw('transactions LEFT JOIN LATERAL jsonb_array_elements(asset->\'payments\') AS payment on true')
            ->where('transactions.type', '=', CoreTransactionTypeEnum::MULTI_PAYMENT)
            ->where('timestamp', '>=', $startOfYear - $epoch)
            ->first()?->amount ?? '0';

        $blocksData = DB::connection('explorer')
            ->query()
            ->from('blocks')
            ->where('timestamp', '>=', $startOfYear - $epoch)
            ->count();

        $transactionCount = (int) $transactionData?->transactions;
        $volume           = BigNumber::new($transactionData?->amount ?? '0')->plus($multipaymentAmount)->__toString();
        $fees             = (string) ($transactionData?->fees ?? '0');

        $hasUpdated   = false;
        $existingData = $cache->getAnnualData($year) ?? [];
        if (Arr::get($existingData, 'transactions') !== $transactionCount) {
            $hasUpdated = true;
        }

        if (! $hasUpdated && Arr::get($existingData, 'volume') !== $volume) {
            $hasUpdated = true;
        }

        if (! $hasUpdated && Arr::get($existingData, 'fees') !== $fees) {
            $hasUpdated = true;
        }

        if (! $hasUpdated && Arr::get($existingData, 'blocks') !== $blocksData) {
            $hasUpdated = true;
        }

        $cache->setAnnualData(
            $year,
            $transactionCount,
            $volume,
            $fees,
            $blocksData,
        );

        if ($hasUpdated) {
            AnnualData::dispatch();
        }
    }
}
