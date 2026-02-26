<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

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

    public function atlDate(): ?string
    {
        if ($this->atl->timestamp === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->atl->timestamp)->format(DateFormat::DATE);
    }

    public function athValue(): ?float
    {
        return $this->ath->value;
    }

    public function athDate(): ?string
    {
        if ($this->ath->timestamp === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->ath->timestamp)->format(DateFormat::DATE);
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
