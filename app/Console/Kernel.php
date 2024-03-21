<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\BuildForgingStats;
use App\Console\Commands\CacheAddressStatistics;
use App\Console\Commands\CacheAnnualStatistics;
use App\Console\Commands\CacheBlocks;
use App\Console\Commands\CacheCurrenciesData;
use App\Console\Commands\CacheValidatorAggregates;
use App\Console\Commands\CacheValidatorPerformance;
use App\Console\Commands\CacheValidatorProductivity;
use App\Console\Commands\CacheValidatorResignationIds;
use App\Console\Commands\CacheValidatorStatistics;
use App\Console\Commands\CacheValidatorsWithVoters;
use App\Console\Commands\CacheValidatorUsernames;
use App\Console\Commands\CacheValidatorVoterCounts;
use App\Console\Commands\CacheValidatorWallets;
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

        $schedule->command(CacheValidatorWallets::class)->everyMinute();

        $schedule->command(CacheValidatorVoterCounts::class)->everyTenMinutes();

        $schedule->command(CacheValidatorAggregates::class)->everyMinute();

        $schedule->command(CacheFees::class)->everyFiveMinutes();

        $schedule->command(CacheValidatorUsernames::class)->everyMinute();

        $schedule->command(CacheMultiSignatureAddresses::class)->everyMinute();

        $schedule->command(CacheValidatorsWithVoters::class)->everyMinute();

        $schedule->command(CacheValidatorResignationIds::class)->everyMinute();

        $schedule->command(CacheNetworkAggregates::class)->everyMinute();

        $schedule->command(BuildForgingStats::class)->everyMinute();

        $schedule->command(CacheValidatorPerformance::class)->everyMinute();

        $schedule->command(CacheValidatorProductivity::class)->everyMinute();

        $schedule->command(CacheTransactions::class)->everyFiveMinutes();

        $schedule->command(CacheBlocks::class)->everyFiveMinutes();

        $schedule->command(CacheAddressStatistics::class)->everyFiveMinutes();

        $schedule->command(CacheValidatorStatistics::class)->everyFiveMinutes();

        $schedule->command(CacheMarketDataStatistics::class)->everyFiveMinutes();

        $schedule->command(CacheAnnualStatistics::class)->everyFiveMinutes();

        $schedule->command(GenerateVoteReport::class)->everyFiveMinutes();

        $schedule->command('view:clear-expired')->hourly();

        $schedule->command(LoadExchanges::class)->daily();

        $schedule->command(FetchExchangesDetails::class)->hourly();

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
