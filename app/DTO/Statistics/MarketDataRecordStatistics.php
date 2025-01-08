<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Facades\Settings;
use App\Services\NumberFormatter;

final class MarketDataRecordStatistics
{
    public function __construct(public ?float $today)
    {
        //
    }

    public static function make(?float $today): self
    {
        return new self(
            $today,
        );
    }

    public function todayValue(): ?string
    {
        if ($this->today === null) {
            return null;
        }

        return $this->formatCurrency($this->today);
    }

    public function toArray(): array
    {
        return [
            'today' => $this->today,
        ];
    }

    /**
     * @param string|int|float $value
     */
    private function formatCurrency($value): string
    {
        return NumberFormatter::currencyForViews($value, Settings::currency());
    }
}
