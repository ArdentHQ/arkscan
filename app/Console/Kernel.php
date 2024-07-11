<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\BuildForgingStats;
use App\Console\Commands\CacheAddressStatistics;
use App\Console\Commands\CacheAnnualStatistics;
use App\Console\Commands\CacheBlocks;
use App\Console\Commands\CacheCurrenciesData;
use App\Console\Commands\CacheDelegateAggregates;
use App\Console\Commands\CacheDelegatePerformance;
use App\Console\Commands\CacheDelegateProductivity;
use App\Console\Commands\CacheDelegateResignationIds;
use App\Console\Commands\CacheDelegateStatistics;
use App\Console\Commands\CacheDelegatesWithVoters;
use App\Console\Commands\CacheDelegateUsernames;
use App\Console\Commands\CacheDelegateVoterCounts;
use App\Console\Commands\CacheDelegateWallets;
use App\Console\Commands\CacheFees;
use App\Console\Commands\CacheMarketDataStatistics;
use App\Console\Commands\CacheMultiSignatureAddresses;
use App\Console\Commands\CacheNetworkAggregates;
use App\Console\Commands\CachePrices;
use App\Console\Commands\CacheTransactions;
use App\Console\Commands\CacheVolume;
use App\Console\Commands\FetchExchangesDetails;
use App\Console\Commands\GenerateVoteReport;
use App\Console\Commands\LoadExchanges;
use App\Console\Commands\ScoutIndexModels;
use App\Facades\Network;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

final class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(CachePrices::class)->everyMinute();

        $schedule->command(CacheCurrenciesData::class)->everyMinute()->withoutOverlapping();

        $schedule->command(CacheVolume::class)->hourly();

        $schedule->command(CacheDelegateWallets::class)->everyTenMinutes();

        $schedule->command(CacheDelegateVoterCounts::class)->everyTenMinutes();

        $schedule->command(CacheDelegateAggregates::class)->everyMinute();

        $schedule->command(CacheFees::class)->everyFiveMinutes();

        $schedule->command(CacheDelegateUsernames::class)->everyMinute();

        $schedule->command(CacheMultiSignatureAddresses::class)->everyMinute();

        $schedule->command(CacheDelegatesWithVoters::class)->everyMinute();

        $schedule->command(CacheDelegateResignationIds::class)->everyMinute();

        $schedule->command(CacheNetworkAggregates::class)->everyMinute();

        $schedule->command(BuildForgingStats::class)->everyMinute();

        $schedule->command(CacheDelegatePerformance::class)->everyMinute();

        $schedule->command(CacheDelegateProductivity::class)->everyMinute();

        $schedule->command(CacheTransactions::class)->everyFiveMinutes();

        $schedule->command(CacheBlocks::class)->everyFiveMinutes();

        $schedule->command(CacheAddressStatistics::class)->everyFiveMinutes();

        $schedule->command(CacheDelegateStatistics::class)->everyFiveMinutes();

        $schedule->command(CacheMarketDataStatistics::class)->everyFiveMinutes();

        $schedule->command(CacheAnnualStatistics::class)->everyFiveMinutes();

        $schedule->command(GenerateVoteReport::class)->everyFiveMinutes();

        $schedule->command('view:clear-expired')->hourly();

        $schedule->command(LoadExchanges::class)->daily();

        if (Network::canBeExchanged()) {
            $schedule->command(FetchExchangesDetails::class)->everyMinute();
        }

        if (config('arkscan.scout.run_jobs', false) === true) {
            $schedule->command(ScoutIndexModels::class)->everyMinute();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
