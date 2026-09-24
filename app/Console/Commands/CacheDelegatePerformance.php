<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Models\Round;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

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
            ->select('rounds.public_key')
            ->selectRaw('MAX(rounds.balance) as balance')
            ->join('blocks', 'blocks.generator_public_key', '=', 'rounds.public_key');

        foreach ([0 => $round - 2, 1 => $round - 1] as $index => $pastRound) {
            [$start, $end] = Monitor::heightRangeByRound($pastRound);

            // `bool_or` is equivalent to `some` in PGSQL and is used here to
            // check if there is at least one block on the range.
            $alias = $index === 0 ? 'round_0' : 'round_1';

            $query->selectRaw("bool_or(blocks.height BETWEEN ? AND ?) AS {$alias}", [$start, $end]);
        }

        /**
         * @var Collection<int, Round> $results
         */
        $results = $query
            ->orderBy('balance', 'desc')
            ->orderBy('public_key', 'asc')
            ->groupBy('rounds.public_key')
            ->get();
        $results->each(function ($row) : void {
            (new WalletCache())->setPerformance($row['public_key'], [
                $row['round_0'],
                $row['round_1'],
            ]);
        });
    }
}
