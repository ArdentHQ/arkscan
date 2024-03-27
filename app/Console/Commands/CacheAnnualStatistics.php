<?php

declare(strict_types=1);

namespace App\Console\Commands;

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
        $epoch           = (float) Network::epoch()->timestamp * 1000;
        $transactionData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP((transactions.timestamp + '.$epoch.') / 1000)) AS year'),
                DB::raw('COUNT(DISTINCT(transactions.id)) AS transactions'),
                DB::raw('SUM(amount) / 1e8 AS amount'),
                DB::raw('SUM(fee) / 1e8 AS fees'),
            ])
            ->from('transactions')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $blocksData = DB::connection('explorer')
            ->query()
            ->select([
                DB::raw('DATE_PART(\'year\', TO_TIMESTAMP((blocks.timestamp + '.$epoch.') / 1000)) AS year'),
                DB::raw('COUNT(*) as blocks'),
            ])
            ->from('blocks')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $transactionData->each(function ($item, $key) use ($blocksData, $cache) {
            $cache->setAnnualData(
                (int) $item->year,
                (int) $item->transactions,
                BigNumber::new($item->amount)->__toString(),
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
                DB::raw('SUM(amount) / 1e8 as amount'),
                DB::raw('SUM(fee) / 1e8 as fees'),
            ])
            ->from('transactions')
            ->where('timestamp', '>=', ($startOfYear - $epoch) * 1000)
            ->first();

        $blocksData = DB::connection('explorer')
            ->query()
            ->from('blocks')
            ->where('timestamp', '>=', ($startOfYear - $epoch) * 1000)
            ->count();

        $cache->setAnnualData(
            $year,
            // @phpstan-ignore-next-line
            (int) $transactionData?->transactions,
            // @phpstan-ignore-next-line
            BigNumber::new($transactionData?->amount ?? '0')->__toString(),
            // @phpstan-ignore-next-line
            $transactionData?->fees ?? '0',
            $blocksData,
        );
    }
}
