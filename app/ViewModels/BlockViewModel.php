<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Actions\CacheNetworkHeight;
use App\Contracts\ViewModel;
use App\Models\Block;
use App\Services\ExchangeRate;
use App\Services\Timestamp;

final class BlockViewModel implements ViewModel
{
    use Concerns\Block\HasDelegate;
    use Concerns\Block\HasTransactions;
    use Concerns\Block\InteractsWithNeighbours;

    private Block $block;

    public function __construct(Block $block)
    {
        $this->block = $block;
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
