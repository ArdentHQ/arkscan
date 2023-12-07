<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use App\Services\Cache\Concerns\ManagesChart;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class TransactionCache implements Contract
{
    use ManagesCache;
    use ManagesChart;

    public function getHistorical(string $period): array
    {
        return $this->get(sprintf('historical/%s', $period), []);
    }

    public function setHistorical(string $period, Collection $data): void
    {
        $this->put(sprintf('historical/%s', $period), $this->chartjs($data));
    }

    public function getHistoricalByType(string $type): int
    {
        return (int) $this->get(sprintf('type/historical/%s', $type), 0);
    }

    public function setHistoricalByType(string $type, int $count): void
    {
        $this->put(sprintf('type/historical/%s', $type), $count);
    }

    public function getHistoricalAverages(): array
    {
        return $this->get('averages', [
            'count'  => 0,
            'volume' => 0,
            'fees'   => 0,
        ]);
    }

    public function setHistoricalAverages(array $averages): void
    {
        $this->put('averages', $averages);
    }

    public function getLargestIdByAmount(): ?string
    {
        return $this->get('largest/amount');
    }

    public function setLargestIdByAmount(string $id): void
    {
        $this->put('largest/amount', $id);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('transaction');
    }
}
