<?php

namespace App\DTO\Statistics;

use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

class MarketDataVolumeStatistics
{
    public static function make(
        ?string $today,
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
        public ?string $today,
        public array $atl,
        public array $ath,
    ) {
        //
    }

    private function zeroValue(): string
    {
        return $this->formatCurrency(0);
    }

    public function todayVolumeValue(): string
    {
        if ($this->today === null) {
            return $this->zeroValue();
        }

        return $this->formatCurrency($this->today['volume']);
    }

    public function todayValueValue(): string
    {
        if ($this->today === null) {
            return $this->zeroValue();
        }

        return $this->formatCurrency($this->today['value']);
    }

    public function todayDate(): ?string
    {
        return Carbon::createFromTimestamp($this->today['timestamp'])->format(DateFormat::DATE);
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
