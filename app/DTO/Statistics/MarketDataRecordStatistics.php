<?php

namespace App\DTO\Statistics;

use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

class MarketDataRecordStatistics
{
    public static function make(
        ?float $today,
        ?array $atl,
        ?array $ath,
    ): self
    {
        return new self(
            $today,
            $atl,
            $ath,
        );
    }

    public function __construct(
        public ?float $today,
        public ?array $atl,
        public ?array $ath,
    ) {
        //
    }

    public function todayValueValue(): string
    {
        return $this->formatCurrency($this->today);
    }

    public function atlValue(): string
    {
        return $this->formatCurrency($this->atl['value']);
    }

    public function atlDate(): ?string
    {
        return Carbon::createFromTimestamp($this->atl['timestamp'])->format(DateFormat::DATE);
    }

    public function athValue(): string
    {
        return $this->formatCurrency($this->ath['value']);
    }

    public function athDate(): ?string
    {
        return Carbon::createFromTimestamp($this->ath['timestamp'])->format(DateFormat::DATE);
    }

    private function formatCurrency($value): string
    {
        return NumberFormatter::currencyForViews($value, 'USD');
    }
}
