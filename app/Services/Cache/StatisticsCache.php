<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class StatisticsCache implements Contract
{
    use ManagesCache;

    public const STATS_TTL = 300;

    public function getTransactionData(): array
    {
        return $this->remember('transactions', self::STATS_TTL, function () {
            $timestamp = Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix();
            $data      = (array) DB::connection('explorer')
                ->table('transactions')
                ->select([
                    'multipayment_volume' => function ($query) use ($timestamp) {
                        $query->selectRaw('SUM(MP_AMOUNT)')
                            ->from(function ($query) use ($timestamp) {
                                $query->selectRaw('(jsonb_array_elements(t.asset->\'payments\')->>\'amount\')::numeric as MP_AMOUNT')
                                    ->from('transactions', 't')
                                    ->where('type', 6)
                                    ->where('timestamp', '>', $timestamp);
                            }, 'b');
                    },
                ])
                ->selectRaw('COUNT(*) as transaction_count')
                ->selectRaw('SUM(amount) as volume')
                ->selectRaw('SUM(fee) as total_fees')
                ->selectRaw('AVG(fee) as average_fee')
                ->from('transactions')
                ->where('timestamp', '>', $timestamp)
                ->first();

            return [
                'transaction_count' => $data['transaction_count'],
                'volume'            => ($data['volume'] ?? 0) + ($data['multipayment_volume'] ?? 0),
                'total_fees'        => $data['total_fees'] ?? 0,
                'average_fee'       => $data['average_fee'] ?? 0,
            ];
        });
    }

    public function getMostUniqueVoters(): ?string
    {
        return $this->get('delegate/mostUniqueVoters');
    }

    public function setMostUniqueVoters(string $publicKey): void
    {
        $this->put('delegate/mostUniqueVoters', $publicKey);
    }

    public function getLeastUniqueVoters(): ?string
    {
        return $this->get('delegate/leastUniqueVoters');
    }

    public function setLeastUniqueVoters(string $publicKey): void
    {
        $this->put('delegate/leastUniqueVoters', $publicKey);
    }

    public function getOldestActiveDelegate(): ?string
    {
        return $this->get('delegate/oldestActiveDelegate');
    }

    public function setOldestActiveDelegate(string $publicKey): void
    {
        $this->put('delegate/oldestActiveDelegate', $publicKey);
    }

    public function getNewestActiveDelegate(): ?string
    {
        return $this->get('delegate/newestActiveDelegate');
    }

    public function setNewestActiveDelegate(string $publicKey): void
    {
        $this->put('delegate/newestActiveDelegate', $publicKey);
    }

    public function getMostBlocksForged(): ?string
    {
        return $this->get('delegate/mostBlocksForged');
    }

    public function setMostBlocksForged(string $publicKey): void
    {
        $this->put('delegate/mostBlocksForged', $publicKey);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('statistics');
    }
}
