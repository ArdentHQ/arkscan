<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ForgingStats;
use App\Services\BigNumber;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class BlocksController
{
    public const STATS_TTL = 300;

    public function __invoke(): View
    {
        $data = $this->blockData();

        return view('app.blocks', [
            'forgedCount'   => $data['block_count'],
            'missedCount'   => $data['missed_count'],
            'totalRewards'  => BigNumber::new($data['total_rewards'])->toFloat(),
            'largestAmount' => BigNumber::new($data['largest_amount'])->toFloat(),
        ]);
    }

    private function blockData(): array
    {
        return Cache::remember('blocks:stats', self::STATS_TTL, function () {
            $timestamp = Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix();

            $data      = (array) DB::connection('explorer')
                ->table('blocks')
                ->selectRaw('COUNT(*) as block_count')
                ->selectRaw('SUM(reward) as total_rewards')
                ->selectRaw('MAX(total_amount) as largest_amount')
                ->where('timestamp', '>', ((int) $timestamp) * 1000)
                ->first();

            return [
                'block_count'    => $data['block_count'],
                'missed_count'   => ForgingStats::missed()->where('timestamp', '>', $timestamp)->count(),
                'total_rewards'  => $data['total_rewards'] ?? 0,
                'largest_amount' => $data['largest_amount'] ?? 0,
            ];
        });
    }
}
