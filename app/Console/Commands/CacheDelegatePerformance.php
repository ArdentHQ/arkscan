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
     * @var string|null
     */
    protected $description = 'Cache the past performance for each active delegate in the current round.';

    public function handle(): void
    {
        $maxRounds = 6;

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
                'wallets.public_key',
                DB::raw('MAX(wallets.balance) as balance'),
            ])
            ->whereIn('wallets.public_key', $mostRecentRound->validators)
            ->join('blocks', 'blocks.generator_public_key', '=', 'wallets.public_key');

        $actualNumberOfRounds = min($maxRounds, $mostRecentRounds->count());

        $mostRecentRounds
            ->slice(1)
            ->reverse()
            ->each(function ($round, int $index) use ($actualNumberOfRounds, $query) : void {
                [$start, $end] = Monitor::heightRangeByRound($round);

                // `bool_or` is equivalent to `some` in PGSQL and is used here to
                // check if there is at least one block on the range.
                $query->addSelect(DB::raw(sprintf('bool_or(blocks.height BETWEEN %s AND %s) round_%s', $start, $end, ($actualNumberOfRounds - $index - 1))));
            });

        /**
         * @var Collection $results
         */
        $results = $query
            ->orderBy('balance', 'desc')
            ->orderBy('wallets.public_key', 'asc')
            ->groupBy('wallets.public_key')
            ->get();

        $results->each(function ($row) : void {
            (new WalletCache())->setPerformance($row['public_key'], [
                $row['round_0'],
                $row['round_1'],
            ]);
        });
    }
}
