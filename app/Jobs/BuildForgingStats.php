<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Services\Monitor\MissedBlocksCalculator;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Scopes\OrderByTimestampScope;
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

    public const DEFAULT_RANGE_SECONDS = 24 * 60 * 60 * 30; // 30 days

    public function __construct(public int $height, public float $numberOfDays)
    {
    }

    public function handle(): void
    {
        $height      = $this->getHeight();
        $timeRange   = $this->getTimeRange($height);
        $startHeight = $this->getStartHeight($height, $timeRange);

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
                'address'       => $statsForTimestamp['address'],
                'forged'        => $statsForTimestamp['forged'],
            ];

            if (count($data) > 1000) {
                DB::transaction(fn () => ForgingStats::upsert($data, ['timestamp'], ['address', 'forged']), attempts: 2);

                $data = [];
            }
        }

        if (count($data) > 0) {
            DB::transaction(fn () => ForgingStats::upsert($data, ['timestamp'], ['address', 'forged']), attempts: 2);
        }

        // clean up old stats entries
        $this->deleteMoreThan30DaysOldStats($this->getTimestampForHeight($height));
    }

    private function getStartHeight(int $height, int $timeRangeInSeconds): int
    {
        $heightTimestamp = $this->getTimestampForHeight($height);

        $startBlock = Block::where('timestamp', '<=', ($heightTimestamp - $timeRangeInSeconds) * 1000)
            ->orderBy('height', 'desc')
            ->limit(1)
            ->first()
            ?->height
            ->toNumber() ?? 1;
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
            $lastForgingInfoTs = ForgingStats::orderBy('timestamp', 'DESC')
                ->limit(1)
                ->firstOr(function (): ForgingStats {
                    // by default if forging_stats table is not initialized we just build stats for past 30 days
                    $forgingStatsPast30Days            = new ForgingStats();
                    $forgingStatsPast30Days->timestamp = Timestamp::now()->getTimestamp() - self::DEFAULT_RANGE_SECONDS;

                    return $forgingStatsPast30Days;
                })
                ->timestamp;

            $timestampForHeight = $this->getTimestampForHeight($height);
            $timeRange          = $timestampForHeight - $lastForgingInfoTs;

            if ($timeRange < 0 || $timeRange > self::DEFAULT_RANGE_SECONDS) {
                return 0;
            }
        }

        return $timeRange;
    }

    private function deleteMoreThan30DaysOldStats(int $refTimestamp): void
    {
        ForgingStats::where('timestamp', '<', $refTimestamp - self::DEFAULT_RANGE_SECONDS)->delete();
    }

    private function getTimestampForHeight(int $height): int
    {
        return (int) (Block::where('height', $height)->firstOrFail()->timestamp / 1000);
    }
}
