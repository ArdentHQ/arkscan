<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use Livewire\Wireable;

final class MarketDataStatistics implements Wireable
{
    public function __construct(
        public MarketDataPriceStatistics $prices,
        public MarketDataVolumeStatistics $volume,
        public MarketDataRecordStatistics $caps,
    ) {
        //
    }

    public static function make(
        MarketDataPriceStatistics $prices,
        MarketDataVolumeStatistics $volume,
        MarketDataRecordStatistics $caps,
    ): self {
        return new self(
            $prices,
            $volume,
            $caps,
        );
    }

    public function toLivewire(): array
    {
        return [
            'prices' => $this->prices->toArray(),
            'volume' => $this->volume->toArray(),
            'caps'   => $this->caps->toArray(),
        ];
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public static function fromLivewire($value)
    {
        return new self(
            MarketDataPriceStatistics::make(
                TimestampedValue::fromArray($value['prices']['atl']),
                TimestampedValue::fromArray($value['prices']['ath']),
                LowHighValue::fromArray($value['prices']['daily']),
                LowHighValue::fromArray($value['prices']['year']),
            ),
            MarketDataVolumeStatistics::make(
                $value['volume']['today'],
                TimestampedValue::fromArray($value['volume']['atl']),
                TimestampedValue::fromArray($value['volume']['ath']),
            ),
            MarketDataRecordStatistics::make(
                $value['caps']['today'],
                TimestampedValue::fromArray($value['caps']['atl']),
                TimestampedValue::fromArray($value['caps']['ath']),
            ),
        );
    }
}
