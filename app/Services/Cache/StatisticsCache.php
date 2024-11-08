<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\BigNumber;
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
            $timestamp = Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix() * 1000;
            $data      = (array) DB::connection('explorer')
                ->table('transactions')
                ->selectRaw('COUNT(*) as transaction_count')
                ->selectRaw('SUM(amount) as volume')
                ->selectRaw('SUM(gas_price * COALESCE(receipts.gas_used, 0)) as total_fees')
                ->selectRaw('AVG(gas_price * COALESCE(receipts.gas_used, 0)) as average_fee')
                ->from('transactions')
                ->join('receipts', 'transactions.id', '=', 'receipts.id')
                ->where('timestamp', '>', $timestamp)
                ->first();

            return [
                'transaction_count' => $data['transaction_count'],
                'volume'            => $data['volume'] ?? 0,
                'total_fees'        => $data['total_fees'] ?? 0,
                'average_fee'       => $data['average_fee'] ?? 0,
            ];
        });
    }

    public function getMostUniqueVoters(): ?string
    {
        return $this->get('validator/mostUniqueVoters');
    }

    public function setMostUniqueVoters(string $address): void
    {
        $this->put('validator/mostUniqueVoters', $address);
    }

    public function getLeastUniqueVoters(): ?string
    {
        return $this->get('validator/leastUniqueVoters');
    }

    public function setLeastUniqueVoters(string $address): void
    {
        $this->put('validator/leastUniqueVoters', $address);
    }

    /**
     * @return array{'address': string, 'timestamp': int}
     */
    public function getOldestActiveValidator(): ?array
    {
        return $this->get('validator/oldestActiveValidator');
    }

    public function setOldestActiveValidator(string $address, int $timestamp): void
    {
        $this->put('validator/oldestActiveValidator', ['address' => $address, 'timestamp' => $timestamp]);
    }

    /**
     * @return array{'address': string, 'timestamp': int}
     */
    public function getNewestActiveValidator(): ?array
    {
        return $this->get('validator/newestActiveValidator');
    }

    public function setNewestActiveValidator(string $address, int $timestamp): void
    {
        $this->put('validator/newestActiveValidator', ['address' => $address, 'timestamp' => $timestamp]);
    }

    public function getMostBlocksForged(): ?string
    {
        return $this->get('validator/mostBlocksForged');
    }

    public function setMostBlocksForged(string $address): void
    {
        $this->put('validator/mostBlocksForged', $address);
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
     * @return ?array{'address': string, 'value': Carbon}
     */
    public function getGenesisAddress(): ?array
    {
        return $this->get('genesis_address', null);
    }

    public function setNewestAddress(array $value): void
    {
        $this->put('newest_address', $value);
    }

    /**
     * @return ?array{'address': string, 'timestamp': int, 'value': Carbon}
     */
    public function getNewestAddress(): ?array
    {
        return $this->get('newest_address', null);
    }

    public function setMostTransactions(array $value): void
    {
        $this->put('most_transactions', $value);
    }

    /**
     * @return ?array{'address': string, 'value': int}
     */
    public function getMostTransactions(): ?array
    {
        return $this->get('most_transactions', null);
    }

    public function setLargestAddress(array $value): void
    {
        $this->put('largest_address', $value);
    }

    /**
     * @return ?array{'address': string, 'value': BigNumber}
     */
    public function getLargestAddress(): ?array
    {
        return $this->get('largest_address', null);
    }

    public function setPriceRangeDaily(string $currency, float $low, float $high): void
    {
        $this->put(sprintf('prices/range_daily/%s', $currency), ['low' => $low, 'high' => $high]);
    }

    /**
     * @return ?array{'low': float, 'high': float}
     */
    public function getPriceRangeDaily(string $currency): ?array
    {
        return $this->get(sprintf('prices/range_daily/%s', $currency), null);
    }

    public function setPriceRange52(string $currency, float $low, float $high): void
    {
        $this->put(sprintf('prices/range_52w/%s', $currency), ['low' => $low, 'high' => $high]);
    }

    /**
     * @return ?array{'low': float, 'high': float}
     */
    public function getPriceRange52(string $currency): ?array
    {
        return $this->get(sprintf('prices/range_52w/%s', $currency), null);
    }

    public function setPriceAth(string $currency, int $timestamp, float $value): void
    {
        $this->put(sprintf('prices/ath/%s', $currency), ['timestamp' => $timestamp, 'value' => $value]);
    }

    /**
     * @return ?array{'timestamp': int, 'value': float}
     */
    public function getPriceAth(string $currency): ?array
    {
        return $this->get(sprintf('prices/ath/%s', $currency), null);
    }

    public function setPriceAtl(string $currency, int $timestamp, float $value): void
    {
        $this->put(sprintf('prices/atl/%s', $currency), ['timestamp' => $timestamp, 'value' => $value]);
    }

    /**
     * @return ?array{'timestamp': int, 'value': float}
     */
    public function getPriceAtl(string $currency): ?array
    {
        return $this->get(sprintf('prices/atl/%s', $currency), null);
    }

    public function setVolumeAth(string $currency, int $timestamp, float $value): void
    {
        $this->put(sprintf('volumes/ath/%s', $currency), ['timestamp' => $timestamp, 'value' => $value]);
    }

    /**
     * @return ?array{'timestamp': int, 'value': float}
     */
    public function getVolumeAth(string $currency): ?array
    {
        return $this->get(sprintf('volumes/ath/%s', $currency), null);
    }

    public function setVolumeAtl(string $currency, int $timestamp, float $value): void
    {
        $this->put(sprintf('volumes/atl/%s', $currency), ['timestamp' => $timestamp, 'value' => $value]);
    }

    /**
     * @return ?array{'timestamp': int, 'value': float}
     */
    public function getVolumeAtl(string $currency): ?array
    {
        return $this->get(sprintf('volumes/atl/%s', $currency), null);
    }

    public function setMarketCapAth(string $currency, int $timestamp, float $value): void
    {
        $this->put(sprintf('market_caps/ath/%s', $currency), ['timestamp' => $timestamp, 'value' => $value]);
    }

    /**
     * @return ?array{'timestamp': int, 'value': float}
     */
    public function getMarketCapAth(string $currency): ?array
    {
        return $this->get(sprintf('market_caps/ath/%s', $currency), null);
    }

    public function setMarketCapAtl(string $currency, int $timestamp, float $value): void
    {
        $this->put(sprintf('market_caps/atl/%s', $currency), ['timestamp' => $timestamp, 'value' => $value]);
    }

    /**
     * @return ?array{'timestamp': int, 'value': float}
     */
    public function getMarketCapAtl(string $currency): ?array
    {
        return $this->get(sprintf('market_caps/atl/%s', $currency), null);
    }

    public function setAnnualData(int $year, int $transactions, string $volume, string $fees, int $blocks): void
    {
        $this->put(sprintf('annual/%s', $year), [
            'year'         => $year,
            'transactions' => $transactions,
            'volume'       => $volume,
            'fees'         => $fees,
            'blocks'       => $blocks,
        ]);
    }

    /**
     * @return ?array{'year': int, 'transactions': int, 'volume': string, 'fees': float, 'blocks': int}
     */
    public function getAnnualData(int $year): ?array
    {
        return $this->get(sprintf('annual/%s', $year), null);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('statistics');
    }
}
