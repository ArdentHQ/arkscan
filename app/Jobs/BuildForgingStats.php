<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Network;
use App\Facades\Services\Monitor\MissedBlocksCalculator;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Scopes\OrderByTimestampScope;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

final class BuildForgingStats implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $height, public float $numberOfDays)
    {
    }

    public function handle(): void
    {
        $height      = $this->getHeight();
        $timeRange   = $this->getTimeRange($height);
        $startHeight = $this->getStartHeight($height, $timeRange);

        // TODO: remove
        ForgingStats::where('timestamp', '<', Carbon::now()->addDays(99))->delete();

        $forgingStats = MissedBlocksCalculator::calculateFromHeightGoingBack($startHeight, $height);

        $data = [];
        foreach ($forgingStats as $timestamp => $statsForTimestamp) {
            $missedHeight = null;
            if ($statsForTimestamp['forged'] === false) {
                /** @var array $missedBlock */
                $missedBlock = Block::select('height')
                    ->withCasts(['height' => 'int'])
                    ->withScope(OrderByTimestampScope::class)
                    ->where('timestamp', '<=', $timestamp * 1000)
                    ->limit(1)
                    ->first();

                $missedHeight = $missedBlock['height'] + 1;
            }

            $data[] = [
                'missed_height' => $missedHeight,
                'timestamp'     => $timestamp,
                'public_key'    => $statsForTimestamp['publicKey'],
                'forged'        => $statsForTimestamp['forged'],
            ];

            if (count($data) > 1000) {
                DB::transaction(fn () => ForgingStats::upsert($data, ['timestamp'], ['public_key', 'forged']), attempts: 2);

                $data = [];
            }
        }

        if (count($data) > 0) {
            DB::transaction(fn () => ForgingStats::upsert($data, ['timestamp'], ['public_key', 'forged']), attempts: 2);
        }

        // clean up old stats entries
        $this->deleteMoreThan30DaysOldStats($this->getTimestampForHeight($height));
    }

    private function getStartHeight(int $height, int $timeRangeInSeconds): int
    {
        $heightTimestamp = $this->getTimestampForHeight($height);

        return Block::where('timestamp', '<=', ($heightTimestamp - $timeRangeInSeconds) * 1000)
            ->orderBy('height', 'desc')
            ->limit(1)
            ->firstOrFail()
            ->height
            ->toNumber();
    }

    private function getHeight(): int
    {
        $height = $this->height;

        if ($height === 0) {
            $lastBlock = Block::orderBy('height', 'DESC')->limit(1)->firstOrFail();
            $height    = $lastBlock->height->toNumber();
        }

        return $height;
    }

    private function getTimeRange(int $height): int
    {
        $timeRange = intval($this->numberOfDays * 24 * 60 * 60);
        if ($timeRange === 0) {
            $lastForgingInfoTs = (int) ForgingStats::orderBy('timestamp', 'DESC')
                ->limit(1)
                ->firstOr(function (): ForgingStats {
                    // by default if forging_stats table is not initialized we just build stats for last hour
                    // (building stats is expensive, if we want data from last X days we need to ask for it explicitly)
                    $forgingStats1HourAgo            = new ForgingStats();
                    $forgingStats1HourAgo->timestamp = Timestamp::now()->getTimestamp() - 60 * 60;

                    return $forgingStats1HourAgo;
                })
                ->timestamp;

            $timestampForHeight = $this->getTimestampForHeight($height);
            // use a one-round margin to be sure we don't skip blocks from last forging info
            $timeRange = ($timestampForHeight - $lastForgingInfoTs) + (Network::delegateCount() * Network::blockTime());

            if ($timeRange < 0 || $timeRange > 24 * 60 * 60) {
                return 0;   // when time range is not specified, go back maximum 1 day (because
                // it is then supposed to be an incremental stats build)
            }
        }

        return $timeRange;
    }

    private function deleteMoreThan30DaysOldStats(int $refTimestamp): void
    {
        ForgingStats::where('timestamp', '<', $refTimestamp - 30 * 24 * 60 * 60)->delete();
    }

    private function getTimestampForHeight(int $height): int
    {
        return Block::where('height', $height)->firstOrFail()->timestamp;
    }
}
