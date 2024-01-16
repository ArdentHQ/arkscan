<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Facades\Network;
use App\Services\NumberFormatter;

final class TransactionAveragesStatistics
{
    public int $count;
    public string $volume;
    public string $fees;

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function __construct(array $data)
    {
        $this->count  = $data['count'];
        $this->volume = $this->formatCurrency($data['amount']);
        $this->fees   = $this->formatCurrency($data['fee']);
    }

    public function toArray(): array
    {
        return [
            'transactions'       => $this->count,
            'transaction_volume' => $this->volume,
            'transaction_fees'   => $this->fees,
        ];
    }

    /**
     * @param string|int|float $value
     */
    private function formatCurrency($value): string
    {
        return NumberFormatter::currency($value, Network::currency());
    }
}
