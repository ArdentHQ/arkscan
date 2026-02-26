<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class MarketDataPriceStatistics
{
    public function __construct(
        public TimestampedValue $atl,
        public TimestampedValue $ath,
        public LowHighValue $daily,
        public LowHighValue $year,
    ) {
        //
    }

    public static function make(
        TimestampedValue $atl,
        TimestampedValue $ath,
        LowHighValue $daily,
        LowHighValue $year,
    ): self {
        return new self(
            $atl,
            $ath,
            $daily,
            $year,
        );
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

    public function dailyLow(): ?float
    {
        return $this->daily->low;
    }

    public function dailyHigh(): ?float
    {
        return $this->daily->high;
    }

    public function yearLow(): ?float
    {
        return $this->year->low;
    }

    public function yearHigh(): ?float
    {
        return $this->year->high;
    }

    public function toArray(): array
    {
        return [
            'atl'   => $this->atl->toArray(),
            'ath'   => $this->ath->toArray(),
            'daily' => $this->daily->toArray(),
            'year'  => $this->year->toArray(),
        ];
    }
}
