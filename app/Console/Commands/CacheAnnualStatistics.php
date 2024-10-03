<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\TransactionTypeEnum;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class CacheAnnualStatistics extends Command
{
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
        $transactionData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP((transactions.timestamp) / 1000)) AS year'),
                DB::raw('COUNT(DISTINCT(transactions.id)) AS transactions'),
                DB::raw(sprintf('SUM(amount) / 1e%d AS amount', config('currencies.decimals.crypto', 18))),
                DB::raw(sprintf('SUM(fee) / 1e%d AS fees', config('currencies.decimals.crypto', 18))),
            ])
            ->from('transactions')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $multipaymentData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP((transactions.timestamp) / 1000)) AS year'),
                DB::raw(sprintf('SUM((payment->>\'amount\')::numeric) / 1e%d AS amount', config('currencies.decimals.crypto', 18))),
            ])
            ->fromRaw('transactions LEFT JOIN LATERAL jsonb_array_elements(asset->\'payments\') AS payment on true')
            ->where('transactions.type', '=', TransactionTypeEnum::MULTI_PAYMENT)
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

        $transactionData->each(function ($item, $key) use ($blocksData, $multipaymentData, $cache) {
            $multipaymentAmount = $multipaymentData->first(function ($value) use ($item) {
                return $value->year === $item->year;
            })?->amount ?? '0';

            $cache->setAnnualData(
                (int) $item->year,
                (int) $item->transactions,
                BigNumber::new($item->amount)->plus($multipaymentAmount)->__toString(),
                $item->fees,
                $blocksData->get($key)->blocks, // We assume to have the same amount of entries for blocks and transactions (years)
            );
        });
    }

    private function cacheCurrentYear(StatisticsCache $cache): void
    {
        $startOfYear = Carbon::now()->startOfYear()->getTimestampMs();
        $year        = Carbon::now()->year;

        $transactionData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('COUNT(*) as transactions'),
                DB::raw(sprintf('SUM(amount) / 1e%d as amount', config('currencies.decimals.crypto', 18))),
                DB::raw(sprintf('SUM(fee) / 1e%d as fees', config('currencies.decimals.crypto', 18))),
            ])
            ->from('transactions')
            ->where('timestamp', '>=', $startOfYear)
            ->first();

        $multipaymentAmount = DB::connection('explorer')
            ->query()
            ->select(DB::raw(sprintf('SUM((payment->>\'amount\')::numeric) / 1e%d AS amount', config('currencies.decimals.crypto', 18))))
            ->fromRaw('transactions LEFT JOIN LATERAL jsonb_array_elements(asset->\'payments\') AS payment on true')
            ->where('transactions.type', '=', TransactionTypeEnum::MULTI_PAYMENT)
            ->where('timestamp', '>=', $startOfYear)
            ->first()?->amount ?? '0';

        $blocksData = DB::connection('explorer')
            ->query()
            ->from('blocks')
            ->where('timestamp', '>=', $startOfYear)
            ->count();

        $cache->setAnnualData(
            $year,
            // @phpstan-ignore-next-line
            (int) $transactionData?->transactions,
            // @phpstan-ignore-next-line
            BigNumber::new($transactionData?->amount ?? '0')->plus($multipaymentAmount)->__toString(),
            // @phpstan-ignore-next-line
            $transactionData?->fees ?? '0',
            $blocksData,
        );
    }
}
