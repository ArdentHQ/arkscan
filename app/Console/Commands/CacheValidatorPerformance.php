<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class CacheValidatorPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-performance';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache the past performance for each active validator in the current round.';

    public function handle(): void
    {
        $maxRounds = 3;

        $mostRecentRounds = Round::query()
            ->orderBy('round', 'DESC')
            ->limit($maxRounds)
            ->get();

        /**
         * @var Round $mostRecentRound
         */
        $mostRecentRound = $mostRecentRounds->first();

        $query = Wallet::query()
            ->select([
                'wallets.address',
                DB::raw('MAX(wallets.balance) as balance'),
            ])
            ->whereIn('wallets.address', $mostRecentRound->validators)
            ->join('blocks', 'blocks.proposer', '=', 'wallets.address');

        $actualNumberOfRounds = min($maxRounds, $mostRecentRounds->count());

        $mostRecentRounds
            ->slice(1)
            ->reverse()
            ->each(function ($round, int $index) use ($actualNumberOfRounds, $query) : void {
                [$start, $end] = Monitor::heightRangeByRound($round);

                // `bool_or` is equivalent to `some` in PGSQL and is used here to
                // check if there is at least one block on the range.
                $query->addSelect(DB::raw(sprintf('bool_or(blocks.number BETWEEN %s AND %s) round_%s', $start, $end, ($actualNumberOfRounds - $index - 1))));
            });

        /**
         * @var Collection $results
         */
        $results = $query
            ->orderBy('balance', 'desc')
            ->orderBy('wallets.address', 'asc')
            ->groupBy('wallets.address')
            ->get();

        $results->each(function ($row) : void {
            (new WalletCache())->setPerformance($row['address'], [
                $row['round_0'],
                $row['round_1'],
            ]);
        });
    }
}
