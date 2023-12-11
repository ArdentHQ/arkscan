<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Services\BigNumber;
use App\Services\Cache\Concerns\ManagesCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class Statistics
{
    use ManagesCache;

    public const STATS_TTL = 300;

    public static function transactionData(): array
    {
        return Cache::remember('transactions:stats', self::STATS_TTL, function () {
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

    public function setAddressHoldings(array $value): void
    {
        $this->put('address_holdings', $value);
    }

    /**
     * @return array<int, array{'grouped': int, 'count': int}>
     */
    public function getAddressHoldings(): array
    {
        return $this->get('address_holdings', []);
    }

    public function setGenesisAddress(array $value): void
    {
        $this->put('genesis_address', $value);
    }

    /**
     * @return array{'address': string, 'value': Carbon}
     */
    public function getGenesisAddress(): array
    {
        return $this->get('genesis_address', []);
    }

    public function setNewestAddress(array $value): void
    {
        $this->put('newest_address', $value);
    }

    /**
     * @return array{'address': string, 'value': Carbon}
     */
    public function getNewestAddress(): array
    {
        return $this->get('newest_address', []);
    }

    public function setMostTransactions(array $value): void
    {
        $this->put('most_transactions', $value);
    }

    /**
     * @return array{'address': string, 'value': int}
     */
    public function getMostTransactions(): array
    {
        return $this->get('most_transactions', []);
    }

    public function setLargestAddress(array $value): void
    {
        $this->put('largest_address', $value);
    }

    /**
     * @return array{'address': string, 'value': BigNumber}
     */
    public function getLargestAddress(): array
    {
        return $this->get('largest_address', []);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('statistics');
    }
}
