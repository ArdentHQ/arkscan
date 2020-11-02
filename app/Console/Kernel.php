<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\CacheDelegateAggregates;
use App\Console\Commands\CacheDelegates;
use App\Console\Commands\CacheExchangeRates;
use App\Console\Commands\CacheFeeChart;
use App\Console\Commands\CacheLastBlocks;
use App\Console\Commands\CacheMultiSignatureAddresses;
use App\Console\Commands\CacheNetworkStatistics;
use App\Console\Commands\CachePastRoundPerformance;
use App\Console\Commands\CacheUsernames;
use App\Console\Commands\CacheVotes;
use App\Facades\Network;
use App\Jobs\CacheProductivityByPublicKey;
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
        $schedule->command(CacheExchangeRates::class)->everyThirtyMinutes();

        $schedule->command(CacheDelegates::class)->everyTenMinutes();

        $schedule->command(CacheDelegateAggregates::class)->everyFiveMinutes();

        $schedule->command(CacheFeeChart::class)->everyMinute();

        $schedule->command(CacheUsernames::class)->everyMinute();

        $schedule->command(CacheNetworkStatistics::class)->everyMinute();

        $schedule->command(CacheMultiSignatureAddresses::class)->everyMinute();

        $schedule->command(CacheLastBlocks::class)->everyMinute();

        $schedule->command(CacheVotes::class)->everyMinute();

        $schedule->command(CachePastRoundPerformance::class)->everyMinute();

        $schedule->command(CacheProductivityByPublicKey::class)->everyMinute();
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
        $shortSchedule->command(CacheLastBlocks::class)->everySeconds(Network::blockTime());
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
