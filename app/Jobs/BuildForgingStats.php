<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Network;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Scopes\OrderByTimestampScope;
use App\Services\Monitor\MissedBlocksCalculator;
use App\Services\Timestamp;
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

        $timestampHeights = Block::select('timestamp', 'height')
            ->withCasts(['height' => 'int'])
            ->withScope(OrderByTimestampScope::class)
            ->where('height', '>=', $startHeight - 1 - Network::delegateCount())
            ->where('height', '<=', $height + 1)
            ->get()
            ->sortByDesc('height');

        $forgingStats = MissedBlocksCalculator::calculateFromHeightGoingBack($startHeight, $height);

        $data = [];
        foreach ($forgingStats as $timestamp => $statsForTimestamp) {
            $missedHeight = null;
            if ($statsForTimestamp['forged'] === false) {
                /** @var array $missedBlock */
                $missedBlock  = $timestampHeights->firstWhere('timestamp', '<=', $timestamp);
                $missedHeight = $missedBlock['height'] + 1;
            }

            $data[] = [
                'missed_height' => $missedHeight,
                'timestamp'     => $timestamp,
                'public_key'    => $statsForTimestamp['publicKey'],
                'forged'        => $statsForTimestamp['forged'],
            ];
        }

        DB::transaction(fn () => ForgingStats::upsert($data, ['timestamp'], ['public_key', 'forged']), attempts: 2);

        // clean up old stats entries
        $this->deleteMoreThan30DaysOldStats($this->getTimestampForHeight($height));
    }

    private function getStartHeight(int $height, int $timeRangeInSeconds): int
    {
        $heightTimestamp = Block::where('height', $height)
            ->firstOrFail()
            ->timestamp;

        return Block::where('timestamp', '<=', $heightTimestamp - $timeRangeInSeconds)
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
        return Block::where('height', $height)->limit(1)->firstOrFail()->timestamp;
    }
}
