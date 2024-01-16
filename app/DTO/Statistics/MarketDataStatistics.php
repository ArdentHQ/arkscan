<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class MarketDataStatistics
{
    public static function make(
        MarketDataPriceStatistics $prices,
        MarketDataVolumeStatistics $volume,
        MarketDataRecordStatistics $caps,
    ): self
    {
        return new self(
            $prices,
            $volume,
            $caps,
        );
    }

    public function __construct(
        public MarketDataPriceStatistics $prices,
        public MarketDataVolumeStatistics $volume,
        public MarketDataRecordStatistics $caps,
    ) {
        //
    }
}
