<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Services\NumberFormatter;
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

    public function atlValue(): ?string
    {
        if ($this->atl->value === null) {
            return null;
        }

        return $this->formatCurrency($this->atl->value);
    }

    public function atlDate(): ?string
    {
        if ($this->atl->timestamp === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->atl->timestamp)->format(DateFormat::DATE);
    }

    public function athValue(): ?string
    {
        if ($this->ath->value === null) {
            return null;
        }

        return $this->formatCurrency($this->ath->value);
    }

    public function athDate(): ?string
    {
        if ($this->ath->timestamp === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->ath->timestamp)->format(DateFormat::DATE);
    }

    public function dailyLow(): ?string
    {
        if ($this->daily->low === null) {
            return null;
        }

        return $this->formatCurrency($this->daily->low);
    }

    public function dailyHigh(): ?string
    {
        if ($this->daily->high === null) {
            return null;
        }

        return $this->formatCurrency($this->daily->high);
    }

    public function yearLow(): ?string
    {
        if ($this->year->low === null) {
            return null;
        }

        return $this->formatCurrency($this->year->low);
    }

    public function yearHigh(): ?string
    {
        if ($this->year->high === null) {
            return null;
        }

        return $this->formatCurrency($this->year->high);
    }

    /**
     * @param string|int|float $value
     */
    private function formatCurrency($value): string
    {
        return NumberFormatter::currencyWithDecimals($value, 'USD', 2);
    }
}
