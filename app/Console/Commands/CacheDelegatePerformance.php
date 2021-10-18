<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Models\Round;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class CacheDelegatePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-performance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the past performance for each active delegate in the current round.';

    public function handle(): void
    {
        $round = Monitor::roundNumber();

        $query = Round::query()
            ->where('round', $round)
            ->limit(Network::delegateCount())
            ->select([
                'rounds.public_key',
                DB::raw('MAX(rounds.balance) as balance'),
            ])
            ->join('blocks', 'blocks.generator_public_key', '=', 'rounds.public_key');

        collect(range($round - 5, $round - 1))
            ->each(function ($round, int $index) use ($query) : void {
                [$start, $end] = Monitor::heightRangeByRound($round);

                // `bool_or` is equivalent to `some` in PGSQL and is used here to
                // check if there is at least one block on the range.
                $query->addSelect(DB::raw(sprintf('bool_or(blocks.height BETWEEN %s AND %s) round_%s', $start, $end, $index)));
            });

        /**
         * @var Collection $results
         */
        $results = $query
            ->orderBy('balance', 'desc')
            ->orderBy('public_key', 'asc')
            ->groupBy('rounds.public_key')
            ->get();

        $results->each(function ($row) : void {
            (new WalletCache())->setPerformance($row->public_key, [
                $row->round_0,
                $row->round_1,
                $row->round_2,
                $row->round_3,
                $row->round_4,
            ]);
        });
    }
}
