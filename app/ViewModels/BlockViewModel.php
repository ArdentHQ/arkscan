<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Actions\CacheNetworkHeight;
use App\Contracts\ViewModel;
use App\Models\Block;
use App\Services\ExchangeRate;
use App\Services\Timestamp;
use App\ViewModels\Concerns\Block\HasDelegate;
use App\ViewModels\Concerns\Block\HasTransactions;
use App\ViewModels\Concerns\Block\InteractsWithNeighbours;
use Carbon\Carbon;

final class BlockViewModel implements ViewModel
{
    use HasDelegate;
    use HasTransactions;
    use InteractsWithNeighbours;

    public function __construct(private Block $block)
    {
    }

    public function url(): string
    {
        return route('block', $this->block);
    }

    public function id(): string
    {
        return $this->block->id;
    }

    public function timestamp(): string
    {
        return Timestamp::fromGenesisHuman($this->block->timestamp);
    }

    public function dateTime(): Carbon
    {
        return Timestamp::fromGenesis($this->block->timestamp);
    }

    public function height(): int
    {
        return $this->block->height->toNumber();
    }

    public function reward(): float
    {
        return $this->block->reward->toFloat();
    }

    public function rewardFiat(): string
    {
        return ExchangeRate::convert($this->block->reward->toFloat(), $this->block->timestamp);
    }

    public function confirmations(): int
    {
        return abs(CacheNetworkHeight::execute() - $this->block->height->toNumber());
    }
}
