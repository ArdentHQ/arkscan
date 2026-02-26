<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

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

    public function toArray(): array
    {
        return [
            'today' => $this->today,
            'atl'   => $this->atl->toArray(),
            'ath'   => $this->ath->toArray(),
        ];
    }
}
