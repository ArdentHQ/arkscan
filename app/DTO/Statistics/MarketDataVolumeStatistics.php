<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

final class MarketDataVolumeStatistics
{
    public function __construct(
        public ?string $today,
        public TimestampedValue $atl,
        public TimestampedValue $ath,
    ) {
        //
    }

    public static function make(
        ?string $today,
        TimestampedValue $atl,
        TimestampedValue $ath,
    ): self {
        return new self(
            $today,
            $atl,
            $ath,
        );
    }

    public function todayVolumeValue(): float
    {
        return $this->today !== null ? (float) $this->today : 0;
    }

    public function atlValue(): float
    {
        return $this->atl->value ?? 0.0;
    }

    public function atlDate(): ?string
    {
        if ($this->atl->timestamp === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->atl->timestamp)->format(DateFormat::DATE);
    }

    public function athValue(): float
    {
        return $this->ath->value ?? 0.0;
    }

    public function athDate(): ?string
    {
        if ($this->ath->timestamp === null) {
            return null;
        }

        return Carbon::createFromTimestamp($this->ath->timestamp)->format(DateFormat::DATE);
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
