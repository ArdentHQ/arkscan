<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\CacheDelegateAggregates;
use App\Console\Commands\CacheDelegatePerformance;
use App\Console\Commands\CacheDelegateProductivity;
use App\Console\Commands\CacheDelegateResignationIds;
use App\Console\Commands\CacheDelegatesWithVoters;
use App\Console\Commands\CacheDelegateUsernames;
use App\Console\Commands\CacheDelegateVoterCounts;
use App\Console\Commands\CacheDelegateWallets;
use App\Console\Commands\CacheFees;
use App\Console\Commands\CacheLastBlocks;
use App\Console\Commands\CacheMultiSignatureAddresses;
use App\Console\Commands\CacheNetworkStatus;
use App\Console\Commands\CachePrices;
use App\Facades\Network;
use App\Jobs\CacheMarketSquareProfileByAddress;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\ShortSchedule\ShortSchedule;

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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(CachePrices::class)->everyThirtyMinutes();

        $schedule->command(CacheDelegateWallets::class)->everyTenMinutes();

        $schedule->command(CacheDelegateVoterCounts::class)->everyTenMinutes();

        $schedule->command(CacheDelegateAggregates::class)->everyFiveMinutes();

        $schedule->command(CacheNetworkStatus::class)->everyMinute();

        $schedule->command(CacheFees::class)->everyMinute();

        $schedule->command(CacheDelegateUsernames::class)->everyMinute();

        $schedule->command(CacheMultiSignatureAddresses::class)->everyMinute();

        $schedule->command(CacheLastBlocks::class)->everyMinute();

        $schedule->command(CacheDelegatesWithVoters::class)->everyMinute();

        $schedule->command(CacheDelegatePerformance::class)->everyMinute();

        $schedule->command(CacheDelegateProductivity::class)->everyMinute();

        $schedule->command(CacheDelegateResignationIds::class)->everyMinute();

        $schedule->command(CacheMarketSquareProfileByAddress::class)->everyMinute();
    }

    /**
     * Define the application's command short schedule.
     *
     * @param ShortSchedule $shortSchedule
     *
     * @return void
     */
    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
        $shortSchedule->command('explorer:cache-last-blocks')->everySeconds(Network::blockTime());

        $shortSchedule->command('explorer:cache-network-status')->everySeconds(Network::blockTime());
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
