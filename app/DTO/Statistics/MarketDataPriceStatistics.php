<?php

namespace App\DTO\Statistics;

use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

class MarketDataPriceStatistics
{
    public static function make(
        array $atl,
        array $ath,
        array $daily,
        array $year,
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
        public array $atl,
        public array $ath,
        public array $daily,
        public array $year,
    ) {
        //
    }

    public function atlValue(): string
    {
        return $this->formatCurrency($this->atl['value']).'asd';
    }

    public function atlDate(): ?string
    {
        return Carbon::createFromTimestamp($this->atl['timestamp'])->format(DateFormat::DATE);
    }

    public function athValue(): string
    {
        return $this->formatCurrency($this->ath['value']).'asd';
    }

    public function athDate(): ?string
    {
        return Carbon::createFromTimestamp($this->ath['timestamp'])->format(DateFormat::DATE);
    }

    public function dailyLow(): string
    {
        return $this->formatCurrency($this->daily['low']).'asd';
    }

    public function dailyHigh(): string
    {
        return $this->formatCurrency($this->daily['high']).'asd';
    }

    public function yearLow(): string
    {
        return $this->formatCurrency($this->year['low']).'asd';
    }

    public function yearHigh(): string
    {
        return $this->formatCurrency($this->year['high']).'asd';
    }

    private function formatCurrency($value): string
    {
        return NumberFormatter::currencyWithDecimals($value, 'USD', 2);
    }
}
