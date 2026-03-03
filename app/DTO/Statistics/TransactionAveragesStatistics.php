<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class TransactionAveragesStatistics
{
    public int $count;

    public float $volume;

    public float $fees;

    public function __construct(array $data)
    {
        $this->count  = $data['count'];
        $this->volume = (float) $data['amount'];
        $this->fees   = (float) $data['fee'];
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'transactions'       => $this->count,
            'transaction_volume' => $this->volume,
            'transaction_fees'   => $this->fees,
        ];
    }
}
