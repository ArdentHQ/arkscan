<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class MarketDataRecordStatistics
{
    public function __construct(
        public ?float $today,
        public TimestampedValue $atl,
        public TimestampedValue $ath,
    ) {
        //
    }

    public static function make(
        ?float $today,
        TimestampedValue $atl,
        TimestampedValue $ath,
    ): self {
        return new self(
            $today,
            $atl,
            $ath,
        );
    }

    public function todayValueValue(): ?float
    {
        return $this->today;
    }

    public function atlValue(): ?float
    {
        return $this->atl->value;
    }

    public function atlTimestamp(): ?int
    {
        return $this->atl->timestamp;
    }

    public function athValue(): ?float
    {
        return $this->ath->value;
    }

    public function athTimestamp(): ?int
    {
        return $this->ath->timestamp;
    }

    public function toArray(): array
    {
        return [
            'today' => $this->today,
            'atl'   => $this->atl->toArray(),
            'ath'   => $this->ath->toArray(),
        ];
    }
}
