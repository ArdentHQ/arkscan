<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

final class MarketDataVolumeStatistics
{
    public function __construct(
        public ?string $today,
        public ?TimestampedValue $atl,
        public ?TimestampedValue $ath,
    ) {
        //
    }

    public static function make(
        ?string $today,
        ?TimestampedValue $atl,
        ?TimestampedValue $ath,
    ): self {
        return new self(
            $today,
            $atl,
            $ath,
        );
    }

    public function todayVolumeValue(): string
    {
        if ($this->today === null) {
            return $this->zeroValue();
        }

        return $this->formatCurrency($this->today);
    }

    public function atlValue(): string
    {
        if ($this->atl === null) {
            return $this->zeroValue();
        }

        return $this->formatCurrency($this->atl->value);
    }

    public function atlDate(): ?string
    {
        if ($this->atl === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->atl->timestamp)->format(DateFormat::DATE);
    }

    public function athValue(): string
    {
        if ($this->ath === null) {
            return $this->zeroValue();
        }

        return $this->formatCurrency($this->ath->value);
    }

    public function athDate(): ?string
    {
        if ($this->ath === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->ath->timestamp)->format(DateFormat::DATE);
    }

    private function zeroValue(): string
    {
        return $this->formatCurrency(0);
    }

    /**
     * @param string|int|float $value
     */
    private function formatCurrency($value): string
    {
        return NumberFormatter::currencyForViews($value, 'USD');
    }
}
