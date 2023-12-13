<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CoreTransactionTypeEnum;
use App\Facades\Network;
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
        $epoch       = (int) Network::epoch()->timestamp;
        $startOfYear = (int) Carbon::now()->startOfYear()->timestamp;
        $year        = Carbon::now()->year;

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
