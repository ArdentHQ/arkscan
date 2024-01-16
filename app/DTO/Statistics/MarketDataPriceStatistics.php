<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

final class MarketDataPriceStatistics
{
    public static function make(
        ?array $atl,
        ?array $ath,
        ?array $daily,
        ?array $year,
    ): self
    {
        return new self(
            $atl,
            $ath,
            $daily,
            $year,
        );
    }

    public function __construct(
        public ?array $atl,
        public ?array $ath,
        public ?array $daily,
        public ?array $year,
    ) {
        //
    }

    public function atlValue(): ?string
    {
        if ($this->atl === null) {
            return null;
        }

        return $this->formatCurrency($this->atl['value']);
    }

    public function atlDate(): ?string
    {
        if ($this->atl === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->atl['timestamp'])->format(DateFormat::DATE);
    }

    public function athValue(): ?string
    {
        if ($this->ath === null) {
            return null;
        }

        return $this->formatCurrency($this->ath['value']);
    }

    public function athDate(): ?string
    {
        if ($this->ath === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->ath['timestamp'])->format(DateFormat::DATE);
    }

    public function dailyLow(): ?string
    {
        if ($this->daily === null) {
            return null;
        }

        return $this->formatCurrency($this->daily['low']);
    }

    public function dailyHigh(): ?string
    {
        if ($this->daily === null) {
            return null;
        }

        return $this->formatCurrency($this->daily['high']);
    }

    public function yearLow(): ?string
    {
        if ($this->year === null) {
            return null;
        }

        return $this->formatCurrency($this->year['low']);
    }

    public function yearHigh(): ?string
    {
        if ($this->year === null) {
            return null;
        }

        return $this->formatCurrency($this->year['high']);
    }

    /**
     * @param string|int|float $value
     */
    private function formatCurrency($value): string
    {
        return NumberFormatter::currencyWithDecimals($value, 'USD', 2);
    }
}
