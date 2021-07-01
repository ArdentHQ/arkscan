<?php

declare(strict_types=1);

use App\Enums\StatsPeriods;
use App\Http\Livewire\Stats\InsightAllTimeFeesCollected;
use App\Models\Transaction;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    Carbon::setTestNow('2021-01-01 00:00:00');
});

it('should render the component', function () {
    Transaction::factory(10)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->unix()]);
    Transaction::factory(15)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('1 hour')->unix()]);
    Transaction::factory(20)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('2 hours')->unix()]);
    Transaction::factory(25)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('3 hours')->unix()]);
    Transaction::factory(30)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('4 hours')->unix()]);
    Transaction::factory(35)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('25 hours')->unix()]);

    Artisan::call('explorer:cache-fees');

    Livewire::test(InsightAllTimeFeesCollected::class)
        ->set('period', StatsPeriods::DAY)
        ->assertSee(trans('pages.statistics.insights.all-time-fees-collected'))
        ->assertSee('16,666.6665285 DARK')
        ->assertSee(trans('pages.statistics.insights.fees'))
        ->assertSee('12,345.67891 DARK')
        ->assertSee('[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,3703.703673,3086.4197275,2469.135782,1851.8518365,1234.567891]');
});

it('should filter by year', function () {
    Transaction::factory(10)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->unix()]);
    Transaction::factory(15)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('1 month')->unix()]);
    Transaction::factory(20)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('2 months')->unix()]);
    Transaction::factory(25)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('3 months')->unix()]);
    Transaction::factory(30)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('4 months')->unix()]);
    Transaction::factory(35)->create(['fee' => 12345678910, 'timestamp' => Timestamp::now()->sub('13 months')->unix()]);

    Artisan::call('explorer:cache-fees');

    Livewire::test(InsightAllTimeFeesCollected::class)
        ->set('period', StatsPeriods::YEAR)
        ->assertSee(trans('pages.statistics.insights.all-time-fees-collected'))
        ->assertSee('16,666.6665285 DARK')
        ->assertSee(trans('pages.statistics.insights.fees'))
        ->assertSee('12,345.67891 DARK')
        ->assertSee('[0,0,0,0,0,0,0,3703.703673,3086.4197275,2469.135782,1851.8518365,1234.567891]');
});
