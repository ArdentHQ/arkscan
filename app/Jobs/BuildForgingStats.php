<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Network;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Services\Monitor\MissedBlocksCalculator;
use App\Services\Timestamp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $height    = $this->getHeight();
        $timeRange = $this->getTimeRange($height);

        $forgingStats = MissedBlocksCalculator::calculateFromHeightGoingBack($height, $timeRange);
        foreach ($forgingStats as $timestamp => $statsForTimestamp) {
            ForgingStats::updateOrCreate(
                [
                    'timestamp' => $timestamp,
                ],
                [
                    'public_key' => $statsForTimestamp['publicKey'],
                    'forged'     => $statsForTimestamp['forged'],
                ],
            );
        }

        // clean up old stats entries
        $this->deleteMoreThan30DaysOldStats($this->getTimestampForHeight($height));
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
